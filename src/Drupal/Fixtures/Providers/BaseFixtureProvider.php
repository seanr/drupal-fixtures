<?php
/**
 *
 * PHP Version 5.3
 *
 * @author Mike Lohmann <mike.lohmann@bauermedia.com>
 * @copyright 2014 Bauer Digital KG
 */
namespace Drupal\Fixtures\Providers;


use Drupal\Fixtures\DrupalBridges\BridgeInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Parser;

/**
 * Class BaseFixtureProvider provides base functionality to load fixtures
 *
 * @package Drupal\Fixtures\Providers
 */
abstract class BaseFixtureProvider implements FixtureProviderInterface {

  /**
   * @var Parser
   */
  private $parser;

  /**
   * @var BridgeInterface
   */
  private $bridge;

  /**
   * @var Finder
   */
  private $finder;

  /**
   * @var string
   */
  private $fixturesPath;

  /**
   * @const RETURN_TYPE
   */
  const RETURN_TYPE = self::ARRAY_RETURN_TYPE;

  /**
   * @param Parser $yamlParser
   */
  public function __construct(Parser $yamlParser, BridgeInterface $userBridge, Finder $fileFinder) {
    $this->parser = $yamlParser;
    $this->bridge = $userBridge;
    $this->finder = $fileFinder;
  }


  /**
   * {@inheritDoc}
   */
  abstract public function getType();

  /**
   * {@inheritDoc}
   */
  public function process() {
    $overallResult = true;

    /** @var SplFileInfo $file */
    foreach($this->finder->files()->name($this->getFilenamePattern())->in($this->fixturesPath) as $file)
    {
      try {
        $loadedFixtures = $this->parser->parse($file->getContents());

        if (is_array($loadedFixtures) && self::RETURN_TYPE == self::STDCLASS_RETURN_TYPE) {
          $loadedFixtures = $this->convertFixturesToObject($loadedFixtures);
        }

        $this->bridge->createFixtures($loadedFixtures);
      } catch (\Exception $e) {
        // @todo: log exception
        $overallResult = false;
        break;
      }
    }

    return $overallResult;
  }

  /**
   * {@inheritDoc}
   */
  public function setFixtureLoadPath($path) {
    $this->fixturesPath = $path;
  }

  /**
   * {@inheritDoc}
   */
  abstract protected function getFilenamePattern();

  /**
   * converts an array of arrays to an array of stdClasses
   *
   * @param array $fixtures
   * @return array
   */
  protected function convertFixturesToObject(array $fixtures)
  {
    $result = array();
    foreach ($fixtures as $fixtureItem) {
      if (is_array($fixtureItem)) {
        $result[] = (object) $fixtureItem;
      }
    }

    return $result;
  }
}