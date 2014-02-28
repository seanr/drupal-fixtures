<?php
use Drupal\Fixtures\DrupalBridges\BridgeInterface;
use Drupal\Fixtures\Providers\FixtureProviderChainInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

function fixtures_drush_command() {
  $items = array();
  // Run all fixtures of all types
  $items['fixtures-all'] = array(
    'description' => 'Run installation of all fixtures',
    'bootstrap' => 'DRUSH_BOOTSTRAP_DRUPAL_FULL',
    'callback' => 'fixtures_drush_create_all',
  );

  $existingTypes = 'user,node,menu';
  if (function_exists('drupal_container')) {
    /* @var $dic ContainerInterface */
    $dic = drupal_container();
    /** @var $providerChain  FixtureProviderChainInterface */
    $providerChain = $dic->get('fixture_provider_chain');
    $existingTypes = implode(',', $providerChain->getProviderNames());
  }

  $items['fixtures-type'] = array(
    'description' => 'Run installation of given type of fixtures',
    'bootstrap' => 'DRUSH_BOOTSTRAP_DRUPAL_FULL',
    'callback' => 'fixtures_drush_create_type',
    'options' => array(
      'type' => 'You can choose one of the fixture types to be installed: ' . $existingTypes,
    )
  );

  return $items;
}

function fixtures_drush_create_type() {
  $type = drush_get_option('type');
  /* @var $dic ContainerInterface */
  $dic = drupal_container();
  /** @var $providerChain  FixtureProviderChainInterface */
  $providerChain = $dic->get('fixture_provider_chain');

  if ($providerChain->hasProvider($type)) {
    if (true == $result = $providerChain->processProvider($type)) {
      drush_log('Generated ' . $type . ' fixtures.', 'success');
    } else {
      drush_log(
        'Error generating ' . $type . ' fixtures: ' . $result,
        'error'
      );
    }
  } else {
    drush_log(
      'Fixtures of type: ' . $type . ' are not available?!',
      'error'
    );

    $result = false;
  }

  return true === $result;
}

/**
 * Install all types of fixtures available
 */
function fixtures_drush_create_all() {
  /* @var $dic ContainerInterface */
  $dic = drupal_container();
  /** @var $providerChain  FixtureProviderChainInterface */
  $providerChain = $dic->get('fixture_provider_chain');

  if (true == $result = $providerChain->processAll()) {
    drush_log('Generated all fixtures.', 'success');
  } else {
    drush_log(
      'Error generating all fixtures: ' . $result,
      'error'
    );
  }
}
