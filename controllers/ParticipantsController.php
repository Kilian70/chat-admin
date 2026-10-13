<?php
namespace kilian\chatadmin\controllers;

use humhub\components\Controller;
use humhub\modules\mail\models\Message;
use kilian\chatadmin\services\Access;
use kilian\chatadmin\services\ParticipantRemoval;
use Yii;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\HttpException;

class ParticipantsController extends Controller
{
    protected function getAccessRules()
    {
        $access = \humhub\components\access\ControllerAccess::class;
        $rules = [[$access::RULE_LOGGED_IN_ONLY], [$access::RULE_ADMIN_ONLY]];
        if (defined($access . '::RULE_DENY_IMPERSONATED')) {
            $rules[] = [constant($access . '::RULE_DENY_IMPERSONATED')];
        }
        return $rules;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['chatAdminVerbs'] = ['class' => VerbFilter::class, 'actions' => ['remove' => ['POST']]];
        return $behaviors;
    }

    public function beforeAction($action)
    {
        if (!Access::isAdmin()) {
            throw new ForbiddenHttpException();
        }
        if (!Access::isCompatible() || !Access::auditReady()) {
            throw new HttpException(503, 'Bitte Messenger 3.3.13, 3.4.5 oder 3.4.6 und die Chat-Admin-Datenbankmigration prüfen.');
        }
        return parent::beforeAction($action);
    }

    public function actionIndex($id)
    {
        $message = Message::findOne((int)$id);
        if ($message === null || !$message->isParticipant()) {
            throw new NotFoundHttpException();
        }
        $participants = $message->getUsers()->with('profile')->all();
        $audit = (new \yii\db\Query())->from('{{%chat_admin_audit}}')
            ->where(['message_id' => $message->id])->orderBy(['id' => SORT_DESC])->limit(20)->all();
        return $this->render('index', compact('message', 'participants', 'audit'));
    }

    public function actionRemove($id)
    {
        $targetId = filter_var(Yii::$app->request->post('user_id'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($targetId === false) {
            throw new ForbiddenHttpException();
        }
        (new ParticipantRemoval())->remove((int)$id, $targetId);
        Yii::$app->session->setFlash('success', 'Person aus der Unterhaltung entfernt.');
        return $this->redirect(['index', 'id' => (int)$id]);
    }
}
