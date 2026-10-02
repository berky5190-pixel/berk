<?php
$dir = new RecursiveDirectoryIterator(__DIR__ . '/../app/Views');
$ite = new RecursiveIteratorIterator($dir);
foreach($ite as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        $newContent = preg_replace('/onsubmit="return confirm\(\'(.*?)\'\);"/', 'data-confirm="$1"', $content);
        if ($newContent !== $content) {
            file_put_contents($file->getPathname(), $newContent);
            echo 'Updated: ' . $file->getPathname() . "\n";
        }
    }
}
