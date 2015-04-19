<?php
/**
 * Created by IntelliJ IDEA.
 * User: mikelohmann
 * Date: 19.11.14
 * Time: 11:26
 */

namespace Drupal\Fixtures\DrupalBridges;
use Drupal\Fixtures\DrupalBridges\Event\NodeSavedEvent;
use Drupal\Fixtures\Exceptions\DrupalFixturesException;


/**
 * Class EntityRegistry
 * @package Drupal\Fixtures\DrupalBridges
 */
class EntityRegistry {
    /**
     * @var \StdClass[]
     */
    private $cratedNodes = array();

    /**
     * @var \StdClass[]
     */
    private $lastCreatedNodeOfType = array();

    /**
     * @param NodeSavedEvent $event
     * @throws DrupalFixturesException
     */
    public function addCreatedNode(NodeSavedEvent $event) {
        $node = $event->getNode();

        if (!property_exists($node, 'nid')) {
            throw new DrupalFixturesException('The id of a saved node has to be set.');
        } else {
            $this->cratedNodes[(int) $node->nid] = $node;
            $this->lastCreatedNodeOfType[$node->type] = $node;
        }
    }

    /**
     * @param string $type
     * @return \StdClass | false
     */
    public function getLastCreatedNodeForType($type) {
       if ($this->hasNodeForType($type)) {
           return $this->lastCreatedNodeOfType[$type];
       } else {
           return false;
       }
    }

    /**
     * @param int $id
     * @return \StdClass | false
     */
    public function getNodeById($id) {
        if ($this->hasNode($id)) {
            return $this->cratedNodes[$id];
        } else {
            return false;
        }
    }

    /**
     * @param int $id
     * @return boolean
     */
    public function hasNode($id) {
       return array_key_exists($id, $this->cratedNodes);
    }

    /**
     * @param string $type
     * @return boolean
     */
    public function hasNodeForType($type) {
        return array_key_exists($type, $this->lastCreatedNodeOfType);
    }

    /**
     * @return \StdClass[]
     */
    public function getNodes() {
        return $this->cratedNodes;
    }
}