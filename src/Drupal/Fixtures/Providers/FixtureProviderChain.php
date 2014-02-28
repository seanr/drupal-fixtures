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
  public function addProvider(FixtureProviderInterface $provider) {
    $this->providers[$provider->getType()] = $provider;
  }

  /**
   * {@inheritDoc}
   */
  public function processAll() {
    /** @var FixtureProviderInterface $provider */
    foreach ($this->providers as $provider) {
      $provider->process();
    }
  }

  /**
   * {@inheritDoc}
   */
  public function getProviderNames() {
    return array_keys($this->providers);
  }

  /**
   * {@inheritDoc}
   */
  public function processProvider($type) {
    if ($this->hasProvider($type)) {
      $this->providers[$type]->process();
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
    return array_key_exists($type, $this->providers);
  }
}