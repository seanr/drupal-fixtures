<?php
/**
 * Declares the BaseSpecializedBridge class.
 *
 * @author     Mike Lohmann <mike.lohmann@deck36.de>
 */

namespace Drupal\Fixtures\DrupalBridges\Specialized;


abstract class BaseSpecializedNodeBridge implements SpecializedBridgeInterface {
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
   */
  protected function solveCategory(\StdClass $node) {
    $category = NULL;
    if (property_exists($node, 'field_category')) {
      // Workaround for missing taxonamy vocabulary selector in drupal driver
      foreach (taxonomy_get_term_by_name($node->field_category) as $term) {
        if ($term->vocabulary_machine_name == 'category'
          && $term->name == $node->field_category
        ) {
          $category = (int) $term->tid;

          // take the first one found (mostly it is for bravo. Sometimes for bravo-girl)
          break;
        }
      }

      unset($node->field_category);
    }

    return $category;
  }

  /**
   * @param \StdClass $node
   */
  protected function solveChannel(\StdClass $node) {
    $channel = NULL;
    if (property_exists($node, 'field_channel')) {
      // Workaround for missing taxonamy vocabulary selector in drupal driver
      foreach (taxonomy_get_term_by_name($node->field_channel) as $term) {
        if ($term->vocabulary_machine_name == 'category'
          && $term->name == $node->field_channel
        ) {
          $channel = array((int) $term->tid);

          break;
        }
      }
      unset($node->field_channel);
    }
    return $channel;
  }

  /**
   * @param \StdClass $node
   */
  protected function solveTags(\StdClass $node) {
    $tags = array();
    if (property_exists($node, 'field_tags') && is_array($node->field_tags)) {
      foreach ($node->field_tags as $tag) {
        // Workaround for missing taxonamy vocabulary selector in drupal driver
        foreach (taxonomy_get_term_by_name($tag) as $term) {
          if ($term->vocabulary_machine_name == 'tags'
            && $term->name == $tag
          ) {
            $tags[] = $term->tid;
            break;
          }
        }
      }
      unset($node->field_tags);
    }
    return $tags;
  }

  /**
   * @param \StdClass $node
   *
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
   * @param \StdClass              $fixNode
   * @param \EntityDrupalWrapper $wrapper
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