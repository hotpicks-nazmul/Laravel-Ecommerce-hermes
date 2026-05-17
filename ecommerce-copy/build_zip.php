<?php

$zipName = 'my-ecommerce.zip';

if (!class_exists('ZipArchive')) {
    die("Error: ZipArchive extension is not loaded. Install php-zip and try again.\n");
}

$rootDir = __DIR__;
if (!is_dir($rootDir)) {
    die("Error: Directory '{$rootDir}' does not exist.\n");
}

$excludeDirs = [
    'node_modules',
    '.git',
    '.idea',
    '.vscode',
];

$excludeFiles = [
    'build_zip.php',
    'storage' . DIRECTORY_SEPARATOR . 'installed',
];

$excludeNames = [
    '.DS_Store',
    'Thumbs.db',
];

$zip = new ZipArchive();
$zipPath = $rootDir . DIRECTORY_SEPARATOR . $zipName;

if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    die("Error: Could not create zip file at {$zipPath}\n");
}

$filesAdded = 0;

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootDir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($iterator as $file) {
    $relativePath = str_replace($rootDir . DIRECTORY_SEPARATOR, '', $file->getPathname());

    $parts = explode(DIRECTORY_SEPARATOR, $relativePath);
    $skip = false;

    foreach ($parts as $part) {
        if (in_array($part, $excludeDirs, true)) {
            $skip = true;
            break;
        }
        if (in_array($part, $excludeNames, true)) {
            $skip = true;
            break;
        }
    }

    if ($skip) {
        continue;
    }

    if (in_array($relativePath, $excludeFiles, true)) {
        continue;
    }

    if ($file->isDir()) {
        $zip->addEmptyDir($relativePath);
    } else {
        $zip->addFile($file->getPathname(), $relativePath);
        $filesAdded++;
        echo "Adding {$relativePath}...\n";
    }
}

$zip->close();

$sizeMB = round(filesize($zipPath) / 1024 / 1024, 2);
echo "✅ Created: {$zipName} ({$sizeMB} MB)\n";
