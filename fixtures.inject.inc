<?php
use Drupal\Fixtures\Providers\FixtureProviderChainInterface;
use Drupal\Fixtures\Providers\FixtureProviderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * This function is provided by the inject module.
 *
 * {@inheritDoc}
 */
function fixtures_inject_build(ContainerBuilder $container, $phase) {
  // this path should be executed on build time, only
  if ($phase != 'boot') {
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

  /** @var $id FixtureProviderChainInterface */
  foreach ($taggedServices as $id => $attributes) {
    $definition->addMethodCall(
      'addProvider',
      array(new Reference($id))
    );

    /** @var FixtureProviderInterface $providerDefinition */
    $providerDefinition = $container->getDefinition(
      $id
    );

    $providerDefinition->setFixtureLoadPath(variable_get('fixture_load_path', DRUPAL_ROOT . '/../config/fixtures'));
  }


}