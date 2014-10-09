<?php
/**
 *
 * PHP Version 5.3
 *
 * @author    Mike Lohmann <mike.lohmann@bauermedia.com>
 * @copyright 2014 Bauer Digital KG
 */
namespace Drupal\Fixtures\DrupalBridges;

use Drupal\Fixtures\DrupalBridges\Specialized\SpecializedBridgeException;
use Drupal\Fixtures\DrupalBridges\Specialized\SpecializedBridgeInterface;
use Drupal\Fixtures\Exceptions\DrupalFixturesException;

/**
 * Class NodeBridge is used to provide some functionality needed from drupal to create node data
 *
 * @package Drupal\Fixtures\DrupalBridges
 */
class NodeBridge extends BaseBridge implements NodeBridgeInterface {

  /**
   * @var array
   */
  private $specializedBridges = array();

  /**
   * {@inheritDoc}
   */
  public function createFixtures(array $fixtureData) {
    $resultData = array();

    foreach ($fixtureData as $node_name => $node) {
      $resultData[$node_name] = $this->fixtureCreateNode($node->type, $node);
    }

    return $resultData;
  }

  /**
   * Do some modifications and add some fields to the given node obj.
   *
   * @param  string $nodeType
   * @param \StdClass $fixnode
   *
   * @throws SpecializedBridgeException
   * @throws DrupalFixturesException
   * @internal param \StdClass $node
   *
   * @return \StdClass
   */
  protected function fixtureCreateNode($nodeType, \StdClass $fixnode) {

    if ($this->hasSpecializedBridge($nodeType)) {
      $node = $this->getSpecializedBridge($nodeType)->process($fixnode);
    }
    else {
      throw new DrupalFixturesException(
        'Could not find specialized Bridge for node type: ' . $nodeType
      );
    }

    return $node;
  }

  /**
   * {@inheritDoc}
   */
  public function addSpecializedBridge(SpecializedBridgeInterface $bridge) {
    $this->specializedBridges[$bridge->getName()] = $bridge;
  }

  /**
   * {@inheritDoc}
   */
  public function hasSpecializedBridge($name) {
    return array_key_exists($name, $this->specializedBridges);
  }

  /**
   * {@inheritDoc}
   */
  public function getSpecializedBridge($name) {
    if (!$this->hasSpecializedBridge($name)) {
      throw new SpecializedBridgeException(
        'Bridge ' . $name . ' does not exists.'
      );
    }

    return $this->specializedBridges[$name];
  }
}