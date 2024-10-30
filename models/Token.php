<?php

namespace app\models;

use yii\db\ActiveRecord;

class Token extends ActiveRecord
{
    public static function tableName()
    {
        return 'token';
    }

    public function rules()
    {
        return [
            [['user_id', 'token', 'expires_at', 'created_at'], 'required'],
            ['token', 'string'],
            ['token', 'unique'],
            ['expires_at', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            ['created_at', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function fields()
    {
        return ['token', 'expires_at'];
    }
}
