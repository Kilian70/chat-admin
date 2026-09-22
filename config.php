<?php
return [
    'id' => 'chat-admin',
    'class' => 'kilian\chatadmin\Module',
    'namespace' => 'kilian\chatadmin',
    'events' => [
        ['class' => 'humhub\modules\mail\widgets\ConversationSettingsMenu', 'event' => 'afterRun', 'callback' => ['kilian\chatadmin\Events', 'onMenu']],
    ],
];
