<?php

namespace App\Services;

use App\Models\Organization;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * Calculates deadlines and elapsed time accounting for business hours.
 *
 * Business hours are configured per organization in settings:
 * settings.sla.business_hours = [
 *     'enabled' => true,
 *     'timezone' => 'Europe/Paris',
 *     'start' => '09:00',
 *     'end' => '18:00',
 *     'working_days' => [1, 2, 3, 4, 5], // Monday=1 to Sunday=7 (ISO)
 * ]
 */
class BusinessHoursService
{
    /**
     * Add N business minutes to a starting datetime, returning the deadline.
     */
    public static function addBusinessMinutes(Carbon $start, int $minutes, int $organizationId): Carbon
    {
        $config = self::getConfig($organizationId);

        if (! $config['enabled']) {
            return $start->copy()->addMinutes($minutes);
        }

        $tz = $config['timezone'];
        $startHour = (int) substr($config['start'], 0, 2);
        $startMinute = (int) substr($config['start'], 3, 2);
        $endHour = (int) substr($config['end'], 0, 2);
        $endMinute = (int) substr($config['end'], 3, 2);
        $workingDays = $config['working_days'];

        $dailyMinutes = ($endHour * 60 + $endMinute) - ($startHour * 60 + $startMinute);
        if ($dailyMinutes <= 0) {
            // Invalid config — fallback to simple calculation
            return $start->copy()->addMinutes($minutes);
        }

        $cursor = $start->copy()->setTimezone($tz);
        $remaining = $minutes;

        // If we start outside business hours, move to next business start
        $cursor = self::moveToBusinessHours($cursor, $startHour, $startMinute, $endHour, $endMinute, $workingDays);

        while ($remaining > 0) {
            // Minutes left today
            $endOfDay = $cursor->copy()->setTime($endHour, $endMinute, 0);
            $minutesLeftToday = (int) $cursor->diffInMinutes($endOfDay, false);

            if ($minutesLeftToday <= 0) {
                // Move to next business day
                $cursor = self::nextBusinessDay($cursor, $startHour, $startMinute, $workingDays);

                continue;
            }

            if ($remaining <= $minutesLeftToday) {
                $cursor = $cursor->addMinutes($remaining);
                $remaining = 0;
            } else {
                $remaining -= $minutesLeftToday;
                $cursor = self::nextBusinessDay($cursor, $startHour, $startMinute, $workingDays);
            }
        }

        return $cursor->setTimezone(config('app.timezone', 'UTC'));
    }

    /**
     * Calculate elapsed business minutes between two datetimes.
     */
    public static function elapsedBusinessMinutes(Carbon $start, Carbon $end, int $organizationId): int
    {
        $config = self::getConfig($organizationId);

        if (! $config['enabled']) {
            return (int) $start->diffInMinutes($end);
        }

        $tz = $config['timezone'];
        $startHour = (int) substr($config['start'], 0, 2);
        $startMinute = (int) substr($config['start'], 3, 2);
        $endHour = (int) substr($config['end'], 0, 2);
        $endMinute = (int) substr($config['end'], 3, 2);
        $workingDays = $config['working_days'];

        $dailyMinutes = ($endHour * 60 + $endMinute) - ($startHour * 60 + $startMinute);
        if ($dailyMinutes <= 0) {
            return (int) $start->diffInMinutes($end);
        }

        $cursor = $start->copy()->setTimezone($tz);
        $target = $end->copy()->setTimezone($tz);
        $total = 0;

        $cursor = self::moveToBusinessHours($cursor, $startHour, $startMinute, $endHour, $endMinute, $workingDays);

        while ($cursor->lt($target)) {
            if (! in_array($cursor->dayOfWeekIso, $workingDays, true)) {
                $cursor = self::nextBusinessDay($cursor, $startHour, $startMinute, $workingDays);

                continue;
            }

            $endOfDay = $cursor->copy()->setTime($endHour, $endMinute, 0);
            $dayEnd = $endOfDay->lt($target) ? $endOfDay : $target;
            $minutesLeftToday = max(0, (int) $cursor->diffInMinutes($dayEnd, false));

            $total += $minutesLeftToday;

            if ($endOfDay->lt($target)) {
                $cursor = self::nextBusinessDay($cursor, $startHour, $startMinute, $workingDays);
            } else {
                break;
            }
        }

        return $total;
    }

    /**
     * Get business hours configuration for an organization.
     *
     * @return array{enabled: bool, timezone: string, start: string, end: string, working_days: int[]}
     */
    public static function getConfig(int $organizationId): array
    {
        return Cache::remember(
            "business_hours_config:{$organizationId}",
            300,
            function () use ($organizationId) {
                $org = Organization::find($organizationId);
                $settings = is_array($org?->settings) ? $org->settings : [];
                $sla = $settings['sla'] ?? [];
                $bh = $sla['business_hours'] ?? [];

                return [
                    'enabled' => (bool) ($bh['enabled'] ?? false),
                    'timezone' => $bh['timezone'] ?? config('app.timezone', 'UTC'),
                    'start' => $bh['start'] ?? '09:00',
                    'end' => $bh['end'] ?? '18:00',
                    'working_days' => $bh['working_days'] ?? [1, 2, 3, 4, 5],
                ];
            }
        );
    }

    /**
     * Move cursor to next valid business time (if currently outside).
     */
    private static function moveToBusinessHours(Carbon $cursor, int $startH, int $startM, int $endH, int $endM, array $workingDays): Carbon
    {
        // If not a working day, jump to next
        if (! in_array($cursor->dayOfWeekIso, $workingDays, true)) {
            return self::nextBusinessDay($cursor, $startH, $startM, $workingDays);
        }

        $startOfDay = $cursor->copy()->setTime($startH, $startM, 0);
        $endOfDay = $cursor->copy()->setTime($endH, $endM, 0);

        // Before business hours → move to start
        if ($cursor->lt($startOfDay)) {
            return $startOfDay;
        }

        // After business hours → move to next day
        if ($cursor->gte($endOfDay)) {
            return self::nextBusinessDay($cursor, $startH, $startM, $workingDays);
        }

        return $cursor;
    }

    /**
     * Move to the start of the next business day.
     */
    private static function nextBusinessDay(Carbon $cursor, int $startH, int $startM, array $workingDays): Carbon
    {
        $next = $cursor->copy()->addDay()->setTime($startH, $startM, 0);
        $maxAttempts = 10; // safety: avoid infinite loop on bad config
        while (! in_array($next->dayOfWeekIso, $workingDays, true) && $maxAttempts-- > 0) {
            $next->addDay();
        }

        return $next;
    }
}
