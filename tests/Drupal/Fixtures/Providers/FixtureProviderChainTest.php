<?php
/**
 * Declares the FixtureProviderChainTest class.
 *
 * @author     Mike Lohmann <mike.lohmann@deck36.de>
 * @copyright  Copyright (c) 2014 DECK36 GmbH & Co. KG (http://www.deck36.de)
 */

namespace Drupal\Fixtures\Providers;


use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Parser;

class FixtureProviderChainTest extends \PHPUnit_Framework_TestCase {
  /**
   * @var FixtureProviderChainInterface
   */
  private $subjectToTest;

  /**
   * @var \PHPUnit_Framework_MockObject_MockObject
   */
  private $provider1;

  /**
   * @var \PHPUnit_Framework_MockObject_MockObject
   */
  private $provider2;

  /**
   * {@inheritDoc}
   */
  public function setUp() {
    $this->provider1 = $this->getMockBuilder('Drupal\Fixtures\Providers\FixtureProviderInterface')
      ->getMockForAbstractClass();

    $this->provider2 = $this->getMockBuilder('Drupal\Fixtures\Providers\FixtureProviderInterface')
      ->getMockForAbstractClass();

    $this->subjectToTest = new FixtureProviderChain();
  }


  public function testProcessAllInOrder() {
    $this->provider1
      ->expects($this->once())
      ->method('getType')
      ->will($this->returnValue('test1'));

    $this->provider2
      ->expects($this->once())
      ->method('getType')
      ->will($this->returnValue('test2'));

    $this->provider1
      ->expects($this->exactly(2))
      ->method('process');

    $this->provider2
      ->expects($this->once())
      ->method('process');

    $this->subjectToTest->addProvider($this->provider1, 2);
    $this->subjectToTest->addProvider($this->provider2, 1);

    $this->assertEquals('test1,test2', implode(',', $this->subjectToTest->getProviderNames()));
    $this->assertEquals('test2,test1', implode(',', $this->subjectToTest->getProviderNamesOrdered()));
    $this->subjectToTest->processAll();
    $this->subjectToTest->processProvider('test1');
    $this->assertFalse($this->subjectToTest->hasProvider('humbledumple'));
  }
}
