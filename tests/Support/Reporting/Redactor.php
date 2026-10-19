<?php

declare(strict_types=1);

namespace Tests\Support\Reporting;

/**
 * Replaces known secret values, and anything matching a sensitive-looking pattern,
 * with a fixed mask.
 */
final class Redactor
{
    public const MASK = '***REDACTED***';

    /**
     * Shorter values are ignored. An unset env var would otherwise register the empty
     * string and mangle every artifact.
     */
    public const MIN_SECRET_LENGTH = 8;

    /** @var array<string, string> */
    private $literals;

    /** @var array<string, string> */
    private $patterns;

    private function __construct(array $literals, array $patterns)
    {
        // Longest first, so a secret containing a shorter one is masked whole.
        uksort($literals, static fn(string $a, string $b): int => strlen($b) <=> strlen($a));

        $this->literals = $literals;
        $this->patterns = $patterns;
    }

    /** A redactor that knows no specific secrets but still catches credential-shaped text. */
    public static function withDefaultPatterns(): self
    {
        return new self([], [
            // Authorization: Basic <b64> / Bearer <token>, in headers, JSON and logs.
            '/(Authorization["\']?\s*[:=]\s*["\']?)(Basic|Bearer)\s+[A-Za-z0-9+\/=._-]+/i'
                => '$1$2 ' . self::MASK,
            // Sensitive keys whose values we cannot know in advance.
            '/(["\'](?:secret_key|shared_secret|client_token|access_token|authtoken|api_key|password)["\']\s*[:=>]+\s*["\'])[^"\']*(["\'])/i'
                => '$1' . self::MASK . '$2',
            // Shell and env style assignments.
            '/\b(NGROK_AUTHTOKEN|PAYTRAIL_SECRET_KEY|WORDPRESS_ADMIN_PASSWORD)\s*=\s*\S+/'
                => '$1=' . self::MASK,
        ]);
    }

    /** Registers a secret and every encoding of it this codebase can emit. */
    public function withSecret(string $secret, string $label): self
    {
        $literals = $this->literals;

        foreach ($this->encodingsOf($secret) as $variant) {
            $literals[$variant] = $label;
        }

        return new self($literals, $this->patterns);
    }

    public function scrub(string $text): string
    {
        foreach (array_keys($this->literals) as $literal) {
            // Cast: an all-digit secret registers as an int array key.
            $text = str_replace((string) $literal, self::MASK, $text);
        }

        foreach ($this->patterns as $pattern => $replacement) {
            $text = (string) preg_replace($pattern, $replacement, $text);
        }

        return $text;
    }

    /**
     * Every literal this redactor knows, mapped to the env var it came from.
     *
     * @return array<string, string>
     */
    public function literals(): array
    {
        return $this->literals;
    }

    /** @return array<int, string> */
    private function encodingsOf(string $secret): array
    {
        if (strlen($secret) < self::MIN_SECRET_LENGTH) {
            return [];
        }

        $decoded = htmlspecialchars_decode($secret, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401);

        $variants = [
            $secret,
            $decoded,
            base64_encode($secret),
            base64_encode($decoded),
            rawurlencode($secret),
            urlencode($secret),
            // JSON-escaped, e.g. a secret containing a slash inside a body dump.
            trim((string) json_encode($secret), '"'),
        ];

        return array_values(array_unique(array_filter(
            $variants,
            static fn(string $variant): bool => strlen($variant) >= self::MIN_SECRET_LENGTH
        )));
    }
}
