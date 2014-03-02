<?php
/**
 * Declares the FixtureProviderTest class.
 *
 * @author     Mike Lohmann <mike.lohmann@deck36.de>
 * @copyright  Copyright (c) 2014 DECK36 GmbH & Co. KG (http://www.deck36.de)
 */

namespace Drupal\Fixtures\Providers;


use Drupal\Fixtures\Exceptions\DrupalFixturesException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Parser;

class FixtureProviderTest extends \PHPUnit_Framework_TestCase {
  /**
   * @var Parser
   */
  protected $parser;

  /**
   * @var \PHPUnit_Framework_MockObject_MockObject
   */
  protected $bridge;

  /**
   * @var Finder
   */
  protected $finder;

  /**
   * @var FixtureProviderInterface
   */
  private $subjectToTest;

  /**
   * {@inheritDoc}
   */
  public function setUp() {
    $this->bridge = $this->getMockBuilder('Drupal\Fixtures\DrupalBridges\BridgeInterface')
      ->getMockForAbstractClass();

    $this->parser = new Parser();
    $this->finder = new Finder();

    $this->subjectToTest = new TestFixtureProvider($this->parser, $this->bridge, $this->finder);
    $this->subjectToTest
      ->setFixtureLoadPath(__DIR__ . '/../fixtures');
    $this->subjectToTest->setFileNamePattern('menu--*.yaml');
  }

  /**
   * @expectedException \Drupal\Fixtures\Exceptions\DrupalFixturesException
   */
  public function testProcessWrongDirectory() {
    $this->subjectToTest->setFixtureLoadPath('humbledumple');
    $this->subjectToTest->process();
  }

  /**
   * test process when there is no file
   */
  public function testProcessNoFileFound() {
    $this->bridge
      ->expects($this->never())
      ->method('validateFixtures');

    $this->bridge
      ->expects($this->never())
      ->method('createFixtures');

    $this->subjectToTest->setFileNamePattern('test--*.yaml');

    $result = $this->subjectToTest
      ->process();

    $this->assertTrue($result);
  }

  /**
   * tests process when everything is fine
   */
  public function testProcess() {
    $this->bridge
      ->expects($this->once())
      ->method('validateFixtures');

    $this->bridge
      ->expects($this->once())
      ->method('createFixtures');

    $this->subjectToTest
      ->setReturnType(FixtureProviderInterface::ARRAY_RETURN_TYPE);

    $result = $this->subjectToTest
      ->process();

    $this->assertTrue($result);
  }

  /**
   * test process by having a validation problem.
   */
  public function testProcessValidationException() {
    $this->bridge
      ->expects($this->once())
      ->method('validateFixtures')
      ->will($this->throwException(new DrupalFixturesException('Test exception')));

    $this->bridge
      ->expects($this->never())
      ->method('createFixtures');

    $this->subjectToTest
      ->setReturnType(FixtureProviderInterface::ARRAY_RETURN_TYPE);

    $result = $this->subjectToTest
      ->process();

    $this->assertFalse($result);
  }

  /**
   * test process by having a stdclass as return type
   */
  public function testProcessStdClass() {
    $this->bridge
      ->expects($this->once())
      ->method('validateFixtures')
      ->with(
        $this->callback(
          function ($subject) {
            return is_array($subject) && $subject[0] instanceof \StdClass;
          }
        )
      );

    $this->bridge
      ->expects($this->once())
      ->method('createFixtures');

    $this->subjectToTest
      ->setReturnType(FixtureProviderInterface::STDCLASS_RETURN_TYPE);

    $result = $this->subjectToTest
      ->process();

    $this->assertTrue($result);
  }
}

class TestFixtureProvider extends BaseFixtureProvider {
  private $returnType;
  private $pattern;

  /**
   * Just for testing
   *
   * @param string $type
   */
  public function setReturnType($type) {
    $this->returnType = $type;
  }

  /**
   * Just for testing
   *
   * @param string $pattern
   */
  public function setFileNamePattern($pattern) {
    $this->pattern = $pattern;
  }

  /**
   * {@inheritDoc}
   */
  public function getType() {
    return 'test';
  }

  /**
   * {@inheritDoc}
   */
  protected function getFilenamePattern() {
    return $this->pattern;
  }

  /**
   * {@inheritDoc}
   */
  protected function getReturnType() {
    return $this->returnType;
  }
}
