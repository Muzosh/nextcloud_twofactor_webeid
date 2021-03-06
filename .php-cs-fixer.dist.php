<?php

// declare(strict_types=1);

// require_once './vendor/autoload.php';

// use Nextcloud\CodingStandard\Config;

// $config = new Config();
// $config
// 	->getFinder()
// 	->ignoreVCSIgnored(true)
// 	->notPath('build')
// 	->notPath('l10n')
// 	->notPath('src')
// 	->notPath('vendor')
// 	->in(__DIR__);
// return $config;

/*
 * This document has been generated with
 * https://mlocati.github.io/php-cs-fixer-configurator/#version:3.8.0|configurator
 * you can change this configuration by importing this file.
 */
$config = new PhpCsFixer\Config();
return $config
    ->setRules([
        '@PSR1' => true,
        '@PSR12' => true,
        '@PhpCsFixer' => true,
        // PHP arrays should be declared using the configured syntax.
        'array_syntax' => ['syntax'=>'long'],
        // Binary operators should be surrounded by space as configured.
        'binary_operator_spaces' => true,
        // Class static references `self`, `static` and `parent` MUST be in lower case.
        'lowercase_static_reference' => true,
    ])
    ->setFinder(PhpCsFixer\Finder::create()
        ->exclude('vendor')
        ->in(__DIR__)
    )
;