<?php
/**
 *
 * PHP Version 5.3
 *
 * @author Mike Lohmann <mike.lohmann@bauermedia.com>
 * @copyright 2014 Bauer Digital KG
 */
namespace Drupal\Fixtures\DrupalBridges;

/**
 * Interface BridgeInterface to be used to create bridge classes to drupal which then can be mocked in tests.
 *
 * @package Drupal\Fixtures\DrupalBridges
 */
interface BridgeInterface {
  /**
   * @param array $fixtureData
   * @return mixed
   */
  public function createFixtures(array $fixtureData);
}