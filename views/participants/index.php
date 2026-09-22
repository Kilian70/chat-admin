<?php
use yii\helpers\Html;

$this->title = 'Chat-Teilnehmer verwalten';
?>
<div class="card">
    <div class="card-header"><strong><?= Html::encode($this->title) ?></strong></div>
    <div class="card-body">
        <h1 class="h5"><?= Html::encode($message->title ?: 'Unterhaltung') ?></h1>
        <p>Entfernte Personen haben keinen weiteren Zugriff auf diese Unterhaltung. Vorhandene Nachrichten bleiben erhalten. Ein späteres erneutes Hinzufügen ist weiterhin möglich.</p>
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Person</th><th>Aktion</th></tr></thead>
                <tbody>
                <?php foreach ($participants as $participant): ?>
                    <tr>
                        <td><?= Html::encode($participant->displayName) ?></td>
                        <td>
                        <?php if ((int)$participant->id === (int)Yii::$app->user->id): ?>
                            Eigener Zugang – geschützt
                        <?php else: ?>
                            <?= Html::beginForm(['remove', 'id' => $message->id], 'post') ?>
                            <?= Html::hiddenInput('user_id', $participant->id) ?>
                            <?= Html::submitButton('Entfernen', [
                                'class' => 'btn btn-sm btn-danger',
                                'data-confirm' => $participant->displayName . ' wirklich aus dieser Unterhaltung entfernen?',
                            ]) ?>
                            <?= Html::endForm() ?>
                        <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?= Html::a('Zurück zum Chat', ['/mail/mail/index', 'id' => $message->id], ['class' => 'btn btn-secondary']) ?>
        <?php if ($audit): ?>
            <h2 class="h6">Letzte Entfernungen (UTC)</h2>
            <ul>
            <?php foreach ($audit as $entry): ?>
                <li><?= Html::encode($entry['created_at']) ?> · Benutzer-ID <?= (int)$entry['actor_id'] ?> hat Benutzer-ID <?= (int)$entry['target_id'] ?> entfernt.</li>
            <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
