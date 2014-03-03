<?php
use Drupal\Fixtures\Providers\FixtureProviderInterface;
use Drupal\Fixtures\Validators\ValidatorInterface;
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

  fixtures_inject_prepareProviders($container);
  fixtures_inject_prepareBridge($container, 'user');
  fixtures_inject_prepareBridge($container, 'node');
  fixtures_inject_prepareBridge($container, 'menu');
}

/**
 * @param ContainerBuilder $container
 */
function fixtures_inject_prepareProviders(ContainerBuilder $container) {
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

  $taggedServicesForFixtureProviders = $container->findTaggedServiceIds(
    'drupal.fixtures.provider'
  );

  /** @var $id FixtureProviderInterface */
  foreach ($taggedServicesForFixtureProviders as $id => $tagAttributes) {
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

/**
 * @param ContainerBuilder $container
 */
function fixtures_inject_prepareBridge(ContainerBuilder $container, $bridgePrefix) {
  $bridgeId = $bridgePrefix . '_drupal_bridge';
  $taggedServiceName = 'drupal.fixtures.' . $bridgePrefix . '_validator';
  if (!$container->hasDefinition($bridgeId)) {
    watchdog(
      'fixtures',
      'cannot find the bridge: ' . $bridgeId,
      array(),
      WATCHDOG_WARNING
    );
  }
  else {
    $definition = $container->getDefinition(
      $bridgeId
    );

    $taggedServicesForValidator = $container->findTaggedServiceIds(
      $taggedServiceName
    );

    /** @var $id ValidatorInterface */
    foreach ($taggedServicesForValidator as $id => $tagAttributes) {
      $definition->addMethodCall(
        'addValidator',
        array(new Reference($id))
      );
    }
  }
}