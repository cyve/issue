<?php

$filename = 'issue.phar';

if(is_file($filename)) unlink($filename);

$phar = new Phar($filename, FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::KEY_AS_FILENAME, $filename);
$phar->buildFromDirectory(__DIR__, "/^(?!.git).*$/");
