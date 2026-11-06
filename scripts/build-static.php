<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$settings = $root . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'settings.php';
if (is_file($settings)) {
    throw new RuntimeException('Static export stopped: remove local settings before generating a public homepage.');
}

$source = $root . DIRECTORY_SEPARATOR . 'index.php';
if (!is_file($source)) {
    throw new RuntimeException('Missing source page: index.php');
}

ob_start();
include $source;
$html = ob_get_clean();
if (!is_string($html)) {
    throw new RuntimeException('Could not render index.php.');
}

$html = preg_replace('/<html\b([^>]*)>/i', '<html$1 data-static-site="true">', $html, 1, $htmlTagCount);
if (!is_string($html) || $htmlTagCount !== 1) {
    throw new RuntimeException('Could not mark the rendered homepage as static.');
}

$destination = $root . DIRECTORY_SEPARATOR . 'index.html';
if (file_put_contents($destination, $html) === false) {
    throw new RuntimeException('Could not write index.html.');
}

echo 'Generated static homepage: ' . $destination . PHP_EOL;
