<?php

namespace Drupal\Fixtures\Bundle\FixturesBundle;

use Drupal\Fixtures\Bundle\FixturesBundle\DependencyInjection\Compiler\PrepareProvidersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @package Drupal\Fixtures\Bundle\FixturesBundle
 */
class FixturesBundle extends Bundle {

  public function build(ContainerBuilder $container)
  {
    parent::build($container);

    $container->addCompilerPass(new PrepareProvidersPass());
    //$container->addCompilerPass(new PrepareProvidersPass());
  }
}
