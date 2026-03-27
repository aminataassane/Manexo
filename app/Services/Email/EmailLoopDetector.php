<?php

namespace App\Services\Email;

class EmailLoopDetector
{
    /** Headers and patterns that indicate auto-generated/loop emails. */
    private const AUTO_HEADERS = [
        'Auto-Submitted',
        'X-Auto-Response-Suppress',
        'X-Autoreply',
        'X-Autorespond',
    ];

    private const NOREPLY_PATTERNS = [
        'noreply@',
        'no-reply@',
        'do-not-reply@',
        'donotreply@',
        'mailer-daemon@',
        'postmaster@',
        'notifications@',
        'bounce@',
        'bounces@',
        'newsletter@',
        'newsletters@',
        'marketing@',
        'promo@',
        'promos@',
        'promotions@',
        'news@',
        'info@',
        'offers@',
        'deals@',
        'campaign@',
        'campaigns@',
        'digest@',
        'updates@',
        'alert@',
        'alerts@',
    ];

    /** Headers that indicate bulk/marketing emails */
    private const MARKETING_HEADERS = [
        'List-Unsubscribe',
        'List-Unsubscribe-Post',
        'List-Id',
        'X-Mailchimp-Id',
        'X-MC-User',
        'X-Mailer-LID',
        'X-Campaign',
        'X-campaignid',
        'X-SG-EID',
        'X-SG-ID',
        'X-Sendinblue-Cluster',
        'X-sib-id',
        'X-PM-Message-Id',
        'X-HubSpot-Correlation-Id',
        'X-Mailgun-Tag',
        'X-Mandrill-User',
        'X-Report-Abuse',
        'Feedback-ID',
        'X-CSA-Complaints',
        'X-SFMC-Stack',
    ];

    /**
     * Determine if an email should be skipped (loop / auto-reply / bounce).
     *
     * @param  array<string, string|string[]>  $headers  Normalized header name → value(s)
     * @return string|null Reason string if should skip, null otherwise
     */
    public static function shouldSkip(array $headers, string $fromEmail): ?string
    {
        $fromEmail = strtolower(trim($fromEmail));

        // Our own emails: never process them back
        if (self::getHeader($headers, 'X-Conversation-Ref') || self::getHeader($headers, 'X-Manexo-Ticket-Id')) {
            return 'own_email';
        }

        // Auto-Submitted header (RFC 3834)
        $autoSubmitted = strtolower((string) self::getHeader($headers, 'Auto-Submitted'));
        if ($autoSubmitted !== '' && $autoSubmitted !== 'no') {
            return 'auto_submitted';
        }

        // X-Auto-Response-Suppress (Microsoft)
        if (self::getHeader($headers, 'X-Auto-Response-Suppress')) {
            return 'auto_response_suppress';
        }

        // X-Autoreply / X-Autorespond
        if (self::getHeader($headers, 'X-Autoreply') || self::getHeader($headers, 'X-Autorespond')) {
            return 'auto_reply_header';
        }

        // Precedence: bulk, junk, list
        $precedence = strtolower((string) self::getHeader($headers, 'Precedence'));
        if (in_array($precedence, ['bulk', 'junk', 'list'], true)) {
            return 'precedence_'.$precedence;
        }

        // Empty Return-Path (bounce)
        $returnPath = self::getHeader($headers, 'Return-Path');
        if ($returnPath !== null && (trim($returnPath) === '' || trim($returnPath) === '<>')) {
            return 'empty_return_path';
        }

        // Noreply / marketing address patterns
        foreach (self::NOREPLY_PATTERNS as $pattern) {
            if (str_starts_with($fromEmail, $pattern)) {
                return 'noreply_address';
            }
        }

        // Marketing / newsletter / bulk email headers
        foreach (self::MARKETING_HEADERS as $header) {
            if (self::getHeader($headers, $header)) {
                return 'marketing_header_'.strtolower($header);
            }
        }

        // Check X-Mailer for known bulk/marketing platforms
        $xMailer = strtolower((string) self::getHeader($headers, 'X-Mailer'));
        if ($xMailer !== '') {
            $bulkMailers = ['mailchimp', 'sendinblue', 'brevo', 'sendgrid', 'hubspot', 'mailgun', 'mailjet', 'constant contact', 'campaign monitor', 'activecampaign', 'klaviyo', 'drip', 'convertkit', 'aweber', 'getresponse'];
            foreach ($bulkMailers as $mailer) {
                if (str_contains($xMailer, $mailer)) {
                    return 'bulk_mailer_'.$mailer;
                }
            }
        }

        return null;
    }

    private static function getHeader(array $headers, string $name): ?string
    {
        // Try case-insensitive match
        $nameLower = strtolower($name);
        foreach ($headers as $key => $value) {
            if (strtolower($key) === $nameLower) {
                return is_array($value) ? ($value[0] ?? null) : $value;
            }
        }

        return null;
    }
}
