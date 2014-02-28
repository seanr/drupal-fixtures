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
use Drupal\Fixtures\Exceptions\DrupalFixturesException;
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
  protected $parser;

  /**
   * @var BridgeInterface
   */
  protected $bridge;

  /**
   * @var Finder
   */
  protected $finder;

  /**
   * @var string
   */
  protected $fixturesPath;

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

    if (!is_dir($this->fixturesPath)) {
      throw new DrupalFixturesException('Cannot find dir: ' . $this->fixturesPath);
    }

    /** @var SplFileInfo $file */
    foreach($this->finder->files()->name($this->getFilenamePattern())->in($this->fixturesPath) as $file)
    {
      try {
        $loadedFixtures = $this->parser->parse($file->getContents());

        if (is_array($loadedFixtures) && $this->getReturnType() == self::STDCLASS_RETURN_TYPE) {
          $loadedFixtures = $this->convertFixturesToObject($loadedFixtures);
        }

        if (true == $this->bridge->validateFixtures($loadedFixtures)) {

        } else {

        }

        $this->bridge->createFixtures($loadedFixtures);
      } catch (DrupalFixturesException $e) {
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
   * {@inheritDoc}
   */
  abstract protected function getReturnType();

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