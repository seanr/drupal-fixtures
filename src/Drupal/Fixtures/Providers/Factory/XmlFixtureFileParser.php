<?php
/**
 * Declares the XmlFixtureFileParser class.
 *
 * @author     Mike Lohmann <mike.lohmann@deck36.de>
 */

namespace Drupal\Fixtures\Providers\Factory;

class XmlFixtureFileParser implements FixtureFileParserInterface
{
  /**
   * {@inheritDoc}
   */
  public function parse($contents) {
    $xml = simplexml_load_string($contents);
    $json = json_encode($xml);
    return json_decode($json,TRUE);
  }
}