<?php
/**
 * Declares the NodeBridgeInterface class.
 *
 * @author     Mike Lohmann <mike.lohmann@deck36.de>
 */

namespace Drupal\Fixtures\DrupalBridges;


use Drupal\Fixtures\DrupalBridges\Specialized\SpecializedBridgeInterface;

interface NodeBridgeInterface {

  /**
   * @param SpecializedBridgeInterface $bridge
   *
   * @return mixed
   */
  public function addSpecializedBridge(SpecializedBridgeInterface $bridge);

  /**
   * @param string $name
   *
   * @return Boolean
   */
  public function hasSpecializedBridge($name);

  /**
   * @param string $name
   *
   * @return SpecializedBridgeInterface
   */
  public function getSpecializedBridge($name);
} 