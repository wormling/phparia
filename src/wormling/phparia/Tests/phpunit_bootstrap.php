<?php

$loader = @include __DIR__ . '/../vendor/autoload.php';
if (!$loader) {
    $loader = require __DIR__ . '/../../../../vendor/autoload.php';
}
$loader->addPsr4('phparia\\Tests\\', __DIR__);
