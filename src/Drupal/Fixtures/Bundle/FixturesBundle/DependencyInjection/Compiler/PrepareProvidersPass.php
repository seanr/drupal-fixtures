<?php
/**
 * Declares the PrepareProvidersPass class.
 *
 * @author     Mike Lohmann <mike.lohmann@deck36.de>
 */

namespace Drupal\Fixtures\Bundle\FixturesBundle\DependencyInjection\Compiler;


use Drupal\Fixtures\Providers\FixtureProviderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class PrepareProvidersPass implements CompilerPassInterface
{
  /**
   * {@inheritDoc}
   */
  public function process(ContainerBuilder $container) {
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
}