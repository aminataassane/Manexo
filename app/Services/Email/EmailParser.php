<?php

namespace App\Services\Email;

use Webklex\PHPIMAP\Message;

class ParsedEmail
{
    public function __construct(
        public readonly ?string $messageId,
        public readonly string $fromEmail,
        public readonly ?string $fromName,
        public readonly ?string $subject,
        public readonly ?string $textBody,
        public readonly ?string $htmlBody,
        public readonly ?string $inReplyTo,
        public readonly array $references,
        public readonly array $headers,
        public readonly array $attachments,
        public readonly ?int $uid,
        /** @var array<int, array{email: string, name: string|null}> */
        public readonly array $cc = [],
    ) {}

    public function getCleanBody(): string
    {
        $raw = '';
        if ($this->textBody !== null && trim($this->textBody) !== '') {
            $raw = trim($this->textBody);
        } elseif ($this->htmlBody !== null && trim($this->htmlBody) !== '') {
            $raw = trim(EmailParser::sanitizeHtml($this->htmlBody));
        }

        if ($raw === '') {
            return '';
        }

        return EmailParser::stripEmailReplyQuotes($raw);
    }
}

class EmailParser
{
    public static function parse(Message $message): ParsedEmail
    {
        $headers = self::extractHeaders($message);

        $fromAddress = $message->getFrom()[0] ?? null;
        $fromEmail = $fromAddress ? strtolower(trim($fromAddress->mail)) : '';
        $fromName = $fromAddress ? trim($fromAddress->personal ?? '') : null;

        $inReplyTo = self::getHeaderValue($headers, 'In-Reply-To');
        $referencesRaw = self::getHeaderValue($headers, 'References') ?? '';
        $references = array_filter(array_map('trim', preg_split('/\s+/', $referencesRaw)));

        $attachments = [];
        foreach ($message->getAttachments() as $attachment) {
            $attachments[] = [
                'name' => $attachment->getName() ?? 'attachment',
                'mime' => $attachment->getMimeType() ?? 'application/octet-stream',
                'size' => $attachment->getSize() ?? 0,
                'content' => $attachment->getContent(),
            ];
        }

        // Decode MIME-encoded subject (RFC 2047)
        $rawSubject = $message->getSubject()?->toString()
            ?? self::getHeaderValue($headers, 'Subject');
        $subject = self::decodeMimeHeader($rawSubject);

        // Extract CC addresses
        $cc = [];
        $ccAddresses = $message->getCc();
        if ($ccAddresses) {
            foreach ($ccAddresses as $ccAddr) {
                $ccEmail = strtolower(trim($ccAddr->mail ?? ''));
                if ($ccEmail !== '' && $ccEmail !== $fromEmail) {
                    $cc[] = [
                        'email' => $ccEmail,
                        'name' => self::decodeMimeHeader(trim($ccAddr->personal ?? '')) ?: null,
                    ];
                }
            }
        }

        return new ParsedEmail(
            messageId: self::getHeaderValue($headers, 'Message-ID') ?? self::getHeaderValue($headers, 'Message-Id'),
            fromEmail: $fromEmail,
            fromName: self::decodeMimeHeader($fromName) ?: null,
            subject: $subject,
            textBody: $message->getTextBody(),
            htmlBody: $message->getHTMLBody(),
            inReplyTo: $inReplyTo ? trim($inReplyTo) : null,
            references: $references,
            headers: $headers,
            attachments: $attachments,
            uid: $message->getUid(),
            cc: $cc,
        );
    }

