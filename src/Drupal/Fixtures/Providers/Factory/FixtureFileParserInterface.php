<?php
/**
 * Declares the FixtureFileParserInterface class.
 *
 * @author     Mike Lohmann <mike.lohmann@deck36.de>
 */
namespace Drupal\Fixtures\Providers\Factory;

interface FixtureFileParserInterface {

  /**
   * @param string $contents
   * @return array
   */
  public function parse($contents);
}