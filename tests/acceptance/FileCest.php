<?php

namespace Filehosting\Tests\Acceptance;

use AcceptanceTester;

class FileCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
        $I->cleanSphinxSearch();
    }

    public function testAnonUploadFile(AcceptanceTester $I)
    {
        $I->wantTo('Test, that the file is upload and an anonymous user can manage file');
        $I->amOnPage('/');
        $I->seeElementInDOM('input', ['name' => 'file']);
        $I->attachFile('file', 'testfile');
        sleep(1);
        $I->seeElement('#load-file-success');
        $I->click('#go-to-file');
        sleep(1);
        $I->see('testfile');
        $I->seeInDatabase('files', ['original_name' => 'testfile']);
        $I->click('.file__three-dots');
        $I->see('Редактировать');
    }

    public function testAuthUploadFile(AcceptanceTester $I)
    {
        $I->wantTo('Test, that the file is upload and an auth user can manage file');
        $I->amOnPage('/');
        $I->setCookie('auth', '013ef89f6d17841a2ac8c35b20q62b1c');
        $I->seeElementInDOM('input', ['name' => 'file']);
        $I->attachFile('file', 'testfile');
        sleep(1);
        $I->seeElement('#load-file-success');
        $I->click('#go-to-file');
        sleep(1);
        $I->see('testfile');
        $I->seeInDatabase('files', ['original_name' => 'testfile']);
        $I->click('.file__three-dots');
        $I->see('Редактировать');
    }

    public function testUploadFileAndUpdateData(AcceptanceTester $I)
    {
        $I->wantTo('Test, that the file is upload and file data is add');
        $I->amOnPage('/');
        $I->seeElementInDOM('input', ['name' => 'file']);
        $I->attachFile('file', 'testfile');
        sleep(1);
        $I->seeElement('#load-file-success');
        $I->fillField('description', 'test upload and update');
        $I->click('Сохранить');
        sleep(1);
        $I->see('test upload and update');
        $I->see('testfile');
        $I->seeInDatabase('files', ['original_name' => 'testfile', 'description' => 'test upload and update']);
    }

    public function testUploadAndRemoveFile(AcceptanceTester $I)
    {
        $I->wantTo('Test, that the file is upload and remove');
        //upload
        $I->amOnPage('/');
        $I->seeElementInDOM('input', ['name' => 'file']);
        $I->attachFile('file', 'testfile');
        sleep(1);
        $I->seeElement('#load-file-success');
        $I->click('#go-to-file');
        sleep(1);
        $I->see('testfile');
        $I->seeInDatabase('files', ['original_name' => 'testfile']);
        //remove
        $I->click('.file__three-dots');
        $I->click('Удалить');
        sleep(1);
        $I->seeInPopup('Файл успешно удален');
        $I->acceptPopup();
        $I->dontSeeInDatabase('files', ['original_name' => 'testfile']);
    }

    public function testUploadAndRemoveFileWithComments(AcceptanceTester $I)
    {
        $I->wantTo('Test, that file upload and file with comments is remove. Test, that bug is not repeated #5a21ef7');
        //upload
        $I->amOnPage('/');
        $I->seeElementInDOM('input', ['name' => 'file']);
        $I->attachFile('file', 'testfile');
        sleep(1);
        $I->seeElement('#load-file-success');
        $I->click('#go-to-file');
        sleep(1);
        $I->see('testfile');
        $I->seeInDatabase('files', ['original_name' => 'testfile']);
        //add comments
        $I->haveInDatabase('comments', ['file_id' => '2', 'parent_id' => null, 'user_id' => 1, 'text' => 'первый', 'matpath' => '001']);
        $I->haveInDatabase('comments', ['file_id' => '2', 'parent_id' => null, 'user_id' => null, 'text' => 'второй', 'matpath' => '002']);
        $I->haveInDatabase('comments', ['file_id' => '2', 'parent_id' => 1, 'user_id' => null, 'text' => 'первый.первый', 'matpath' => '001.001']);
        $I->haveInDatabase('comments', ['file_id' => '2', 'parent_id' => 1, 'user_id' => null, 'text' => 'первый.второй', 'matpath' => '001.002']);
        $I->reloadPage();
        $I->see('первый.первый');
        //remove
        $I->click('.file__three-dots');
        $I->click('Удалить');
        sleep(1);
        $I->seeInPopup('Файл успешно удален');
        $I->acceptPopup();
        $I->dontSeeInDatabase('files', ['original_name' => 'testfile']);
        $I->dontSeeInDatabase('comments', ['file_id' => '2', 'parent_id' => null, 'user_id' => 1, 'text' => 'первый', 'matpath' => '001']);
        $I->dontSeeInDatabase('comments', ['file_id' => '2', 'parent_id' => 1, 'user_id' => null, 'text' => 'первый.второй', 'matpath' => '001.002']);
    }

    public function testUpdateFileData(AcceptanceTester $I)
    {
        $I->wantTo('Test, that the file data is update');
        $I->amOnPage('/');
        $I->setCookie('auth', '013ef89f6d17841a2ac8c35b20q62b1c');
        $I->seeInDatabase('files', ['name' => 'testfile']);
        $I->amOnPage('/file/testfile');
        $I->click('.file__three-dots');
        $I->click('Редактировать');
        $I->fillField('description', 'test update file data');
        $I->click('Сохранить');
        $I->see('test update file data');
        $I->seeInDatabase('files', ['original_name' => 'test.jpg', 'description' => 'test update file data']);
    }

    public function testXSSAttackFileData(AcceptanceTester $I)
    {
        $I->wantTo('Test, that xss attack in the file data does not work');
        $I->amOnPage('/');
        $I->setCookie('auth', '013ef89f6d17841a2ac8c35b20q62b1c');
        $I->seeInDatabase('files', ['name' => 'testfile']);
        $I->amOnPage('/file/testfile');
        $I->click('.file__three-dots');
        $I->click('Редактировать');
        $I->fillField('description', '<script>alert(1);</script>');
        $I->click('Сохранить');
        $I->see('<script>alert(1);</script>');
        $I->seeInDatabase('files', ['original_name' => 'test.jpg', 'description' => '<script>alert(1);</script>']);
    }

    public function testDownloadFile(AcceptanceTester $I)
    {
        $I->wantTo('Test, that the file is download');
        $I->amOnPage('/file/testfile');
        $I->seeInDatabase('files', ['original_name' => 'test.jpg', 'downloads' => '0']);
        $I->click('Скачать');
        sleep(3);
        $I->seeInDatabase('files', ['original_name' => 'test.jpg', 'downloads' => '1']);
    }

    public function testUploadFileExceedingMaxSize(AcceptanceTester $I)
    {
        $I->wantTo('Test, that the file exceeding max size file is not upload');
        $I->amOnPage('/');
        $I->seeElementInDOM('input', ['name' => 'file']);
        $I->attachFile('file', 'big_testfile');
        sleep(1);
        $I->see('Превышен максимально допустимый размер файла');
    }

    public function testCsrfErrorWhileLoadingFile(AcceptanceTester $I)
    {
        $I->wantTo('Test csrf error while loading file');
        $I->amOnPage('/');
        $I->seeElementInDOM('input', ['name' => 'file']);
        $I->setCookie('PHPSESSID', 'qwe');
        $I->attachFile('file', 'testfile');
        sleep(1);
        $I->see('При загрузке файла произошка ошибка. Попробуйте ещё раз');
    }
}
