<?php

namespace tests\unit\models;

use app\models\User;
use app\tests\unit\fixtures\models\UserFixture;

class UserTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    public function _fixtures()
    {
        return [
            'users' => UserFixture::class
        ];
    }
    public function testFindUserById()
    {
        verify($user = User::findIdentity(1))->notEmpty();
        verify($user->username)->equals('nigger');
        verify(User::findIdentity(999))->null();
        verify($user = User::findIdentity(2))->notEmpty();
        verify($user->username)->equals('napoleon');
    }

    // public function testFindUserByAccessToken()
    // {
    //     expect_that($user = User::findIdentityByAccessToken('100-token'));
    //     expect($user->username)->equals('admin');

    //     expect_not(User::findIdentityByAccessToken('non-existing'));
    // }

    // public function testFindUserByUsername()
    // {
    //     expect_that($user = User::findByUsername('admin'));
    //     expect_not(User::findByUsername('not-admin'));
    // }

    // /**
    //  * @depends testFindUserByUsername
    //  */
    // public function testValidateUser($user)
    // {
    //     $user = User::findByUsername('admin');
    //     expect_that($user->validateAuthKey('test100key'));
    //     expect_not($user->validateAuthKey('test102key'));

    //     expect_that($user->validatePassword('admin'));
    //     expect_not($user->validatePassword('123456'));
    // }
}
