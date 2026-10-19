<?php
/**
 * Refuses to hand over a report that contains a credential. On a hit it deletes the
 * report and exits non-zero.
 *
 * Scans the Allure results rather than the rendered HTML: Allure base64-encodes
 * attachment payloads, so grepping index.html would give false confidence.
 *
 * Usage: php tests/_support_scripts/verify-no-secrets.php [reportDir ...]
 */

declare(strict_types=1);

use Tests\Support\Reporting\SecretRegistry;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

$root      = dirname(__DIR__, 2);
$outputDir = $root . '/tests/_output';

$literals = SecretRegistry::fromEnvironment()->literals();

if ($literals === []) {
    fwrite(STDERR, "verify-no-secrets: no credentials configured in tests/.env, nothing to check.\n");
    fwrite(STDERR, "  (Expected on a machine that only runs the Integration and Harness suites.)\n");
    exit(0);
}

$scannableExtensions = ['json', 'html', 'htm', 'txt', 'log', 'xml', 'csv', 'yml', 'yaml', ''];

$leaks   = [];
$scanned = 0;

if (is_dir($outputDir)) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($outputDir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $file) {
        if (! $file->isFile()) {
            continue;
        }

        $path = (string) $file->getRealPath();

        // The rendered report is derived from the results this already scans.
        if (str_contains($path, '/tests/_output/report')) {
            continue;
        }

        if (! in_array(strtolower($file->getExtension()), $scannableExtensions, true)) {
            continue;
        }

        $contents = (string) file_get_contents($path);
        ++$scanned;

        foreach ($literals as $literal => $label) {
            // Cast: an all-digit secret registers as an int array key.
            if (str_contains($contents, (string) $literal)) {
                // Never print the value, name the env var it came from.
                $leaks[] = sprintf('%s  (value of %s)', substr($path, strlen($root) + 1), $label);
            }
        }
    }
}

if ($leaks !== []) {
    fwrite(STDERR, "\n\033[41m LEAK \033[0m A credential survived redaction:\n\n");
    foreach (array_unique($leaks) as $leak) {
        fwrite(STDERR, "  - {$leak}\n");
    }

    foreach (array_slice($argv, 1) as $reportDir) {
        if (is_dir($reportDir)) {
            exec('rm -rf ' . escapeshellarg($reportDir));
            fwrite(STDERR, "\nDeleted {$reportDir}, it was not safe to publish.\n");
        }
    }

    fwrite(STDERR, "\nFix the artifact writer so it scrubs through Redactor, then re-run.\n");
    exit(1);
}

echo "verify-no-secrets: scanned {$scanned} files, no credentials found.\n";
exit(0);
