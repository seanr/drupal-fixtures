<?php
/**
 *
 * PHP Version 5.3
 *
 * @author    Mike Lohmann <mike.lohmann@bauermedia.com>
 * @copyright 2014 Bauer Digital KG
 */

namespace Drupal\Fixtures\Providers;

use Drupal\Fixtures\Exceptions\ProviderNotFoundException;

class FixtureProviderChain implements FixtureProviderChainInterface {

  /**
   * @var array
   */
  private $providers = array();

  /**
   * {@inheritDoc}
   */
  public function addProvider(FixtureProviderInterface $provider, $order) {
    $this->providers[$order][$provider->getType()] = $provider;
  }

  /**
   * {@inheritDoc}
   */
  public function processAll() {
    sort($this->providers, SORT_NUMERIC);
    /** @var FixtureProviderInterface $provider */

    if (function_exists('drupal_dic')
      && drupal_dic()->has('event_dispatcher')
    ) {
      $prepocressEvent = new \Drupal\Fixtures\Providers\Event\PreprocessEvent();

      drupal_dic()->get('event_dispatcher')->dispatch(
        'fixtures.preprocess', $prepocressEvent
      );
    }

    $overallResult = array();
    foreach ($this->providers as $providerType) {
      foreach ($providerType as $provider) {
        array_push($overallResult, $provider->process());
      }
    }

    $result = true;
    array_walk_recursive($overallResult, function($item) use (&$result) {
      if (in_array(false, array_values($item))) {
        $result = false;
      }
    });

    return $result;
  }

  /**
   * {@inheritDoc}
   */
  public function validateAll() {
    /** @var FixtureProviderInterface $provider */
    foreach ($this->providers as $providerType) {
      foreach ($providerType as $provider) {
        $provider->validate();
      }
    }

    return TRUE;
  }

  /**
   * {@inheritDoc}
   */
  public function getProviderNames() {
    $keys = array();
    foreach ($this->providers as $order => $provider) {
      $providerKeys = array_keys($provider);
      $keys[] = $providerKeys[0];
    }

    return $keys;
  }

  /**
   * {@inheritDoc}
   */
  public function getProviderNamesOrdered() {
    sort($this->providers, SORT_NUMERIC);

    return $this->getProviderNames();
  }


  /**
   * {@inheritDoc}
   */
  public function processProvider($type) {
    if ($this->hasProvider($type)) {
      /** @var FixtureProviderInterface[] $provider */
      foreach ($this->providers as $provider) {
        if (array_key_exists($type, $provider)) {
          $provider[$type]->process();
          break;
        }
      }
    }
    else {
      throw new ProviderNotFoundException(
        'Cannot process provider with name: ' . $type . '. It does not exists.'
      );
    }

    return TRUE;
  }

  /**
   * {@inheritDoc}
   */
  public function hasProvider($type) {
    $result = FALSE;
    foreach ($this->providers as $provider) {
      if (array_key_exists($type, $provider)) {
        $result = TRUE;
        break;
      }
    }

    return $result;
  }
}