    /**
     * Garde uniquement le texte tapé par l’utilisateur, sans la citation du mail du support (réponse par email).
     */
    public static function stripEmailReplyQuotes(string $text): string
    {
        $text = str_replace("\r\n", "\n", $text);

        // Blocs « réponse / citation » (FR / EN / Gmail, Outlook…). Pas de /s sur .+ pour rester sur une ligne quand il faut.
        $cutPatterns = [
            // Gmail FR : « … a » en fin de 1re ligne, « écrit : » sur la ligne suivante (.+ sans /s = pas de saut ligne)
            '/(?:^|\n)\s*Le(.+)\s+a\s*\n\s*écrit\s*:\s*\n*/u',
            // FR une ligne (« Le … a écrit : »)
            '/\n\s*Le(.+)\s+a\s+écrit\s*:\s*\n/u',
            '/\n\s*Le(.+)\s+a\s+écrit\s*:\s*$/u',
            '/^\s*Le(.+)\s+a\s+écrit\s*:\s*\n/u',
            '/^\s*Le(.+)\s+a\s+écrit\s*:\s*$/u',
            // EN (Gmail / Apple)
            '/\n\s*On(.+)wrote:\s*\n/u',
            '/\n\s*On(.+)wrote:\s*$/u',
            '/^\s*On(.+)wrote:\s*\n/u',
            '/^\s*On(.+)wrote:\s*$/u',
            '/\n\s*Am(.+)schrieb:\s*\n/u',
            '/\n\s*---------- Forwarded message ----------/i',
            '/\n\s*-----Original Message-----/i',
            '/\n\s*________________________________/s',
            '/\n\s*De : .+\n\s*Envoyé :/is',
            '/\n\s*From: .+\n\s*Sent:/is',
        ];

        $len = strlen($text);
        foreach ($cutPatterns as $p) {
            if (preg_match($p, $text, $m, PREG_OFFSET_CAPTURE)) {
                $pos = $m[0][1];
                if ($pos >= 0 && $pos < $len) {
                    $len = $pos;
                }
            }
        }
        $text = substr($text, 0, $len);

        $text = self::stripPlainTextAngleQuoteLines($text);

        return trim(preg_replace("/\n{3,}/", "\n\n", $text));
    }

    /**
     * Supprime à partir de la première ligne de citation type client mail (lignes commençant par >).
     * Utilisé quand le délimiteur « Le … a écrit » n’a pas été reconnu ou après coupe partielle.
     */
    public static function stripPlainTextAngleQuoteLines(string $text): string
    {
        $text = str_replace("\r\n", "\n", $text);
        $lines = explode("\n", $text);
        $out = [];

        foreach ($lines as $i => $line) {
            $prev = $i > 0 ? $lines[$i - 1] : null;
            $prevEmpty = $prev === null || trim((string) $prev) === '';

            if ($prevEmpty && preg_match('/^\s*>/', $line)) {
                break;
            }
            $out[] = $line;
        }

        return implode("\n", $out);
    }

    /**
     * Convert HTML email to clean plain text for storage in ticket messages.
     */
    public static function sanitizeHtml(string $html): string
    {
        // 1. Extract <body> content only (skip <head>, <style> in head, etc.)
        if (preg_match('/<body[^>]*>(.*)<\/body>/is', $html, $matches)) {
            $html = $matches[1];
        }

        // 2. Remove tags whose content must be completely invisible
        $html = preg_replace('/<head\b[^>]*>.*?<\/head>/is', '', $html);
        $html = preg_replace('/<style\b[^>]*>.*?<\/style>/is', '', $html);
        $html = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $html);
        $html = preg_replace('/<iframe\b[^>]*>.*?<\/iframe>/is', '', $html);

        // 3. Remove hidden elements (email preheaders with display:none / max-height:0)
        $html = preg_replace('/<div\b[^>]*style\s*=\s*"[^"]*display\s*:\s*none[^"]*"[^>]*>.*?<\/div>/is', '', $html);
        $html = preg_replace('/<span\b[^>]*style\s*=\s*"[^"]*display\s*:\s*none[^"]*"[^>]*>.*?<\/span>/is', '', $html);
        $html = preg_replace('/<div\b[^>]*style\s*=\s*"[^"]*max-height\s*:\s*0[^"]*overflow\s*:\s*hidden[^"]*"[^>]*>.*?<\/div>/is', '', $html);

        // 4. Remove MSO conditional comments and regular HTML comments
        $html = preg_replace('/<!--\[if\b[^>]*\]>.*?<!\[endif\]-->/is', '', $html);
        $html = preg_replace('/<!--.*?-->/s', '', $html);

        // 5. Remove event handlers (on*)
        $html = preg_replace('/\s+on\w+\s*=\s*["\'][^"\']*["\']/i', '', $html);

        // 6. Preserve link URLs: <a href="url">text</a> → text (url)
        $html = preg_replace_callback('/<a\b[^>]*href\s*=\s*["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/is', function ($m) {
            $url = trim($m[1]);
            $text = trim(strip_tags($m[2]));
            if ($text === '' || $text === $url) {
                return $url;
            }

            return "{$text} ({$url})";
        }, $html);

