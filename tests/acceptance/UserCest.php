<?php

namespace Filehosting\Tests\Acceptance;

use AcceptanceTester;

class UserCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function testSuccessfulRegistration(AcceptanceTester $I)
    {
        $I->wantTo('Test, that registration works');
        $I->amOnPage('/register');
        $I->fillField('name', 'testregister');
        $I->fillField('email', 'testregister@example.com');
        $I->fillField('password', 'qwerty123456');
        $I->click('Зарегистрироваться');
        sleep(1);
        $I->seeCurrentUrlEquals('/');
        $I->see('testregister');
        $I->seeCookie('auth');
        $I->seeInDatabase('users', ['email' => 'testregister@example.com']);
    }

    public function testUnsuccessfulRegistration(AcceptanceTester $I)
    {
        $I->wantTo('Test, that registration data is invalid');
        $I->amOnPage('/register');
        $I->fillField('name', 'testregister');
        $I->fillField('email', 'testregister@example.com');
        $I->fillField('password', '123');
        $I->click('Зарегистрироваться');
        sleep(1);
        $I->see('Пароль должнен содержать не меньше 8 и не больше 255 символов');
    }

    public function testRegistrationIfEmailIsNotUnique(AcceptanceTester $I)
    {
        $I->wantTo('Test, that registration does not work if email is not unique');
        $I->amOnPage('/register');
        $I->fillField('name', 'testregister');
        $I->fillField('email', 'testuser@example.com');
        $I->fillField('password', '12345678');
        $I->click('Зарегистрироваться');
        sleep(1);
        $I->see('Пользователь с таким email уже существует');
    }

    public function testLogIn(AcceptanceTester $I)
    {
        $I->wantTo('Test, that log in works');
        $I->amOnPage('/login');
        $I->fillField('email', 'testuser@example.com');
        $I->fillField('password', '12345678');
        $I->click('Войти');
        sleep(1);
        $I->seeCurrentUrlEquals('/');
        $I->see('testuser');
        $I->seeCookie('auth');
    }

    public function testUnsuccessfulLogIn(AcceptanceTester $I)
    {
        $I->wantTo('Test, that log in data is invalid');
        $I->amOnPage('/login');
        $I->fillField('email', 'testuser@example.com');
        $I->fillField('password', 'qwerty');
        $I->click('Войти');
        sleep(1);
        $I->see('Не удалось войти. Пожалуйста, проверьте правильность логина и пароля.');
    }
}
