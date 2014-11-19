<?php
/**
 * Declares the PreprocessEvent class.
 *
 * @author     Mike Lohmann <mike.lohmann@deck36.de>
 */

namespace Drupal\Fixtures\DrupalBridges\Event;

use Symfony\Component\EventDispatcher\Event;
/**
 * Class CounterEvent
 *
 * @package Drupal\Fixtures\Providers\Event
 */
class NodeSavedEvent extends Event
{
    /**
     * @var \StdClass
     */
    private $node;

    /**
     * @param \StdClass $node
     */
    public function __construct(\StdClass $node = null) {
        $this->node = $node;
        return $this;
    }

    /**
     * @param \StdClass $node
     */
    public function setNode(\StdClass $node) {
        $this->node = $node;
    }

    /**
     * @return \StdClass
     */
    public function getNode() {
        return $this->node;
    }
}