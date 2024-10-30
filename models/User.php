<?php

namespace app\models;

use yii\db\ActiveRecord;
use Yii;

class User extends ActiveRecord
{
    public $password;

    public static function tableName()
    {
        return 'user';
    }

    public function rules()
    {
        return [
            [['username', 'email', 'role'], 'required'],
            ['email', 'email'],
            ['email', 'unique'],
            ['username', 'string', 'max' => 255],
            ['password', 'string', 'min' => 6],
            ['password', 'required', 'on' => 'create'],
            ['role', 'in', 'range' => [0, 1]],
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (!empty($this->password)) {
                $this->password_hash = Yii::$app->security->generatePasswordHash($this->password);
            }
            return true;
        }
        return false;
    }
}
