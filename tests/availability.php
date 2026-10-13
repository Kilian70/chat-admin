<?php
declare(strict_types=1);

// Uses the actual Yii exception and HumHub controller classes. Authentication,
// module version and schema readiness are doubles; no database is connected.
$humhubRoot = isset($argv[1]) ? realpath($argv[1]) : false;
if ($humhubRoot === false || !is_file($humhubRoot . '/protected/vendor/autoload.php')) {
    fwrite(STDERR, "A prepared HumHub checkout is required.\n");
    exit(1);
}
require $humhubRoot . '/protected/vendor/autoload.php';
require $humhubRoot . '/protected/vendor/yiisoft/yii2/Yii.php';
$moduleRoot = dirname(__DIR__);
spl_autoload_register(static function (string $class) use ($moduleRoot, $humhubRoot): void {
    foreach (['kilian\\chatadmin\\' => $moduleRoot, 'humhub\\' => $humhubRoot . '/protected/humhub'] as $prefix => $base) {
        if (str_starts_with($class, $prefix)) {
            $path = $base . '/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
            if (is_file($path)) {
                require $path;
            }
            return;
        }
    }
});

class AvailabilityIdentity
{
    public function __construct(public bool $admin) {}
    public function isSystemAdmin(): bool { return $this->admin; }
}
class AvailabilityUser extends yii\base\Component
{
    public bool $isGuest = false;
    public bool $impersonated = false;
    public AvailabilityIdentity $identity;
    public function getImpersonation(): object
    {
        return new class($this->impersonated) {
            public function __construct(private bool $active) {}
            public function isActive(): bool { return $this->active; }
        };
    }
}
class AvailabilityMail extends yii\base\Module
{
    public string $version;
    public function getVersion(): string { return $this->version; }
}
class AvailabilitySchema
{
    public bool $ready = true;
    public function getTableSchema($name, $refresh = false): ?object
    {
        return $this->ready ? new stdClass() : null;
    }
}
class AvailabilityDatabase extends yii\base\Component
{
    public AvailabilitySchema $schema;
    public int $transactions = 0;
    public function beginTransaction()
    {
        $this->transactions++;
        throw new RuntimeException('Denied requests must not start a transaction.');
    }
}

$app = new yii\console\Application(['id' => 'availability-regression', 'basePath' => $moduleRoot]);
$user = new AvailabilityUser();
$database = new AvailabilityDatabase();
$database->schema = new AvailabilitySchema();
$app->set('user', $user);
$app->set('db', $database);
$controller = new kilian\chatadmin\controllers\ParticipantsController('participants', new yii\base\Module('chat-admin'));
$action = new yii\base\InlineAction('remove', $controller, 'actionRemove');

// version, audit-ready, admin, guest, impersonated, expected HTTP status
$cases = [
    ['3.4.7', true, true, false, false, 503],
    ['9.9.9', true, true, false, false, 503],
    [null, true, true, false, false, 503],
    ['3.4.5', false, true, false, false, 503],
    ['3.4.6', false, true, false, false, 503],
    ['3.3.13', false, true, false, false, 503],
    ['3.4.7', false, false, false, false, 403],
    ['3.4.7', false, true, true, false, 403],
    ['3.4.5', true, true, false, true, 403],
];
$count = 0;
foreach ($cases as [$version, $auditReady, $admin, $guest, $impersonated, $expected]) {
    $app->setModule('mail', $version === null ? null : new AvailabilityMail('mail', $app, ['version' => $version]));
    $database->schema->ready = $auditReady;
    $user->identity = new AvailabilityIdentity($admin);
    $user->isGuest = $guest;
    $user->impersonated = $impersonated;
    foreach ([
        'controller' => static fn() => $controller->beforeAction($action),
        'service' => static fn() => (new kilian\chatadmin\services\ParticipantRemoval())->remove(1, 2),
    ] as $path => $call) {
        try {
            $call();
        } catch (yii\web\HttpException $exception) {
            if ($exception->statusCode !== $expected) {
                throw new RuntimeException("Unexpected status on $path: {$exception->statusCode}, expected $expected");
            }
            $count++;
            continue;
        }
        throw new RuntimeException("Expected HTTP $expected from $path");
    }
}
if ($database->transactions !== 0) {
    throw new RuntimeException('A denied request started a transaction.');
}
echo "$count Controller-/Dienst-Sperrprüfungen bestanden; keine Transaktion gestartet.\n";
