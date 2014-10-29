<?php
/**
 *
 * PHP Version 5.3
 *
 * @author    Mike Lohmann <mike.lohmann@bauermedia.com>
 * @copyright 2014 Bauer Digital KG
 */
namespace Drupal\Fixtures\Providers;

/**
 * Class UserFixtureProvider cares about user fixtures
 *
 * @package Drupal\Fixtures\Providers
 */
class UserFixtureProvider extends BaseFixtureProvider {
  /**
   * @const TYPE_NAME
   */
  const TYPE_NAME = 'user';

  /**
   * @const FILENAME_PATTERN
   */
  const FILENAME_PATTERN = '/user.*\.(yml|yaml|xml)/';

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