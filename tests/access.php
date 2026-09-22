<?php
// Dependency doubles: these checks do not claim to replace a HumHub integration test.
class Yii { public static $app; }
class IdentityDouble {
    public bool $admin = true;
    public function isSystemAdmin(): bool { return $this->admin; }
}
class LegacyUserDouble {
    public bool $isGuest = false;
    public IdentityDouble $identity;
    public function __construct() { $this->identity = new IdentityDouble(); }
}
class CurrentUserDouble extends LegacyUserDouble {
    public bool $impersonated = false;
    public function getImpersonation(): object {
        return new class($this->impersonated) {
            public function __construct(private bool $active) {}
            public function isActive(): bool { return $this->active; }
        };
    }
}
class MailDouble {
    public function __construct(public string $version) {}
    public function getVersion(): string { return $this->version; }
}
class AppDouble {
    public $user;
    public $mail;
    public function getModule($id) { return $id === 'mail' ? $this->mail : null; }
}
require dirname(__DIR__) . '/services/Access.php';
use kilian\chatadmin\services\Access;

function check(bool $actual, bool $expected, string $label): void {
    if ($actual !== $expected) throw new RuntimeException($label);
}
Yii::$app = new AppDouble();
Yii::$app->user = new CurrentUserDouble();
foreach (['3.3.13', '3.4.5'] as $version) {
    Yii::$app->mail = new MailDouble($version);
    check(Access::isCompatible(), true, 'supported version ' . $version);
    check(Access::isAdmin(), true, 'normal admin ' . $version);
    Yii::$app->user->impersonated = true;
    check(Access::isAdmin(), false, 'impersonated ' . $version);
    Yii::$app->user->impersonated = false;
}
Yii::$app->user->identity->admin = false;
check(Access::isAdmin(), false, 'non-admin');
Yii::$app->user->identity->admin = true;
Yii::$app->user->isGuest = true;
check(Access::isAdmin(), false, 'guest');
Yii::$app->user->isGuest = false;
Yii::$app->mail = new MailDouble('3.4.6');
check(Access::isCompatible(), false, 'unreviewed version');
Yii::$app->mail = null;
check(Access::isCompatible(), false, 'missing Messenger');
Yii::$app->user = new LegacyUserDouble();
Yii::$app->mail = new MailDouble('3.3.13');
check(Access::isAdmin(), true, 'legacy supported session');
Yii::$app->mail = new MailDouble('3.4.5');
check(Access::isAdmin(), false, 'new Messenger without impersonation interface');
echo "12 Versions- und Zugriffsprüfungen bestanden.\n";
