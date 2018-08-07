<?php 

$I = new AcceptanceTester($scenario);

$I->wantTo('see Add User word in title ');
$I->amOnPage('/');
$I->see('Add User');
$I->fillField('#first_name','Test');
$I->fillField('#last_name','Test');
$I->fillField('#email','test');
$I->see('test is not a valid email');

$I->wantTo('see a form, fill the field and submit ');
$I->amOnPage('user/add');
$I->see('Add User');
$I->fillField('#first_name','Test2');
$I->fillField('#last_name','Test1');
$I->fillField('#email','test2@ymail.com');
$I->click('/html/body/div[1]/form/div[4]/div/button');
$I->see('Hello, Test2');

$I->wantTo('see a form, fill the field and submit with email already existed ');
$I->amOnPage('user/add');
$I->see('Add User');
$I->fillField('#first_name','Test1');
$I->fillField('#last_name','Test1');
$I->fillField('#email','test1@ymail.com');
$I->click('/html/body/div[1]/form/div[4]/div/button');
$I->see('email already exist in the database');

$I->wantTo('see edit form succesfully update');
$I->amOnPage('user/edit/1');
$I->see('Add User');
$I->fillField('#first_name','Ademola');
$I->fillField('#last_name','Raimi');
$I->fillField('#email','ademola.raimi@andela.com');
$I->click('/html/body/div[1]/form/div[4]/div/button');
$I->wait(2);
$I->see('Success! Ademola has been updated successfully');

$I->wantTo('see edit form fail due to validation');
$I->amOnPage('user/edit/1');
$I->see('Add User');
$I->fillField('#first_name','');
$I->fillField('#last_name','');
$I->fillField('#email','ademola.raimi@andela.com');
$I->click('/html/body/div[1]/form/div[4]/div/button');
$I->see('Value is required');
$I->fillField('#first_name','Ademola');
$I->fillField('#last_name','Raimi');
$I->fillField('#email','test1@');
$I->see('test1@ is not a valid email');

$I->wantTo('delete a user');
$I->amOnPage('user/list');
$I->click('//*[@id="example"]/tbody/tr[3]/td[5]/a[2]');
$I->see('Are you sure you want to delete this user?');
$I->click('body > div.swal-overlay.swal-overlay--show-modal > div > div.swal-footer > div:nth-child(2) > button');
$I->wait(2);
$I->see('Success! User succesfully deleted');
