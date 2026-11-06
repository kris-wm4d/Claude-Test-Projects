<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$settings = $root . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'settings.php';
if (is_file($settings)) {
    throw new RuntimeException('Static export stopped: remove local settings before generating a public homepage.');
}

function render_static_page(string $source, string $destination): void
{
    if (!is_file($source)) {
        throw new RuntimeException('Missing source page: ' . $source);
    }

    ob_start();
    include $source;
    $html = ob_get_clean();
    if (!is_string($html)) {
        throw new RuntimeException('Could not render ' . $source . '.');
    }

    $html = preg_replace('/<html\b([^>]*)>/i', '<html$1 data-static-site="true">', $html, 1, $htmlTagCount);
    if (!is_string($html) || $htmlTagCount !== 1) {
        throw new RuntimeException('Could not mark the rendered page as static: ' . $source);
    }

    $directory = dirname($destination);
    if (!is_dir($directory) && !mkdir($directory, 0755, true)) {
        throw new RuntimeException('Could not create output directory: ' . $directory);
    }
    if (file_put_contents($destination, $html) === false) {
        throw new RuntimeException('Could not write static page: ' . $destination);
    }

}

$pages = [
    [
        $root . DIRECTORY_SEPARATOR . 'index.php',
        $root . DIRECTORY_SEPARATOR . 'index.html',
    ],
    [
        $root . DIRECTORY_SEPARATOR . 'implants' . DIRECTORY_SEPARATOR . 'dental-implants' . DIRECTORY_SEPARATOR . 'index.php',
        $root . DIRECTORY_SEPARATOR . 'implants' . DIRECTORY_SEPARATOR . 'dental-implants' . DIRECTORY_SEPARATOR . 'index.html',
    ],
];

foreach ($pages as [$source, $destination]) {
    render_static_page($source, $destination);
}

foreach ($pages as [, $destination]) {
    echo 'Generated static page: ' . $destination . PHP_EOL;
}
