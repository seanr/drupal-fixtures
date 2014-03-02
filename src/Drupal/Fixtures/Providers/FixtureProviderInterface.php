<?php
/**
 * This interface defines the access to the different fixture providers.
 *
 * PHP Version 5.3
 *
 * @author    Mike Lohmann <mike.lohmann@bauermedia.com>
 * @copyright 2014 Bauer Digital KG
 *
 */
namespace Drupal\Fixtures\Providers;

/**
 * Class FixtureProviderInterface
 *
 * @package Providers
 */
interface FixtureProviderInterface {
  /**
   * @const ARRAY_RETURN_TYPE
   */
  const ARRAY_RETURN_TYPE = 1;

  /**
   * @const STDCLASS_RETURN_TYPE
   */
  const STDCLASS_RETURN_TYPE = 2;

  /**
   * @return string
   */
  public function getType();

  /**
   * @return Boolean
   */
  public function process();

  /**
   * @param string $path
   */
  public function setFixtureLoadPath($path);
}