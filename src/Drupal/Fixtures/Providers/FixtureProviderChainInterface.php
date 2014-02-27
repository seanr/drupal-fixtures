<?php
/**
 *
 * PHP Version 5.3
 *
 * @author Mike Lohmann <mike.lohmann@bauermedia.com>
 * @copyright 2014 Bauer Digital KG
 */
namespace Drupal\Fixtures\Providers;

use \ErrorException;

/**
 * Interface FixtureProviderChainInterface
 *
 * @package Drupal\Fixtures\Providers
 */
interface FixtureProviderChainInterface {
  /**
   * Used to add a provider.
   *
   * @param FixtureProviderInterface $provider
   *
   * @return void
   */
  public function addProvider(FixtureProviderInterface $provider);

  /**
   * Processes all registered fixture providers. Returns true or an errormessage if something went wrong.
   *
   * @return Boolean|string
   * @throws \ErrorException
   */
  public function processAll();

  /**
   * Return an array containing the names of all registered fixture providers
   *
   * @return array
   */
  public function getProviderNames();

  /**
   * Processes a registered fixture provider. Returns true or an errormessage if something went wrong.
   *
   * @param $type
   *
   * @return Boolean|string
   */
  public function processProvider($type);

  /**
   * Returns true or false if or if not a provider is registered.
   *
   * @param $type
   *
   * @return Boolean
   */
  public function hasProvider($type);
} 