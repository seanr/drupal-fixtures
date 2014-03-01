<?php
use Drupal\Fixtures\Providers\FixtureProviderChainInterface;
use Drupal\Fixtures\Providers\FixtureProviderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This function is provided by the inject module.
 *
 * {@inheritDoc}
 */
function fixtures_inject_build(ContainerBuilder $container, $phase) {
  // this path should be executed on build time, only
  if ($phase != 'init') {
    return;
  }

  if (!$container->hasDefinition('fixture_provider_chain')) {
    watchdog(
      'fixtures',
      'cannot find the fixture provider chain service.',
      array(),
      WATCHDOG_ERROR
    );
    return;
  }

  $definition = $container->getDefinition(
    'fixture_provider_chain'
  );

  $taggedServices = $container->findTaggedServiceIds(
    'drupal.fixtures.provider'
  );

  /** @var $id FixtureProviderInterface */
  foreach ($taggedServices as $id => $tagAttributes) {
      foreach ($tagAttributes as $attributes) {
          $definition->addMethodCall(
              'addProvider',
              array(new Reference($id), $attributes["order"])
          );
      }

    $providerDefinition = $container->getDefinition(
      $id
    );
    $providerDefinition->addMethodCall(
      'setFixtureLoadPath',
      array(variable_get('fixture_load_path', DRUPAL_ROOT . '/../config/fixtures'))
    );
  }
}