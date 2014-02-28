<?php
/**
 *
 * PHP Version 5.3
 *
 * @author Mike Lohmann <mike.lohmann@bauermedia.com>
 * @copyright 2014 Bauer Digital KG
 */
namespace Drupal\Fixtures\Providers;

/**
 * Class MenuFixtureProvider cares about menu fixtures
 *
 * @package Drupal\Fixtures\Providers
 */
class MenuFixtureProvider extends BaseFixtureProvider {

 /**
   * @const TYPE_NAME
   */
  const TYPE_NAME = 'menu';

  /**
   * @const FILENAME_PATTERN
   */
  const FILENAME_PATTERN = 'menu--.*\.yml';

  /**
   * {@inheritDoc}
   */
  public function getType() {
    return self::TYPE_NAME;
  }

  /**
   * {@inheritDoc}
   */
  protected function getFilenamePattern() {
    return self::TYPE_NAME;
  }
}