<?php

namespace Tests\Unit\Email;

use App\Services\Email\EmailParser;
use PHPUnit\Framework\TestCase;

class EmailParserSanitizeHtmlTest extends TestCase
{
    public function test_sanitize_html_extracts_body_only(): void
    {
        $html = '<html><head><title>X</title></head><body><p>Hello</p></body></html>';
        $out = EmailParser::sanitizeHtml($html);

        $this->assertStringContainsString('Hello', $out);
        $this->assertStringNotContainsString('<html', $out);
    }

    public function test_sanitize_html_removes_script_tags(): void
    {
        $html = '<div><script>alert(1)</script>Safe</div>';
        $out = EmailParser::sanitizeHtml($html);

        $this->assertStringContainsString('Safe', $out);
        $this->assertStringNotContainsString('script', strtolower($out));
    }

    public function test_sanitize_html_preserves_link_as_text_and_url(): void
    {
        $html = '<a href="https://example.com/path">Click here</a>';
        $out = EmailParser::sanitizeHtml($html);

        $this->assertStringContainsString('Click here', $out);
        $this->assertStringContainsString('https://example.com/path', $out);
    }

    public function test_sanitize_html_converts_br_to_newlines(): void
    {
        $html = 'Line1<br/>Line2';
        $out = EmailParser::sanitizeHtml($html);

        $this->assertStringContainsString('Line1', $out);
        $this->assertStringContainsString('Line2', $out);
    }
}
