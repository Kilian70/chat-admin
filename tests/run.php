<?php
require dirname(__DIR__) . '/services/RemovalPolicy.php';
use kilian\chatadmin\services\RemovalPolicy;

$cases = [
    [[true, true, 1, 2, true, 3], true],
    [[false, true, 1, 2, true, 3], false],
    [[true, false, 1, 2, true, 3], false],
    [[true, true, 1, 1, true, 3], false],
    [[true, true, 1, 2, false, 3], false],
    [[true, true, 1, 2, true, 1], false],
    [[true, true, 0, 2, true, 3], false],
    [[true, true, 1, 0, true, 3], false],
    [[true, true, 1, 2, true, 2], true],
];
foreach ($cases as [$arguments, $expected]) {
    if (RemovalPolicy::allows(...$arguments) !== $expected) {
        throw new RuntimeException('Removal policy failed: ' . json_encode($arguments));
    }
}
echo "9 Berechtigungsregeln bestanden.\n";
