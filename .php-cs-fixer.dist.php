<?php

$finder = PhpCsFixer\Finder::create()
    ->ignoreDotFiles(false)
    ->ignoreVCSIgnored(true)
    ->exclude('tests/Fixtures')
    ->in(__DIR__ . "/App")
    ->in(__DIR__ . "/UnitTest");

$cacheDir = __DIR__ . '/Runtime';
$config = new PhpCsFixer\Config();
$config->setCacheFile($cacheDir . '/.php-cs-fixer.cache')->setFinder($finder);

return $config;