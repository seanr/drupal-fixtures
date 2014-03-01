<?php
/**
 *
 * PHP Version 5.3
 *
 * @author Mike Lohmann <mike.lohmann@bauermedia.com>
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
    sort($this->providers);
    /** @var FixtureProviderInterface $provider */
    foreach ($this->providers as $provider) {
      $provider->process();
    }
  }

  /**
   * {@inheritDoc}
   */
  public function getProviderNames() {
      $keys = array();
      foreach($this->providers as $provider) {
          $keys = array_merge($keys, array_keys($provider));
      }
    return $keys;
  }


  /**
   * {@inheritDoc}
   */
  public function processProvider($type) {
    if ($this->hasProvider($type)) {
        foreach($this->providers as $provider) {
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
  }

  /**
   * {@inheritDoc}
   */
  public function hasProvider($type) {
      $result = false;
      foreach ($this->providers as $provider) {
        if (array_key_exists($type, $provider)) {
            $result = true;
            break;
        }
      }
    return $result;
  }
}