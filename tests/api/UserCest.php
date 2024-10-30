<?php

namespace api;

use ApiTester;

class UserCest
{
    private $token;

    public function _before(ApiTester $I)
    {
        // Получаем токен для аутентификации администратора (используем при тестах для админ-эндпоинтов)
        $I->sendPOST('/login', [
            'username' => 'admin',
            'password' => 'admin_password',
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['token']);
        $this->token = $I->grabDataFromResponseByJsonPath('$.token')[0];
    }

    public function loginTest(ApiTester $I)
    {
        $I->sendPOST('/login', [
            'username' => 'user',
            'password' => 'user_password',
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['token']);
    }

    public function getMeTest(ApiTester $I)
    {
        $I->haveHttpHeader('Authorization', "Bearer {$this->token}");
        $I->sendGET('/me');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson([
            'username' => 'admin', // предполагаем, что имя администратора — admin
            'role' => 1, // роль администратора
        ]);
    }

    public function getUsersTest(ApiTester $I)
    {
        $I->haveHttpHeader('Authorization', "Bearer {$this->token}");
        $I->sendGET('/users');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function createUserTest(ApiTester $I)
    {
        $I->haveHttpHeader('Authorization', "Bearer {$this->token}");
        $I->sendPOST('/users', [
            'username' => 'new_user',
            'email' => 'new_user@example.com',
            'password' => 'password123',
            'role' => 0,
        ]);
        $I->seeResponseCodeIs(201);
        $I->seeResponseContainsJson(['username' => 'new_user']);
    }

    public function updateUserTest(ApiTester $I)
    {
        $I->haveHttpHeader('Authorization', "Bearer {$this->token}");
        $I->sendPATCH('/users/1', [
            'email' => 'updated_email@example.com',
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsJson(['email' => 'updated_email@example.com']);
    }

    public function deleteUserTest(ApiTester $I)
    {
        $I->haveHttpHeader('Authorization', "Bearer {$this->token}");
        $I->sendDELETE('/users/1');
        $I->seeResponseCodeIs(204);
    }
}
