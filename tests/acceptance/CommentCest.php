<?php

namespace Filehosting\Tests\Acceptance;

use AcceptanceTester;

class CommentCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function addComment(AcceptanceTester $I)
    {
        $I->wantTo('Test, that comment is add');
        $I->amOnPage('/file/testfile');
        $I->fillField('comment-text', 'test-comment');
        $I->click('Отправить');
        sleep(1);
        $I->see('test-comment');
    }

    public function replyComment(AcceptanceTester $I)
    {
        $I->wantTo('Test, that comment reply is work');
        $I->amOnPage('/file/testfile');
        $I->click('//input[@id=\'comment-reply-2\']/preceding::span[1]'); //xpath предыдущий элемент(span) от элемента(input[id='comment-reply-2]')
        $I->fillField('comment-text', 'второй.первый');
        $I->click('Отправить');
        sleep(1);
        $I->see('второй.первый');
    }

    public function showAllComments(AcceptanceTester $I)
    {
        $I->wantTo('Test, that show all comments work');
        $I->amOnPage('/files');
        $I->dontSee('третий');
        $I->click('Показать все коментарии');
        sleep(1);
        $I->see('третий');
    }

    public function testXSSAttackComments(AcceptanceTester $I)
    {
        $I->wantTo('Test, that xss attack in the comments does not work');
        $I->amOnPage('/file/testfile');
        $I->fillField('comment-text', '<script>alert(1);</script>');
        $I->click('Отправить');
        sleep(1);
        $I->see('<script>alert(1);</script>');
    }
}
