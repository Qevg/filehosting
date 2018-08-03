<?php

namespace Filehosting\Tests\Functional\Controller;

use FunctionalTester;

class FileControllerCest
{
    public function _before(FunctionalTester $I)
    {
    }

    public function _after(FunctionalTester $I)
    {
    }

    public function testFilePageOpens(FunctionalTester $I)
    {
        $I->wantTo('Test that file page opens and "testfile" exist');
        $I->amOnPage('/file/testfile');
        $I->seeResponseCodeIs(200);
        $I->see('test.jpg');
        $I->seeInDatabase('files', array('name' => 'testfile'));
    }
}
