<?php
/**
 * Declares the YmlFixtureFileParser class.
 *
 * @author     Mike Lohmann <mike.lohmann@deck36.de>
 */

namespace Drupal\Fixtures\Providers\Factory;


use Symfony\Component\Yaml\Parser;

class YmlFixtureFileParser implements FixtureFileParserInterface {

  /**
   * @var Parser
   */
  private $parser;

  /**
   * @param Parser $parser
   */
  public function __construct(Parser $parser)
  {
    $this->parser = $parser;
  }

  /**
   * {@inheritDoc}
   */
  public function parse($contents)
  {
    return $this->parser->parse($contents);
  }
}