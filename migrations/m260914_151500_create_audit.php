<?php
use humhub\components\Migration;

class m260914_151500_create_audit extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%chat_admin_audit}}', [
            'id' => $this->primaryKey(),
            'message_id' => $this->integer()->notNull(),
            'actor_id' => $this->integer()->notNull(),
            'target_id' => $this->integer()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
        ]);
        $this->createIndex('idx-chat-admin-audit-message', '{{%chat_admin_audit}}', ['message_id', 'id']);
    }

    public function safeDown()
    {
        $this->dropTable('{{%chat_admin_audit}}');
    }
}
