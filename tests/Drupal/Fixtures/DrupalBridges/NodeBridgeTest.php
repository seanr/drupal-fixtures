<?php
/**
 *
 * PHP Version 5.3
 *
 * @author Mike Lohmann <mike.lohmann@bauermedia.com>
 * @copyright 2014 Bauer Digital KG
 */
namespace {
  $nodeSavedCalled = false;
  $nodeGiven = array();
}

namespace Drupal\Fixtures\DrupalBridges {

  use PHPUnit_Framework_MockObject_MockObject;

  function node_save($node) {
    global $nodeSavedCalled, $nodeGiven;
    $nodeSavedCalled = true;
    $nodeGiven = $node;
  }

  class NodeBridgeTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $subjectToTest;

    /**
     * {@inheritDoc}
     */
    public function setUp() {
      $this->subjectToTest = new NodeBridge();
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown() {
      global $nodeSavedCalled, $nodeGiven;

      $nodeSavedCalled = false;
      $nodeGiven = array();
    }

    /**
     * Tests the createFixtures method of the MenuBridge
     */
    public function testCreateFixtures() {
      global $nodeSavedCalled, $nodeGiven;

      $bodyText = 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.';
      $path = 'test1';

      $node = array();
      $node['test1'] = new \StdClass();
      $node['test1']->body = $bodyText;
      $node['test1']->created = '2012-08-05 22:48:51';
      $node['test1']->path = 'test1';

      $this->subjectToTest->createFixtures($node);
      $this->assertTrue($nodeSavedCalled);
      $this->assertEquals($bodyText, $nodeGiven->body['und'][0]['value']);
      $this->assertEquals($path, $nodeGiven->path['alias']);
    }
  }
}
