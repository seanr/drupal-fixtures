<?php
/**
 *
 * PHP Version 5.3
 *
 * @author    Mike Lohmann <mike.lohmann@bauermedia.com>
 * @copyright 2014 Bauer Digital KG
 */
namespace Drupal\Fixtures\DrupalBridges;

use PHPUnit_Framework_MockObject_MockObject;

class UserBridgeTest extends \PHPUnit_Framework_TestCase {
  /**
   * @var PHPUnit_Framework_MockObject_MockObject
   */
  private $subjectToTest;

  public function setUp() {
    $this->subjectToTest = $this->getMock(
      'Drupal\Fixtures\DrupalBridges\UserBridge',
      array('fixturesGetUsersRoles', 'fixturesSaveUser', 'fixturesGetUserPictureId')
    );
  }

  /**
   * Tests the createFixtures method of the UserBridge
   */
  public function testCreateFixturesWithPic() {
    $users[0] = new \StdClass();
    $users[0]->name = 'testname1';
    $users[0]->mail = 'testname1@test.tes';
    $users[0]->pass = '12345';
    $users[0]->language = 'de';
    $users[0]->status = 1;
    $users[0]->init = $users[0]->mail;
    $users[0]->picture = __DIR__ . '/../fixtures/images/user/userpic1.png';


    $roleId = '123';
    $userId = '123';
    $fileId = '123';

    $this->subjectToTest
      ->expects($this->once())
      ->method('fixturesGetUsersRoles')
      ->will($this->returnValue(array($roleId)));

    $user = $users[0];
    $this->subjectToTest
      ->expects($this->at(1))
      ->method('fixturesSaveUser')
      ->will(
        $this->returnCallback(
          function ($user) {
            $user->uid = '123';

            return $user;
          }
        )
      );

    $this->subjectToTest
      ->expects($this->at(2))
      ->method('fixturesSaveUser');

    $this->subjectToTest
      ->expects($this->once())
      ->method('fixturesGetUserPictureId')
      ->will($this->returnValue($fileId));

    $savedFixtures = $this->subjectToTest->createFixtures($users);

    $this->assertEquals($fileId, $savedFixtures[0]->picture);
    $this->assertEquals($userId, $savedFixtures[0]->uid);
    $this->assertEquals($roleId, $savedFixtures[0]->roles[0]);
    $this->assertEquals(date_default_timezone_get(), $savedFixtures[0]->timezone);
  }

  /**
   * Tests the createFixtures method of the UserBridge
   */
  public function testCreateFixturesWithoutPic() {
    $users[0] = new \StdClass();
    $users[0]->name = 'testname1';
    $users[0]->mail = 'testname1@test.tes';
    $users[0]->pass = '12345';
    $users[0]->language = 'de';
    $users[0]->status = 1;
    $users[0]->init = $users[0]->mail;


    $roleId = '123';
    $userId = '123';

    $this->subjectToTest
      ->expects($this->once())
      ->method('fixturesGetUsersRoles')
      ->will($this->returnValue(array($roleId)));

    $user = $users[0];
    $this->subjectToTest
      ->expects($this->once())
      ->method('fixturesSaveUser')
      ->will(
        $this->returnCallback(
          function ($user) {
            $user->uid = '123';

            return $user;
          }
        )
      );

    $this->subjectToTest
      ->expects($this->never())
      ->method('fixturesGetUserPictureId');

    $savedFixtures = $this->subjectToTest->createFixtures($users);

    $this->assertEquals(FALSE, isset($savedFixtures[0]->picture));
    $this->assertEquals($userId, $savedFixtures[0]->uid);
    $this->assertEquals($roleId, $savedFixtures[0]->roles[0]);
    $this->assertEquals(date_default_timezone_get(), $savedFixtures[0]->timezone);
  }

}
