<?php
/**
 * Declares the FixtureParserFactoryInterface class.
 *
 * @author     Mike Lohmann <mike.lohmann@deck36.de>
 */
namespace Drupal\Fixtures\Providers\Factory;

interface FixtureParserFactoryInterface {
   /**
    * @param string $name
    * @param mixed $parser
    * @return FixtureFileParserInterface
    * @throws DrupalFixturesException
    */
   public function getParser($name, $parser = null);
}