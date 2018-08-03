<?php

namespace Filehosting\Tests\Functional\Controller;

use FunctionalTester;

class FilesControllerCest
{
    public function _before(FunctionalTester $I)
    {
    }

    public function _after(FunctionalTester $I)
    {
    }

    public function testFilesPageOpens(FunctionalTester $I)
    {
        $I->wantTo('Test that files page opens');
        $I->amOnPage('/files');
        $I->seeResponseCodeIs(200);
        $I->see('Файлы');
    }

    public function testSearch(FunctionalTester $I)
    {
        $I->wantTo('Test that the file "test.jpg" is found');
        $I->amOnPage('/files?query=test.jpg');
        $I->seeResponseCodeIs(200);
        $I->see('test.jpg');
    }
}
