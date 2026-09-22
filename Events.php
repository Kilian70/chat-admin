<?php
namespace kilian\chatadmin;

use kilian\chatadmin\services\Access;
use yii\helpers\Html;
use yii\base\WidgetEvent;

final class Events
{
    public static function onMenu(WidgetEvent $event): void
    {
        if (!Access::isAdmin() || !Access::isCompatible() || !Access::auditReady()) {
            return;
        }
        $message = $event->sender->message;
        if ($message === null || !$message->isParticipant()) {
            return;
        }
        $position = strrpos($event->result, '</ul>');
        if ($position === false) {
            return;
        }
        $link = Html::tag('li', Html::a('Teilnehmer verwalten', ['/chat-admin/participants/index', 'id' => (int)$message->id], [
            'class' => 'dropdown-item', 'data-pjax' => '0',
        ]));
        $event->result = substr_replace($event->result, $link, $position, 0);
    }
}
