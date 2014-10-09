<?php
/**
 * Declares the PrepareSpecializedNodeBridgesPass class.
 *
 * @author     Mike Lohmann <mike.lohmann@deck36.de>
 */

namespace Drupal\Fixtures\Bundle\FixturesBundle\DependencyInjection\Compiler;

use Drupal\Fixtures\Validators\ValidatorInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class PrepareSpecializedNodeBridgesPass implements CompilerPassInterface
{
  /**
   * {@inheritDoc}
   */
  public function process(ContainerBuilder $container) {
    $specializedBridgeIds = $this->getBridgeIds($container);

    if (0 == count($specializedBridgeIds)) {
      watchdog(
        'fixtures',
        'cannot find any specialized bridge',
        array(),
        WATCHDOG_ERROR
      );

      throw new \Exception('You need to define some specialized bridges to make
       it work.');
    }

    foreach ($specializedBridgeIds as $bridgeId) {

        $definition = $container->getDefinition('node_drupal_bridge');

         $definition->addMethodCall(
            'addSpecializedBridge',
            array(new Reference($bridgeId))
          );
     }
  }

  /**
   * @param ContainerBuilder $container
   *
   * @return array
   */
  private function getBridgeIds(ContainerBuilder $container)
  {
    $specializeBridgeIds = array();
    $bridges = $container->findTaggedServiceIds('drupal.fixtures.drupal_node_bridge_specialized');
    foreach($bridges as $id => $tagAttributes) {
      $specializeBridgeIds[] = $id;
    }

    return $specializeBridgeIds;
  }
}