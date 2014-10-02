<?php
/**
 *
 * PHP Version 5.3
 *
 * @author    Mike Lohmann <mike.lohmann@bauermedia.com>
 * @copyright 2014 Bauer Digital KG
 */
namespace Drupal\Fixtures\DrupalBridges;

/**
 * Class NodeBridge is used to provide some functionality needed from drupal to create node data
 *
 * @package Drupal\Fixtures\DrupalBridges
 */
class NodeBridge extends BaseBridge {

  /**
   * {@inheritDoc}
   */
  public function createFixtures(array $fixtureData) {
    $resultData = array();

    foreach ($fixtureData as $node_name => $node) {
      $resultData[$node_name] = $this->fixtureCreateNode($node);
    }

    return $resultData;
  }

  /**
   * Do some modifications and add some fields to the given node obj.
   *
   * @param \StdClass $node
   *
   * @return \StdClass
   */
  protected function fixtureCreateNode(\StdClass $fixnode) {
    $node = new \StdClass();
    $node->is_new = true;
    $node->title = $fixnode->title;
    unset($fixnode->title);

    $node->language = $fixnode->language;
    unset($fixnode->language);

    $node->type = $fixnode->type;
    unset($fixnode->type);

    $existingUser = user_load_by_name($fixnode->author);
    if (false !== $existingUser) {
      $node->uid = $existingUser->uid;
    }
    unset($fixnode->author);

    // attach an image to the node
    if (isset($fixnode->field_image)
      && 0 !== $fixnode->field_image
      && false !== $existingUser
    ) {
      $fid = $this->fixturesGetUserPictureId(
        $fixnode->field_image,
        $existingUser->uid, false
      );
      $display = 1;

      $fixnode->field_image = array('fid' => $fid, 'display' => $display);
    } else if (isset($fixnode->field_image)) {
      unset($fixnode->field_image);
    }

    // return null in case of success
    node_object_prepare($node);
    $node->created = strtotime($fixnode->date);
    unset($fixnode->date);
    $node->changed = time();

    // Published or not published that is here the question
    // 1 = published
    $node->status = property_exists($fixnode, 'status') ? $fixnode->status : 1;

    // Shown on startpage or not
    // 1 = show
    $node->promote = property_exists($fixnode, 'promote') ? $fixnode->promote : 1;

    $wrapper = entity_metadata_wrapper('node', $node);
    $wrapper->body->set($fixnode->body);
    unset($fixnode->body);

    if (null != $categoryTermId = $this->solveCategory($fixnode)) {
      $wrapper->field_category->set($categoryTermId);
    }

    if (null != $channelTermId = $this->solveChannel($fixnode)) {
      $wrapper->field_channel->set($channelTermId);
    }

    $tags = $this->solveTags($fixnode);
    if (0 < count($tags)) {
      $wrapper->field_tags->set($tags);
    }


    foreach($fixnode as $fieldname => $fieldValue) {
      if (0 === strpos($fieldname, 'field_')) {
        $wrapper->$fieldname->set($fieldValue);
      } else {
        // unsupported fields
        unset($fixnode->$fieldname);
      }
    }

    $wrapper->save();
    $node = $wrapper->value();
    return $node;
  }

  /**
   * @param \StdClass $node
   */
  private function solveCategory(\StdClass $node) {
    $category = null;
    if (property_exists($node, 'field_category')) {
      // Workaround for missing taxonamy vocabulary selector in drupal driver
      foreach (taxonomy_get_term_by_name($node->field_category) as $term) {
        if ($term->vocabulary_machine_name == 'category'
          && $term->name == $node->field_category
        ) {
          $category = (int)$term->tid;

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
  private function solveChannel(\StdClass $node) {
    $channel = null;
    if (property_exists($node, 'field_channel')) {
       // Workaround for missing taxonamy vocabulary selector in drupal driver
      foreach (taxonomy_get_term_by_name($node->field_channel) as $term) {
        if ($term->vocabulary_machine_name == 'category'
          && $term->name == $node->field_channel
        ) {
          $channel = array((int)$term->tid);

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
  private function solveTags(\StdClass $node) {
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
}