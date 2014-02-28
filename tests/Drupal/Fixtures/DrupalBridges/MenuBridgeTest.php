<?php
/**
 *
 * PHP Version 5.3
 *
 * @author Mike Lohmann <mike.lohmann@bauermedia.com>
 * @copyright 2014 Bauer Digital KG
 */
namespace {
  $menuLoadResult = false;
  $watchDogMessage = '';
  $menuSavedCalled = false;
  $menuGiven = array();
}

namespace Drupal\Fixtures\DrupalBridges {

  use PHPUnit_Framework_MockObject_MockObject;

  function menu_load($menu_name) {
    global $menuLoadResult;
    return $menuLoadResult;
  }

  function menu_save($menu) {
    global $menuSavedCalled, $menuGiven;
    $menuSavedCalled = true;
    $menuGiven = $menu;
  }

  function watchdog($message, $severity) {
    global $watchDogMessage;
    $watchDogMessage = $message . ' ' . $severity;
  }

  class MenuBridgeTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $subjectToTest;

    /**
     * {@inheritDoc}
     */
    public function setUp() {
      $this->subjectToTest = $this->getMock(
        'Drupal\Fixtures\DrupalBridges\MenuBridge',
        array('fixturesCreateMenuItem')
      );
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown() {
      global $menuLoadResult, $watchDogMessage, $menuSavedCalled, $menuGiven;

      $menuLoadResult = false;
      $watchDogMessage = '';
      $menuSavedCalled = false;
      $menuGiven = array();
    }

    /**
     * Tests the createFixtures method of the MenuBridge
     */
    public function testCreateFixtures() {
      global $menuLoadResult, $watchDogMessage, $menuSavedCalled, $menuGiven;

      $menu = array();
      $menu['test1'] = array (
        'title' => 'testtitle1',
        'description' => 'description1',
        'items' => array(
          'exits' => true,
          'link' => 'testlink'
        )
      );

      $menuLoadResult = false;

      $this->subjectToTest
        ->expects($this->once())
        ->method('fixturesCreateMenuItem');

      $this->subjectToTest->createFixtures($menu);

      $this->assertTrue($menuSavedCalled);
      $this->assertEquals($menu['test1']['title'], $menuGiven['title']);
      $this->assertTrue($menuGiven['menu_name'] == 'test1');
      $this->assertEquals('Created menu test1. info', $watchDogMessage);
    }

    /**
     * Tests the createFixtures method of the MenuBridge without creating new menu
     */
    public function testCreateFixturesWithoutCreatingMenu() {
      global $menuLoadResult, $watchDogMessage, $menuSavedCalled;

      $menu = array();
      $menu['test1'] = array (
        'title' => 'testtitle1',
        'description' => 'description1',
        'items' => array(
          'exits' => true,
          'link' => 'testlink'
        )
      );

      $menuLoadResult = true;

      $this->subjectToTest
        ->expects($this->once())
        ->method('fixturesCreateMenuItem');

      $this->subjectToTest->createFixtures($menu);

      $this->assertFalse($menuSavedCalled);
      $this->assertEquals('Menu test1 already exists. info', $watchDogMessage);
    }
  }
}
