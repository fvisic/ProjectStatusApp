<?php
/**
 * Dark-mode lint for Blade templates.
 *
 * Scans `resources/views/**\/*.blade.php` for Tailwind utility classes that
 * affect light-mode appearance but lack a paired `dark:` variant, e.g.
 *   bg-white                      → needs   dark:bg-*
 *   text-gray-900 / text-gray-800 → needs   dark:text-*
 *   border-gray-200/300           → needs   dark:border-*
 *
 * Exits 1 with a list of offending file:line:class entries when violations
 * are found. Designed to run from a pre-commit hook and from CI.
 *
 * Usage:
 *   php scripts/lint-dark-mode.php                    # scan all blade files
 *   php scripts/lint-dark-mode.php file1 file2 ...    # scan specific files
 */

const ROOT = __DIR__ . '/..';

// Class prefixes that require a dark: pairing in the same class string.
// Each entry: [pattern => required dark prefix]
$RULES = [
    // backgrounds
    '/(?<![\w:])bg-white(?![\w-])/'                        => 'dark:bg-',
    '/(?<![\w:])bg-gray-(50|100|200)(?![\w-])/'            => 'dark:bg-',

    // foreground text that becomes invisible on dark bg
    '/(?<![\w:])text-gray-(700|800|900)(?![\w-])/'         => 'dark:text-',
    '/(?<![\w:])text-black(?![\w-])/'                      => 'dark:text-',

    // borders that disappear or shimmer on dark
    '/(?<![\w:])border-gray-(100|200|300)(?![\w-])/'       => 'dark:border-',
];

// Allow-list: files (relative to repo root) that intentionally skip the lint.
$IGNORE = [
    // Mail templates render in email clients without dark-mode overrides.
    'resources/views/vendor/mail/',
    'resources/views/emails/',
];

function gather_files(array $argv): array {
    if (count($argv) > 1) {
        return array_slice($argv, 1);
    }
    $files = [];
    $dir   = realpath(ROOT . '/resources/views');
    $it    = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $f) {
        if ($f->isFile() && str_ends_with($f->getFilename(), '.blade.php')) {
            $files[] = $f->getPathname();
        }
    }
    sort($files);
    return $files;
}

function is_ignored(string $absPath, array $ignore): bool {
    $rel = ltrim(str_replace(realpath(ROOT) ?: ROOT, '', realpath($absPath) ?: $absPath), '/');
    foreach ($ignore as $prefix) {
        if (str_starts_with($rel, $prefix)) return true;
    }
    return false;
}

/**
 * Extract every `class="..."` value (including alpine `:class` and merged
 * blade attribute strings) so we lint only inside a class attribute.
 */
function extract_class_attrs(string $html): array {
    // Match  class="..."   :class="..."   class='...'
    preg_match_all('/(?:^|[\s({\[,])(?::?class)\s*=\s*("[^"]*"|\'[^\']*\')/i', $html, $m, PREG_OFFSET_CAPTURE);
    return $m[1] ?? [];
}

function line_of_offset(string $src, int $offset): int {
    return substr_count(substr($src, 0, $offset), "\n") + 1;
}

$files = gather_files($argv);
$violations = [];

foreach ($files as $file) {
    if (is_ignored($file, $IGNORE)) continue;
    $src   = file_get_contents($file);
    if ($src === false) continue;
    $attrs = extract_class_attrs($src);

    foreach ($attrs as $attr) {
        [$raw, $offset] = $attr;
        $value = trim($raw, "\"'");

        foreach ($RULES as $pattern => $requiredPrefix) {
            if (preg_match($pattern, $value, $hit)) {
                if (!str_contains($value, $requiredPrefix)) {
                    $line = line_of_offset($src, $offset);
                    $violations[] = sprintf(
                        '%s:%d  missing %s sibling for "%s"',
                        $file,
                        $line,
                        $requiredPrefix . '*',
                        $hit[0]
                    );
                }
            }
        }
    }
}

if (!$violations) {
    fwrite(STDOUT, "Dark-mode lint: OK (" . count($files) . " files scanned)\n");
    exit(0);
}

fwrite(STDERR, "Dark-mode lint: " . count($violations) . " violation(s)\n\n");
foreach ($violations as $v) {
    fwrite(STDERR, "  " . $v . "\n");
}
fwrite(STDERR, "\nFix by adding a `dark:` sibling to every offending class.\n");
exit(1);
