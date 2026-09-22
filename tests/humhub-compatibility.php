<?php
declare(strict_types=1);

$humhubRoot = isset($argv[1]) ? realpath($argv[1]) : false;
$humhubVersion = $argv[2] ?? '';
$moduleRoot = dirname(__DIR__);

if ($humhubRoot === false || !is_file($humhubRoot . '/protected/vendor/autoload.php')) {
    fwrite(STDERR, "A prepared HumHub checkout is required.\n");
    exit(1);
}

require $humhubRoot . '/protected/vendor/autoload.php';
require $humhubRoot . '/protected/vendor/yiisoft/yii2/Yii.php';

spl_autoload_register(static function (string $class) use ($moduleRoot, $humhubRoot): void {
    $prefix = 'kilian\\chatadmin\\';
    if (str_starts_with($class, $prefix)) {
        $relative = str_replace('\\', '/', substr($class, strlen($prefix)));
        $path = $moduleRoot . '/' . $relative . '.php';
    } elseif (str_starts_with($class, 'humhub\\')) {
        $relative = str_replace('\\', '/', substr($class, strlen('humhub\\')));
        $path = $humhubRoot . '/protected/humhub/' . $relative . '.php';
    } else {
        return;
    }
    if (is_file($path)) {
        require $path;
    }
});

$manifest = json_decode(file_get_contents($moduleRoot . '/module.json'), true, 512, JSON_THROW_ON_ERROR);
if ($humhubVersion === '' || version_compare($humhubVersion, $manifest['humhub']['minVersion'], '<')) {
    fwrite(STDERR, "HumHub {$humhubVersion} is below the declared minimum version.\n");
    exit(1);
}

if (!class_exists('kilian\\chatadmin\\Module')) {
    fwrite(STDERR, "Module class cannot be loaded against HumHub {$humhubVersion}.\n");
    exit(1);
}

$config = require $moduleRoot . '/config.php';
if (($config['id'] ?? null) !== 'chat-admin') {
    fwrite(STDERR, "The HumHub module configuration is invalid.\n");
    exit(1);
}

echo "Chat Admin classes and configuration are compatible with HumHub {$humhubVersion}.\n";
