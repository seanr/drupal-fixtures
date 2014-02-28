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
 * Class NodeFixtureProvider cares about node fixtures
 *
 * @package Drupal\Fixtures\Providers
 */
class NodeFixtureProvider extends BaseFixtureProvider {
  /**
   * @const TYPE_NAME
   */
  const TYPE_NAME = 'node';

  /**
   * @const FILENAME_PATTERN
   */
  const FILENAME_PATTERN = 'node--.*\.yml';

  /**
   * @const RETURN_TYPE
   */
  const RETURN_TYPE = self::STDCLASS_RETURN_TYPE;

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
    return self::FILENAME_PATTERN;
  }

  /**
   * {@inheritDoc}
   */
  protected function getReturnType() {
    return self::RETURN_TYPE;
  }
}