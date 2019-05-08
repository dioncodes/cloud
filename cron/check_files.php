<?php

require __DIR__ . '/../vendor/autoload.php';

$uploader = new drpdev\Cloud\Uploader();

$uploader->moveUploadedFiles();
