<?php

namespace app\controllers;

use app\models\User;
use app\models\Token;
use Yii;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;
use yii\base\ActionFilter;

class UserController extends ActiveController
{
    public $modelClass = 'app\models\User';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
        ];
        return $behaviors;
    }

    public function beforeAction($action)
    {
        $authHeader = Yii::$app->request->headers->get('Authorization');
        if (!$authHeader || strpos($authHeader, 'Bearer ') !== 0) {
            throw new UnauthorizedHttpException('No token provided.');
        }
        $tokenString = substr($authHeader, 7);
        $token = Token::findOne(['token' => $tokenString]);
        if (!$token || strtotime($token->expires_at) < time()) {
            throw new UnauthorizedHttpException('Invalid or expired token.');
        }
        $user = User::findOne($token->user_id);
        if (!$user) {
            throw new UnauthorizedHttpException('User not found.');
        }
        if ($user->role != 1) {
            throw new UnauthorizedHttpException('You do not have permission to access this resource.');
        }
        return parent::beforeAction($action);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->load(Yii::$app->request->getBodyParams(), '');
        if (!empty($model->password)) {
            $model->password_hash = Yii::$app->security->generatePasswordHash($model->password);
        }
        if ($model->save()) {
            return $model;
        }
        return $model->errors;
    }

    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException("User with ID $id not found.");
    }
}
