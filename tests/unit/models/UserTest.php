<?php

namespace tests\unit\models;

use app\exceptions\ValidationErrorHttpException;
use app\models\UploadFile;
use Yii;
use app\models\User;
use app\models\UserProfile;
use app\tests\unit\fixtures\models\UserFixture;
use app\tests\unit\fixtures\models\UserProfileFixture;

use function Codeception\Extension\codecept_log;

class UserTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    public function _fixtures()
    {
        return [
            'users' => UserFixture::class,
            'userProfiles' => UserProfileFixture::class
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

    public function testGetEmailForSendWithEmailEmpty()
    {
        $user = User::findByUsername('nigger');
        verify($user->getEmailForSend())->equals([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']]);
    }
    public function testGetEmailForSendWithEmailNotEmpty()
    {
        $user = User::findByUsername('napoleon');
        verify($user->getEmailForSend())->equals(['4mo@gmail.com' => 'ABC Н. О.']);
    }
    public function testCreateUserWithIncorrectUserData()
    {
        try {
            User::createUser(['username' => 'sdaw'], $this->getUploadFileModel());
            $this->expectException(
                ValidationErrorHttpException::class
            );
        } catch (\Throwable $th) {
            verify(json_decode($th->getMessage()))->equals([
                'Необходимо заполнить «Password».',
            ]);
        }
    }
    public function testCreateUserWithCorrectUserDataAndIncorrectUserProfileData()
    {

        $res = User::createUser(['username' => 'admin', 'password' => 'admin'], $this->getUploadFileModel());
        verify($res)->equals(['message' => 'Пользователь создан', 'data' => 3]);
    }

    public function getUploadFileModel(): UploadFile
    {
        $uploadFileModel = new UploadFile();
        $uploadFileModel->files = [];
        return $uploadFileModel;
    }
}
