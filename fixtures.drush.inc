<?php
use Drupal\Fixtures\Providers\FixtureProviderChainInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
/**
 * Implements hook_drush_command().
 */
function fixtures_drush_command() {
  $items = array();
  // Run all fixtures of all types
  $items['fixtures-all'] = array(
    'description' => 'Run installation of all fixtures',
    'bootstrap' => 'DRUSH_BOOTSTRAP_DRUPAL_FULL',
    'callback' => 'fixtures_drush_create_all'
  );

  $existingTypes = 'user,node,menu';
  if (function_exists('drupal_dic')) {
    /* @var $dic ContainerInterface */
    $dic = drupal_dic();
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

  $items['fixtures-validate-all'] = array(
    'description' => 'Run validation of all fixtures',
    'bootstrap' => 'DRUSH_BOOTSTRAP_DRUPAL_FULL',
    'callback' => 'fixtures_drush_validate_all'
  );

  return $items;
}

function fixtures_drush_create_type() {
  $type = drush_get_option('type');
  /* @var $dic ContainerInterface */
  $dic = drupal_dic();
  /** @var $providerChain  FixtureProviderChainInterface */
  $providerChain = $dic->get('fixture_provider_chain');

  if ($providerChain->hasProvider($type)) {
    if (TRUE == $result = $providerChain->processProvider($type)) {
      drush_log('Generated ' . $type . ' fixtures.', 'success');
    }
    else {
      drush_log(
        'Error generating ' . $type . ' fixtures: ' . $result,
        'error'
      );
    }
  }
  else {
    drush_log(
      'Fixtures of type: ' . $type . ' are not available?!',
      'error'
    );

    $result = FALSE;
  }

  return TRUE === $result;
}

/**
 * Install all types of fixtures available
 */
function fixtures_drush_create_all() {
  /* @var $dic ContainerInterface */
  $dic = drupal_dic();
  /** @var $providerChain  FixtureProviderChainInterface */
  $providerChain = $dic->get('fixture_provider_chain');

  if (TRUE == $result = $providerChain->processAll()) {
    drush_log('Generated all fixtures.', 'success');
  }
  else {
    drush_log(
      'Error generating all fixtures: ' . $result,
      'error'
    );
  }
}

/**
 * Validate all types of fixtures available
 */
function fixtures_drush_validate_all() {
  /* @var $dic ContainerInterface */
  $dic = drupal_dic();
  /** @var $providerChain  FixtureProviderChainInterface */
  $providerChain = $dic->get('fixture_provider_chain');

  if (TRUE == $result = $providerChain->validateAll()) {
    drush_log('Validated all fixtures.', 'success');
  }
  else {
    drush_log(
      'Error validating all fixtures: ' . $result,
      'error'
    );
  }
}
