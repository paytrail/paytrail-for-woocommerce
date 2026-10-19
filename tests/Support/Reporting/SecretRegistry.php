<?php

declare(strict_types=1);

namespace Tests\Support\Reporting;

/**
 * The single place that knows which environment values are secret. Reads tests/.env and
 * the real environment, and hands back a primed Redactor. Real env vars win, so CI can
 * inject credentials without a .env file.
 */
final class SecretRegistry
{
    /**
     * Env vars whose values must never appear in an artifact.
     *
     * @var array<int, string>
     */
    public const SECRET_KEYS = [
        'PAYTRAIL_SECRET_KEY',
        'NGROK_AUTHTOKEN',
        'WORDPRESS_ADMIN_PASSWORD',
    ];

    /** Builds a Redactor from the environment. */
    public static function fromEnvironment(?string $envFile = null, ?array $env = null): Redactor
    {
        $envFile ??= dirname(__DIR__, 2) . '/.env';
        $values    = self::parseEnvFile($envFile);

        $read = static function (string $key) use ($values, $env): string {
            if ($env === null) {
                $fromEnv = getenv($key);
                $fromEnv = is_string($fromEnv) ? $fromEnv : '';
            } else {
                $fromEnv = $env[$key] ?? '';
            }

            return $fromEnv !== '' ? $fromEnv : ($values[$key] ?? '');
        };

        $redactor = Redactor::withDefaultPatterns();

        foreach (self::SECRET_KEYS as $key) {
            $value = $read($key);
            if ($value !== '') {
                $redactor = $redactor->withSecret($value, $key);
            }
        }

        return $redactor;
    }

    /**
     * Minimal .env parser. Deliberately not a dependency: the file is a flat list of
     * KEY=VALUE lines with `#` comments.
     *
     * @return array<string, string>
     */
    public static function parseEnvFile(string $path): array
    {
        if (! is_readable($path)) {
            return [];
        }

        $values = [];

        foreach ((array) file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim((string) $line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            $parts = explode('=', $line, 2);
            if (count($parts) !== 2) {
                continue;
            }

            $key   = trim($parts[0]);
            $value = trim($parts[1]);

            if (strlen($value) >= 2 && (
                ($value[0] === '"' && substr($value, -1) === '"')
                || ($value[0] === "'" && substr($value, -1) === "'")
            )) {
                $value = substr($value, 1, -1);
            }

            $values[$key] = $value;
        }

        return $values;
    }
}