        // 7. Convert HTML structure to line breaks before stripping tags
        $html = preg_replace('/<br\s*\/?>/i', "\n", $html);
        $html = preg_replace('/<\/p>/i', "\n\n", $html);
        $html = preg_replace('/<\/div>/i', "\n", $html);
        $html = preg_replace('/<\/tr>/i', "\n", $html);
        $html = preg_replace('/<\/h[1-6]>/i', "\n\n", $html);
        $html = preg_replace('/<li[^>]*>/i', '- ', $html);
        $html = preg_replace('/<\/li>/i', "\n", $html);
        $html = preg_replace('/<\/blockquote>/i', "\n", $html);

        // 8. Strip all remaining HTML tags
        $text = strip_tags($html);

        // 9. Decode HTML entities
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // 10. Clean up whitespace
        $text = preg_replace('/[ \t]+/', ' ', $text);        // collapse spaces/tabs
        $text = preg_replace('/\n[ \t]+/', "\n", $text);     // trim leading space on lines
        $text = preg_replace('/[ \t]+\n/', "\n", $text);     // trim trailing space on lines
        $text = preg_replace('/\n{3,}/', "\n\n", $text);     // max 2 consecutive newlines

        return trim($text);
    }

    /**
     * Decode RFC 2047 MIME-encoded header value.
     * Handles =?charset?B?...?= (base64) and =?charset?Q?...?= (quoted-printable).
     */
    public static function decodeMimeHeader(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return $value;
        }

        // Try iconv_mime_decode first (most reliable)
        if (function_exists('iconv_mime_decode')) {
            $decoded = @iconv_mime_decode($value, ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8');
            if ($decoded !== false && $decoded !== $value) {
                return trim($decoded);
            }
        }

        // Fallback: mb_decode_mimeheader
        if (function_exists('mb_decode_mimeheader')) {
            $decoded = @mb_decode_mimeheader($value);
            if ($decoded !== $value) {
                return trim($decoded);
            }
        }

        // Manual fallback for =?charset?encoding?data?= patterns
        $decoded = preg_replace_callback(
            '/=\?([^?]+)\?(B|Q)\?([^?]+)\?=/i',
            function ($matches) {
                $charset = $matches[1];
                $encoding = strtoupper($matches[2]);
                $data = $matches[3];

                if ($encoding === 'B') {
                    $decoded = base64_decode($data);
                } else {
                    $decoded = quoted_printable_decode(str_replace('_', ' ', $data));
                }

                if ($charset && strtoupper($charset) !== 'UTF-8') {
                    $decoded = @iconv($charset, 'UTF-8//IGNORE', $decoded) ?: $decoded;
                }

                return $decoded;
            },
            $value
        );

        return trim($decoded);
    }

    private static function extractHeaders(Message $message): array
    {
        $headers = [];
        $headerObj = $message->getHeader();

        if ($headerObj) {
            $raw = $headerObj->raw ?? '';
            $currentKey = null;
            $currentValue = '';

            foreach (explode("\n", $raw) as $line) {
                $rtrimmed = rtrim($line, "\r");

                // Continuation line (starts with space/tab) — append to current header
                if ($rtrimmed !== '' && ($rtrimmed[0] === ' ' || $rtrimmed[0] === "\t")) {
                    if ($currentKey !== null) {
                        $currentValue .= ' '.trim($rtrimmed);
                    }

                    continue;
                }

                // Save previous header
                if ($currentKey !== null) {
                    $headers[$currentKey] = trim($currentValue);
                }

                // Parse new header line
                $trimmed = trim($rtrimmed);
                if ($trimmed === '') {
                    $currentKey = null;

                    continue;
                }

                $pos = strpos($trimmed, ':');
                if ($pos !== false) {
                    $currentKey = trim(substr($trimmed, 0, $pos));
                    $currentValue = trim(substr($trimmed, $pos + 1));
                } else {
                    $currentKey = null;
                }
            }

            // Don't forget the last header
            if ($currentKey !== null) {
                $headers[$currentKey] = trim($currentValue);
            }
        }

        return $headers;
    }

    private static function getHeaderValue(array $headers, string $name): ?string
    {
        $nameLower = strtolower($name);
        foreach ($headers as $key => $value) {
            if (strtolower($key) === $nameLower) {
                return is_array($value) ? ($value[0] ?? null) : (string) $value;
            }
        }

        return null;
    }
}
