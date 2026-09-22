<?php
namespace kilian\chatadmin\services;

use humhub\modules\mail\models\Message;
use humhub\modules\mail\models\UserMessage;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\ServiceUnavailableHttpException;

final class ParticipantRemoval
{
    public function remove(int $messageId, int $targetId): void
    {
        if (!Access::isAdmin()) {
            throw new ForbiddenHttpException();
        }
        if (!Access::isCompatible() || !Access::auditReady()) {
            throw new ServiceUnavailableHttpException('Messenger-Version oder Protokolltabelle ist nicht bereit.');
        }
        $actorId = (int)Yii::$app->user->id;
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            // Serialize removals from this extension; never delete the conversation.
            $table = $db->quoteTableName(Message::tableName());
            $db->createCommand("SELECT id FROM $table WHERE id = :id FOR UPDATE", [':id' => $messageId])->queryScalar();
            $message = Message::findOne($messageId);
            $target = UserMessage::findOne(['message_id' => $messageId, 'user_id' => $targetId]);
            if ($message === null || !RemovalPolicy::allows(
                Access::isAdmin(),
                UserMessage::find()->where(['message_id' => $messageId, 'user_id' => $actorId])->exists(),
                $actorId, $targetId, $target !== null,
                (int)UserMessage::find()->where(['message_id' => $messageId])->count()
            )) {
                throw new ForbiddenHttpException('Diese Person kann nicht aus dieser Unterhaltung entfernt werden.');
            }
            // Same AR path as Messenger leave(): retains messages and invokes its
            // participant-left event plus unread-cache invalidation.
            if ($target->delete() !== 1) {
                throw new \RuntimeException('Teilnehmerzuordnung konnte nicht entfernt werden.');
            }
            $db->createCommand()->insert('{{%chat_admin_audit}}', [
                'message_id' => $messageId, 'actor_id' => $actorId,
                'target_id' => $targetId, 'created_at' => gmdate('Y-m-d H:i:s'),
            ])->execute();
            $transaction->commit();
        } catch (\Throwable $exception) {
            $transaction->rollBack();
            throw $exception;
        }
    }
}
