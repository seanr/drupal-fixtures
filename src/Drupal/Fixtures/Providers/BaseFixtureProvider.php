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
use Drupal\Fixtures\Providers\Factory\FixtureFileParserInterface;
use Drupal\Fixtures\Providers\Factory\FixtureParserFactory;
use Drupal\Fixtures\Providers\Factory\FixtureParserFactoryInterface;
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
  public function __construct(BridgeInterface $userBridge) {
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
      if (function_exists('drush_print')) {
        drush_print('Processing fixutures of file: ' . $file->getFilename());
      }

      try {
        $fileParserServiceName = 'fixture_' . strtolower($file->getExtension()) . '_file_parser';
        $parser = $this->getFileParser($fileParserServiceName);

        $loadedFixtures = $parser->parse($file->getContents());
        // $this->bridge->validateFixtures($loadedFixtures);

        if (is_array($loadedFixtures) && $this->getReturnType() == self::STDCLASS_RETURN_TYPE) {
          $loadedFixtures = $this->convertFixturesToObject($loadedFixtures);
        }

        $this->bridge->createFixtures($loadedFixtures);
      } catch (DrupalFixturesException $e) {
        // @todo: log exception
        // Simple debug message so we can see what had been deleted.
        if (function_exists('drush_print')) {
          drush_print($e->getMessage());
        } else {
          echo($e->getMessage() . "\n\n");
        }

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
      $fileParserServiceName = 'fixture_' . strtolower($file->getExtension()) . '_file_parser service.';
      $parser = $this->getFileParser($fileParserServiceName);
      $loadedFixtures = $parser->parse($file->getContents());
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
  protected function convertFixturesToObject(array $fixturetypes) {
    $result = array();
    foreach ($fixturetypes as $fixtureItems) {
        foreach($fixtureItems as $singleFixtureItem) {
          $result[] = (object) $singleFixtureItem;
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

  /**
   * @param $fileParserServiceName
   * @return \Drupal\Fixtures\Providers\Factory\FixtureFileParserInterface
   * @throws \Drupal\Fixtures\Exceptions\DrupalFixturesException
   */
  private function getFileParser($fileParserServiceName) {
      if (function_exists('drupal_dic')
      && drupal_dic()->has($fileParserServiceName)
    ) {
      /** @var FixtureFileParserInterface $parser */
      return drupal_dic()->get($fileParserServiceName);
    }
    else {
      throw new DrupalFixturesException('Cannot find ' . $fileParserServiceName);
    }
  }
}