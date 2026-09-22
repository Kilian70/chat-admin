<?php
declare(strict_types=1);
$humhub = $argv[1] ?? null;
$expected = $argv[2] ?? null;
if (!$humhub || !is_dir($humhub)) { fwrite(STDERR, "HumHub checkout not found.\n"); exit(1); }
$autoload = $humhub . '/vendor/autoload.php';
if (!is_file($autoload)) { fwrite(STDERR, "HumHub vendor/autoload.php not found.\n"); exit(1); }
require $autoload;
$root = dirname(__DIR__);
require_once $root . '/Module.php';
if (!class_exists('kilian\\chatadmin\\Module')) { fwrite(STDERR, "Module class could not be loaded.\n"); exit(1); }
$config = require $root . '/config.php';
if (($config['id'] ?? null) !== 'chat-admin') { fwrite(STDERR, "Unexpected module id.\n"); exit(1); }
echo "Chat Admin compatibility smoke test passed" . ($expected ? " for HumHub {$expected}" : "") . ".\n";
