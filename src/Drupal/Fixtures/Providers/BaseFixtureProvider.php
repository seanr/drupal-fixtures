<?php
/**
 *
 * PHP Version 5.3
 *
 * @author    Mike Lohmann <mike.lohmann@bauermedia.com>
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
   * @var string
   */
  protected $fixturesPath;

  /**
   * @param Parser $yamlParser
   */
  public function __construct(Parser $yamlParser, BridgeInterface $userBridge) {
    $this->parser = $yamlParser;
    $this->bridge = $userBridge;
  }


  /**
   * {@inheritDoc}
   */
  abstract public function getType();

  /**
   * {@inheritDoc}
   */
  public function process() {
    $overallResult = TRUE;

    if (!is_dir($this->fixturesPath)) {
      throw new DrupalFixturesException('Cannot find dir: ' . $this->fixturesPath);
    }

    /** @var SplFileInfo $file */
    $fileIterator = $this->getFinder();
    foreach ($fileIterator as $file) {
      try {
        $loadedFixtures = $this->parser->parse($file->getContents());
        $this->bridge->validateFixtures($loadedFixtures);

        if (is_array($loadedFixtures) && $this->getReturnType() == self::STDCLASS_RETURN_TYPE) {
          $loadedFixtures = $this->convertFixturesToObject($loadedFixtures);
        }

        $this->bridge->createFixtures($loadedFixtures);
      } catch (DrupalFixturesException $e) {
        // @todo: log exception
        echo($e->getMessage() . "\n\n");
        $overallResult = FALSE;
        break;
      }
    }

    return $overallResult;
  }

  /**
   * {@inheritDoc}
   */
  public function validate() {
    /** @var SplFileInfo $file */
    $fileIterator = $this->getFinder();
    foreach ($fileIterator as $file) {
      $loadedFixtures = $this->parser->parse($file->getContents());
      $this->bridge->validateFixtures($loadedFixtures);
    }

    return TRUE;
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
   *
   * @return array
   */
  protected function convertFixturesToObject(array $fixtures) {
    $result = array();
    foreach ($fixtures as $fixtureItem) {
      if (is_array($fixtureItem)) {
        $result[] = (object) $fixtureItem;
      }
    }

    return $result;
  }

  /**
   * @return Finder
   */
  private function getFinder() {
    $finder = new Finder();
    $fileIterator = $finder->files()->name($this->getFilenamePattern())->in($this->fixturesPath);

    return $fileIterator;
  }
}