<?php

namespace Filehosting\Tests\Functional\Controller;

use FunctionalTester;

class AuthControllerCest
{
    public function _before(FunctionalTester $I)
    {
    }

    public function _after(FunctionalTester $I)
    {
    }

    public function testRegisterPageOpens(FunctionalTester $I)
    {
        $I->wantTo('Test that register page opens');
        $I->amOnPage('/register');
        $I->seeResponseCodeIs(200);
        $I->see('Регистрация');
        $I->see('Зарегистрироваться');
    }

    public function testLoginPageOpens(FunctionalTester $I)
    {
        $I->wantTo('Test that login page opens');
        $I->amOnPage('/login');
        $I->seeResponseCodeIs(200);
        $I->see('Авторизация');
        $I->see('Войти');
    }
}
