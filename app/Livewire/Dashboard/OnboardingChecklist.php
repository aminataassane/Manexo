<?php

namespace App\Livewire\Dashboard;

use App\Livewire\Dashboard\Concerns\WithOrganization;
use App\Services\OnboardingService;
use Livewire\Attributes\Computed;
use Livewire\Component;

class OnboardingChecklist extends Component
{
    use WithOrganization;

    #[Computed]
    public function steps(): array
    {
        $org = $this->organization;
        if (! $org) {
            return [];
        }

        return OnboardingService::state($org);
    }

    #[Computed]
    public function completedCount(): int
    {
        return collect($this->steps)->where('completed', true)->count();
    }

    #[Computed]
    public function totalSteps(): int
    {
        return count(OnboardingService::STEPS);
    }

    #[Computed]
    public function progressPercent(): int
    {
        if ($this->totalSteps === 0) {
            return 0;
        }

        return (int) round(($this->completedCount / $this->totalSteps) * 100);
    }

    #[Computed]
    public function isComplete(): bool
    {
        return $this->completedCount === $this->totalSteps;
    }

    #[Computed]
    public function shouldShow(): bool
    {
        $org = $this->organization;
        if (! $org) {
            return false;
        }

        return ! OnboardingService::isDismissedByOrg($org) && ! $this->isComplete;
    }

    public function dismiss(): void
    {
        $org = $this->organization;
        if ($org) {
            OnboardingService::dismiss($org);
        }
    }

    public function markDone(string $step): void
    {
        $org = $this->organization;
        if ($org && in_array($step, OnboardingService::STEPS, true)) {
            OnboardingService::markStepDone($org, $step);
            unset($this->steps, $this->completedCount, $this->progressPercent, $this->isComplete, $this->shouldShow);
        }
    }

    public function render()
    {
        return view('livewire.dashboard.onboarding-checklist');
    }
}
