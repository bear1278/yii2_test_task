<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\Token;
use yii\rest\Controller;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

class LoginController extends Controller
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

    public function actionLogin()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $email  = $request->post('email');
        $password = $request->post('password');
        $user = User::findOne(['email' => $email]);
        if ($user && Yii::$app->security->validatePassword($password, $user->password_hash)) {
            $token = new Token();
            $token->user_id = $user->id;
            $token->token = Yii::$app->security->generateRandomString();
            $token->expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $token->created_at = date('Y-m-d H:i:s');
            if ($token->save()) {
                return [
                    'token' => $token->token,
                    'expires_at' => $token->expires_at,
                ];
            } else {
                return ['error' => 'Unable to save token.'];
            }
        } else {
            throw new UnauthorizedHttpException('Invalid username or password.');
        }
    }
}