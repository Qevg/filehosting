<?php

namespace Filehosting\Tests\Functional\Controller;

use FunctionalTester;

class UploadControllerCest
{
    public function _before(FunctionalTester $I)
    {
    }

    public function _after(FunctionalTester $I)
    {
    }

    public function testMainPageOpens(FunctionalTester $I)
    {
        $I->wantTo('Test that main page opens');
        $I->amOnPage('/');
        $I->seeResponseCodeIs(200);
        $I->see('Главная страница');
        $I->see('Выбрать файл');
    }
}
