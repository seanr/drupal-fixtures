<?php
/**
 *
 * PHP Version 5.3
 *
 * @author    Mike Lohmann <mike.lohmann@bauermedia.com>
 * @copyright 2014 Bauer Digital KG
 */
namespace Drupal\Fixtures\DrupalBridges;

use Drupal\Fixtures\Exceptions\ValidationException;
use Drupal\Fixtures\Validators\ValidatorInterface;

/**
 * Interface BridgeInterface to be used to create bridge classes to drupal which then can be mocked in tests.
 *
 * @package Drupal\Fixtures\DrupalBridges
 */
interface BridgeInterface {
  /**
   * @param array $fixtureData
   *
   * @return mixed
   */
  public function createFixtures(array $fixtureData);

  /**
   * @param array $fixtureData
   *
   * @return Boolean
   * @throws ValidationException
   */
  public function validateFixtures(array $fixtureData);

  /**
   * @param ValidatorInterface $validator
   */
  public function addValidator(ValidatorInterface $validator);
}