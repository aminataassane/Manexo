<?php

namespace Tests\Unit\Email;

use App\Services\Email\EmailParser;
use PHPUnit\Framework\TestCase;

class EmailParserDecodeMimeHeaderTest extends TestCase
{
    public function test_decode_mime_header_returns_null_for_null(): void
    {
        $this->assertNull(EmailParser::decodeMimeHeader(null));
    }

    public function test_decode_mime_header_returns_empty_string_for_empty(): void
    {
        $this->assertSame('', EmailParser::decodeMimeHeader(''));
    }

    public function test_decode_mime_header_plain_ascii_unchanged(): void
    {
        $this->assertSame('Simple Subject', EmailParser::decodeMimeHeader('Simple Subject'));
    }

    public function test_decode_mime_header_decodes_utf8_base64_word(): void
    {
        // =?UTF-8?B?SMOpbGxv?= → "Héllo" when iconv/mb available
        $encoded = '=?UTF-8?B?'.base64_encode('Héllo').'?=';
        $decoded = EmailParser::decodeMimeHeader($encoded);

        $this->assertIsString($decoded);
        $this->assertNotSame($encoded, $decoded);
        $this->assertStringContainsString('H', (string) $decoded);
    }
}
