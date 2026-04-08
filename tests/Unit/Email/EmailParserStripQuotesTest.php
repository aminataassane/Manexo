<?php

namespace Tests\Unit\Email;

use App\Services\Email\EmailParser;
use PHPUnit\Framework\TestCase;

class EmailParserStripQuotesTest extends TestCase
{
    public function test_strip_email_reply_quotes_removes_english_on_wrote_block(): void
    {
        $text = "My reply only.\n\nOn Tuesday someone wrote:\n> quoted line\n> more";
        $out = EmailParser::stripEmailReplyQuotes($text);

        $this->assertStringContainsString('My reply only', $out);
        $this->assertStringNotContainsString('quoted line', $out);
    }

    public function test_strip_email_reply_quotes_removes_french_ecrit_pattern(): void
    {
        $text = "Réponse courte.\n\nLe support a écrit :\n> ancien message";
        $out = EmailParser::stripEmailReplyQuotes($text);

        $this->assertStringContainsString('Réponse courte', $out);
        $this->assertStringNotContainsString('ancien message', $out);
    }

    public function test_strip_plain_text_angle_quote_lines_stops_at_first_quoted_block_after_blank(): void
    {
        $text = "Visible line one\n\n> quoted after blank";
        $out = EmailParser::stripPlainTextAngleQuoteLines($text);

        $this->assertSame('Visible line one', trim($out));
    }

    public function test_strip_plain_text_angle_quote_lines_keeps_inline_greater_than(): void
    {
        $text = 'Price > 100 is ok';
        $out = EmailParser::stripPlainTextAngleQuoteLines($text);

        $this->assertSame($text, $out);
    }

    public function test_strip_email_reply_quotes_normalizes_crlf(): void
    {
        $text = "A\r\n\r\nB";
        $out = EmailParser::stripEmailReplyQuotes($text);

        $this->assertStringNotContainsString("\r", $out);
    }
}
