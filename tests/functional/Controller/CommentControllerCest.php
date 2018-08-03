<?php

namespace Filehosting\Tests\Functional\Controller;

use FunctionalTester;

class CommentControllerCest
{
    public function _before(FunctionalTester $I)
    {
    }

    public function _after(FunctionalTester $I)
    {
    }

    public function testAddComment(FunctionalTester $I)
    {
        $I->wantTo('Test that the comment is added');
        $I->amOnPage('/file/testfile');
        $I->seeResponseCodeIs(200);
        $I->submitForm('#form-comments-testfile', array('comment-text' => 'test comment'));
        $I->seeInDatabase('comments', array('text' => 'test comment'));
    }

    public function testGetAllComments(FunctionalTester $I)
    {
        $I->wantTo('Test that received all comments');
        $I->amOnPage('/');
        $I->seeResponseCodeIs(200);
        $csrfName = $I->grabValueFrom('#csrfName');
        $csrfValue = $I->grabValueFrom('#csrfValue');
        $I->sendPOST('/getAllComments/testfile', array('csrf_name' => $csrfName, 'csrf_value' => $csrfValue));
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('status' => 'success'));
        $I->seeResponseContainsJson(array('id' => '4'));
    }
}
