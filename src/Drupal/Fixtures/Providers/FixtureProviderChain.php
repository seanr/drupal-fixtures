<?php
/**
 *
 * PHP Version 5.3
 *
 * @author Mike Lohmann <mike.lohmann@bauermedia.com>
 * @copyright 2014 Bauer Digital KG
 */

namespace Drupal\Fixtures\Providers;

class FixtureProviderChain implements FixtureProviderChainInterface {

  /**
   * @var array
   */
  private $providers = array();

  /**
   * {@inheritDoc}
   */
  public function addProvider(FixtureProviderInterface $provider) {
    $this->providers[] = $provider;
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
    // TODO: Implement getProviderNames() method.
  }

  /**
   * {@inheritDoc}
   */
  public function processProvider($type) {
    // TODO: Implement processProvider() method.
  }

  /**
   * {@inheritDoc}
   */
  public function hasProvider($type) {
    // TODO: Implement hasProvider() method.
  }
}