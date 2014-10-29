<?php
/**
 * Declares the FixtureParserFactory class.
 *
 * @author     Mike Lohmann <mike.lohmann@deck36.de>
 */

namespace Drupal\Fixtures\Providers\Factory;


use Drupal\Fixtures\Exceptions\DrupalFixturesException;

class FixtureParserFactory implements FixtureParserFactoryInterface
{
  /**
   * {@inheritDoc}
   */
  public function getParser($name, $parser = null) {
    switch($name) {
      case 'yml':
      case 'yaml':
        return new YmlFixtureFileParser($parser);
        break;
      case 'xml':
        return new XmlFixtureFileParser();
        break;
      default:
        throw new DrupalFixturesException('Cannot find a FixtureParser for ' . $name);
        break;
    }
  }
}