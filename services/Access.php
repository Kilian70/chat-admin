<?php
namespace kilian\chatadmin\services;

use Yii;

final class Access
{
    public static function isAdmin(): bool
    {
        $user = Yii::$app->user;
        if ($user->isGuest || !$user->identity->isSystemAdmin()) {
            return false;
        }
        // Never permit participant management while impersonating another user,
        // even when the installation permits private-content impersonation.
        if (method_exists($user, 'getImpersonation')) {
            return !$user->getImpersonation()->isActive();
        }
        $mail = Yii::$app->getModule('mail');
        return $mail !== null && $mail->getVersion() === '3.3.13';
    }

    public static function isCompatible(): bool
    {
        $mail = Yii::$app->getModule('mail');
        // Fail closed after Messenger updates until that version is verified.
        return $mail !== null && in_array($mail->getVersion(), ['3.3.13', '3.4.5'], true);
    }

    public static function auditReady(): bool
    {
        return Yii::$app->db->schema->getTableSchema('{{%chat_admin_audit}}', true) !== null;
    }
}
