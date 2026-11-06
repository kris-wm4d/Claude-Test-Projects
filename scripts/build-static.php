<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$output = $root . DIRECTORY_SEPARATOR . 'dist';

if (is_file($root . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'settings.php')) {
    throw new RuntimeException('Static export stopped: do not publish local settings. Build from a clean checkout.');
}

if (!is_dir($output) && !mkdir($output, 0755, true) && !is_dir($output)) {
    throw new RuntimeException('Could not create the static output directory.');
}

function copy_static_directory(string $source, string $destination): void
{
    if (!is_dir($source)) {
        throw new RuntimeException('Missing directory: ' . $source);
    }
    if (!is_dir($destination) && !mkdir($destination, 0755, true) && !is_dir($destination)) {
        throw new RuntimeException('Could not create directory: ' . $destination);
    }

    foreach (new DirectoryIterator($source) as $entry) {
        if ($entry->isDot()) {
            continue;
        }

        $sourcePath = $source . DIRECTORY_SEPARATOR . $entry->getFilename();
        $destinationPath = $destination . DIRECTORY_SEPARATOR . $entry->getFilename();

        if ($entry->isDir()) {
            copy_static_directory($sourcePath, $destinationPath);
        } elseif (!copy($sourcePath, $destinationPath)) {
            throw new RuntimeException('Could not copy file: ' . $sourcePath);
        }
    }
}

$pages = [
    'financing.php',
    'gallery.php',
    'testimonials.php',
    'submit.php',
];

foreach (['financing.html', 'gallery.html', 'testimonials.html', 'index.php'] as $stalePage) {
    $stalePath = $output . DIRECTORY_SEPARATOR . $stalePage;
    if (is_file($stalePath) && !unlink($stalePath)) {
        throw new RuntimeException('Could not remove stale static page: ' . $stalePage);
    }
}

foreach (['assets', 'Uploads'] as $directory) {
    copy_static_directory(
        $root . DIRECTORY_SEPARATOR . $directory,
        $output . DIRECTORY_SEPARATOR . $directory
    );
}

$inc = $root . DIRECTORY_SEPARATOR . 'inc';
copy_static_directory($inc, $output . DIRECTORY_SEPARATOR . 'inc');

$dataOutput = $output . DIRECTORY_SEPARATOR . 'data';
if (!mkdir($dataOutput, 0755, true) && !is_dir($dataOutput)) {
    throw new RuntimeException('Could not create the protected data directory.');
}
foreach (['.htaccess', 'index.html'] as $guardFile) {
    $sourcePath = $root . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . $guardFile;
    if (!is_file($sourcePath) || !copy($sourcePath, $dataOutput . DIRECTORY_SEPARATOR . $guardFile)) {
        throw new RuntimeException('Could not copy protected data guard file: ' . $guardFile);
    }
}

$htaccess = file_get_contents($root . DIRECTORY_SEPARATOR . '.htaccess');
if (!is_string($htaccess)) {
    throw new RuntimeException('Could not read the root .htaccess file.');
}
$htaccess = preg_replace('/^DirectoryIndex\s+.*$/m', 'DirectoryIndex index.html index.php', $htaccess, 1, $directoryIndexCount);
if (!is_string($htaccess) || $directoryIndexCount !== 1
    || file_put_contents($output . DIRECTORY_SEPARATOR . '.htaccess', $htaccess) === false) {
    throw new RuntimeException('Could not configure the static homepage as the default document.');
}

$staticHomeSource = $root . DIRECTORY_SEPARATOR . 'index.php';
if (!is_file($staticHomeSource)) {
    throw new RuntimeException('Missing public page: index.php');
}

ob_start();
include $staticHomeSource;
$html = ob_get_clean();
if (!is_string($html)) {
    throw new RuntimeException('Could not render the static homepage.');
}

$html = preg_replace_callback(
    '/\bhref=(["\'])([^"\']+)\1/i',
    static function (array $match): string {
        $url = $match[2];
        $parts = parse_url($url);
        $path = is_array($parts) ? ($parts['path'] ?? '') : '';
        if ($path !== 'index.php') {
            return $match[0];
        }

        $staticUrl = 'index.html';
        if (isset($parts['query'])) {
            $staticUrl .= '?' . $parts['query'];
        }
        if (isset($parts['fragment'])) {
            $staticUrl .= '#' . $parts['fragment'];
        }

        return 'href=' . $match[1] . $staticUrl . $match[1];
    },
    $html
);
if (!is_string($html)) {
    throw new RuntimeException('Could not rewrite the homepage links.');
}

$html = preg_replace('/<html\b([^>]*)>/i', '<html$1 data-static-site="true">', $html, 1, $htmlTagCount);
if (!is_string($html) || $htmlTagCount !== 1) {
    throw new RuntimeException('Could not mark static mode for the homepage.');
}

if (preg_match_all('/\bhref=["\']([^"\']+\.php(?:[?#][^"\']*)?)["\']/i', $html, $matches)) {
    foreach ($matches[1] as $url) {
        $parts = parse_url($url);
        $target = is_array($parts) ? ltrim($parts['path'] ?? '', '/') : '';
        if (!in_array($target, $pages, true)) {
            throw new RuntimeException('Unexpected PHP link in static homepage: ' . $target);
        }
    }
}

if (file_put_contents($output . DIRECTORY_SEPARATOR . 'index.html', $html) === false) {
    throw new RuntimeException('Could not write the static homepage.');
}

foreach ($pages as $source) {
    $sourcePath = $root . DIRECTORY_SEPARATOR . $source;
    if (!is_file($sourcePath)) {
        throw new RuntimeException('Missing public page: ' . $source);
    }
    $php = file_get_contents($sourcePath);
    if (!is_string($php)) {
        throw new RuntimeException('Could not read PHP page: ' . $source);
    }
    $php = preg_replace_callback(
        '/\bhref=(["\'])index\.php((?:[?#][^"\']*)?)\1/i',
        static function (array $match): string {
            return 'href=' . $match[1] . 'index.html' . $match[2] . $match[1];
        },
        $php
    );
    if (!is_string($php) || file_put_contents($output . DIRECTORY_SEPARATOR . $source, $php) === false) {
        throw new RuntimeException('Could not copy PHP page: ' . $source);
    }
}

echo 'Built the static homepage and PHP inner pages in ' . $output . PHP_EOL;
