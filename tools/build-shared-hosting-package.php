<?php

$root = dirname(__DIR__);
$target = $root.'/build/shared-hosting/public_html';

function removeTree(string $path): void
{
    if (! is_dir($path)) {
        return;
    }

    $items = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST,
    );

    foreach ($items as $item) {
        $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
    }

    rmdir($path);
}

function copyTree(string $source, string $destination, array $exclude = []): void
{
    if (! is_dir($destination)) {
        mkdir($destination, 0755, true);
    }

    $items = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST,
    );

    foreach ($items as $item) {
        $relative = str_replace('\\', '/', substr($item->getPathname(), strlen($source) + 1));
        $topLevel = explode('/', $relative)[0];

        if (in_array($topLevel, $exclude, true) || in_array($relative, $exclude, true)) {
            continue;
        }

        $dest = $destination.'/'.$relative;
        if ($item->isDir()) {
            if (! is_dir($dest)) {
                mkdir($dest, 0755, true);
            }
        } else {
            copy($item->getPathname(), $dest);
        }
    }
}

removeTree($target);

copyTree($root, $target, [
    '.git',
    'build',
    'docker',
    'node_modules',
    'tests',
    'tools',
    'public_html',
    '.env',
    '.phpunit.cache',
]);

echo "Shared hosting package ready: {$target}".PHP_EOL;
