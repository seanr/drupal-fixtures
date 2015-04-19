<?php
/**
 * Declares the SpecializedBridgeInterface class.
 *
 * @author     Mike Lohmann <mike.lohmann@deck36.de>
 */

namespace Drupal\Fixtures\DrupalBridges\Specialized;


use Drupal\Fixtures\Providers\Event\PreprocessEvent;

interface SpecializedBridgeInterface {
    /**
     * @throws SpecializedBridgeException
     * @return string
     */
    public function getName();

    /**
     * @param \StdClass $fixNode
     *
     * @throws SpecializedBridgeException
     * @return \StdClass[]
     */
    public function process(\StdClass $fixNode);

    /**
     * @param PreprocessEvent $event
     * @return mixed
     */
    public function deleteAllNodes(PreprocessEvent $event);
} 