<?php
/**
 * Declares the BaseSpecializedBridge class.
 *
 * @author     Mike Lohmann <mike.lohmann@deck36.de>
 */

namespace Drupal\Fixtures\DrupalBridges\Specialized;


use Drupal\Fixtures\DrupalBridges\ImageBridge;

abstract class BaseSpecializedNodeBridge implements SpecializedBridgeInterface {
  use ImageBridge;

  /**
   * @const string
   */
  const NAME = '';

  /**
   * {@inheritDoc}
   */
  public function getName() {
    if ('' == static::NAME) {
      throw new SpecializedBridgeException(
        'You have to give a name for a specialized bridge.'
      );
    }

    return static::NAME;
  }

  /**
   * {@inheritDoc}
   */
  abstract public function process(\StdClass $fixNode);

  /**
   * @param \StdClass $node
   *
   * @return \StdClass
   * @throws SpecializedBridgeException
   */
  protected function prepareNode(\StdClass $node) {
    // return null in case of success
    if (null !== node_object_prepare($node)) {
      throw new SpecializedBridgeException('Node Object Preparation failed.');
    }

    return $node;
  }

  /**
   * @param \StdClass $node
   * @param string    $type
   *
   * @return \EntityDrupalWrapper
   */
  protected function wrapNode(\StdClass $node, $type = 'node') {
      return entity_metadata_wrapper($type, $node);
  }

  /**
   * @param \StdClass                                   $fixNode
   * @param \EntityDrupalWrapper|\EntityMetadataWrapper $wrapper
   */
  protected function solveFields(\StdClass $fixNode, \EntityMetadataWrapper $wrapper) {
    foreach ($fixNode as $fieldname => $fieldValue) {
      if (0 === strpos($fieldname, 'field_')) {
        $wrapper->$fieldname->set($fieldValue);
      }
      else {
        // unsupported fields
        unset($fixNode->$fieldname);
      }
    }
  }
}