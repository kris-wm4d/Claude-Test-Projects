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
    'index' => 'index.php',
    'financing' => 'financing.php',
    'gallery' => 'gallery.php',
    'testimonials' => 'testimonials.php',
];

foreach (['assets', 'Uploads'] as $directory) {
    copy_static_directory(
        $root . DIRECTORY_SEPARATOR . $directory,
        $output . DIRECTORY_SEPARATOR . $directory
    );
}

$robots = $root . DIRECTORY_SEPARATOR . 'robots.txt';
if (is_file($robots) && !copy($robots, $output . DIRECTORY_SEPARATOR . 'robots.txt')) {
    throw new RuntimeException('Could not copy robots.txt.');
}

foreach ($pages as $name => $source) {
    $sourcePath = $root . DIRECTORY_SEPARATOR . $source;
    if (!is_file($sourcePath)) {
        throw new RuntimeException('Missing public page: ' . $source);
    }

    ob_start();
    include $sourcePath;
    $html = ob_get_clean();
    if (!is_string($html)) {
        throw new RuntimeException('Could not render public page: ' . $source);
    }

    $html = preg_replace_callback(
        '/\bhref=(["\'])([^"\']+)\1/i',
        static function (array $match) use ($pages): string {
            $url = $match[2];
            $parts = parse_url($url);
            $path = is_array($parts) ? ($parts['path'] ?? '') : '';
            $target = array_search($path, $pages, true);

            if ($target === false) {
                return $match[0];
            }

            $staticUrl = $target . '.html';
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
        throw new RuntimeException('Could not rewrite navigation links in: ' . $source);
    }

    $html = preg_replace('/<html\b([^>]*)>/i', '<html$1 data-static-site="true">', $html, 1, $htmlTagCount);
    if (!is_string($html) || $htmlTagCount !== 1) {
        throw new RuntimeException('Could not mark static mode for: ' . $source);
    }

    if (preg_match('/\bhref=["\'][^"\']+\.php(?:[?#][^"\']*)?["\']/i', $html)) {
        throw new RuntimeException('Unconverted PHP link found in: ' . $source);
    }

    $destination = $output . DIRECTORY_SEPARATOR . $name . '.html';
    if (file_put_contents($destination, $html) === false) {
        throw new RuntimeException('Could not write static page: ' . $destination);
    }
}

echo 'Built static pages in ' . $output . PHP_EOL;
