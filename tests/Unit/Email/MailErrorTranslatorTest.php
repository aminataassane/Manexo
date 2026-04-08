<?php

namespace Tests\Unit\Email;

use App\Services\Email\MailErrorTranslator;
use PHPUnit\Framework\TestCase;

class MailErrorTranslatorTest extends TestCase
{
    public function test_connection_refused_message(): void
    {
        $msg = MailErrorTranslator::translate('Connection refused', 'IMAP', 993);

        $this->assertStringContainsString('Connexion refusée', $msg);
        $this->assertStringContainsString('993', $msg);
    }

    public function test_timeout_message(): void
    {
        $msg = MailErrorTranslator::translate('Connection timed out', 'SMTP', 587);

        $this->assertStringContainsString('délai', mb_strtolower($msg));
    }

    public function test_authentication_failed_message(): void
    {
        $msg = MailErrorTranslator::translate('Authentication failed', 'IMAP', 993);

        $this->assertStringContainsString('Identifiant', $msg);
    }

    public function test_unknown_host_message(): void
    {
        $msg = MailErrorTranslator::translate('getaddrinfo failed: Name or service not known', 'IMAP', 0);

        $this->assertStringContainsString('introuvable', mb_strtolower($msg));
    }

    public function test_ssl_tls_generic_message(): void
    {
        $msg = MailErrorTranslator::translate('SSL handshake failed', 'SMTP', 465);

        $this->assertStringContainsString('SSL', $msg);
    }

    public function test_fallback_includes_original_error(): void
    {
        $msg = MailErrorTranslator::translate('Some obscure XYZ error code', 'IMAP', 143);

        $this->assertStringContainsString('obscure', mb_strtolower($msg));
    }
}
