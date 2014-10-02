<?php
/**
 * Declares the PrepareBridgesPass class.
 *
 * @author     Mike Lohmann <mike.lohmann@deck36.de>
 */

namespace Drupal\Fixtures\Bundle\FixturesBundle\DependencyInjection\Compiler;

use Drupal\Fixtures\Validators\ValidatorInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class PrepareBridgesPass implements CompilerPassInterface
{
  /**
   * {@inheritDoc}
   */
  public function process(ContainerBuilder $container) {
    $bridgeIds = $this->getBridgeIds($container);

    if (0 == count($bridgeIds)) {
      watchdog(
        'fixtures',
        'cannot find any bridge',
        array(),
        WATCHDOG_ERROR
      );

      throw new \Exception('You need to define some bridges to make it work.');
    }

    foreach ($bridgeIds as $bridgeId) {
      $taggedServiceName = 'drupal.fixtures.' . $bridgeId . '_validator';
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
  }

  /**
   * @param ContainerBuilder $container
   *
   * @return array
   */
  private function getBridgeIds(ContainerBuilder $container)
  {
    $bridgeIds = array();
    $bridges = $container->findTaggedServiceIds('drupal.fixtures.drupal_bridge');
    foreach($bridges as $id => $tagAttributes) {
      $bridgeIds[] = $id;
    }

    return $bridgeIds;
  }
}