<?php
/**
 * Declares the PrepareSpecializedNodeValidatorPass class.
 *
 * @author     Mike Lohmann <mike.lohmann@deck36.de>
 */

namespace Drupal\Fixtures\Bundle\FixturesBundle\DependencyInjection\Compiler;

use Drupal\Fixtures\Validators\ValidatorInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class PrepareSpecializedNodeValidatorPass implements CompilerPassInterface {
  /**
   * {@inheritDoc}
   */
  public function process(ContainerBuilder $container) {
    $specializedValidatorsIds = $this->getValidatorIds($container);

    if (0 == count($specializedValidatorsIds)) {
      watchdog(
        'fixtures',
        'cannot find any specialized node validator',
        array(),
        WATCHDOG_INFO
      );

      // validators are optional
      return;
    }

    foreach ($specializedValidatorsIds as $bridgeId) {

      $definition = $container->getDefinition('node_fixture_validator');

      $definition->addMethodCall(
        'addSpecializedValidator',
        array(new Reference($bridgeId))
      );
    }
  }

  /**
   * @param ContainerBuilder $container
   *
   * @return array
   */
  private function getValidatorIds(ContainerBuilder $container) {
    $specializedValidatorIds = array();
    $validators = $container->findTaggedServiceIds(
      'drupal.fixtures.drupal_node_validator_specialized'
    );
    foreach ($validators as $id => $tagAttributes) {
      $specializedValidatorIds[] = $id;
    }

    return $specializedValidatorIds;
  }
}