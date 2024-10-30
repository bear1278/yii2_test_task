<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use app\models\User;

class UserController extends Controller
{
    /**
     * Регистрирует нового пользователя.
     * Команда: php yii user/register <username> <email> <password> <role>
     *
     * @param string $username Имя пользователя
     * @param string $email Email пользователя
     * @param string $password Пароль пользователя
     * @param string $role Роль пользователя (admin или user)
     * @return int Exit code
     */
    public function actionRegister($username, $email, $password, $role)
    {
        if (!in_array($role, [1, 0])) {
            echo "Invalid role. Allowed roles: 1, 0.\n";
            return ExitCode::DATAERR;
        }
        $user = new User();
        $user->username = $username;
        $user->email = $email;
        $user->password_hash = Yii::$app->security->generatePasswordHash($password);
        $user->role = $role;
        $user->created_at = date('Y-m-d H:i:s');
        $user->updated_at = date('Y-m-d H:i:s');
        if ($user->save()) {
            echo "User '{$username}' has been successfully registered.\n";
            return ExitCode::OK;
        } else {
            echo "Error registering user:\n";
            foreach ($user->errors as $error) {
                echo "- " . implode(", ", $error) . "\n";
            }
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }
}
