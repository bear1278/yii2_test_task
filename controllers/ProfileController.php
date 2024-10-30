<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\Token;
use yii\rest\Controller;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

class ProfileController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
        ];
        return $behaviors;
    }

    public function actionMe()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
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
        return [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->role,
        ];
    }
}
