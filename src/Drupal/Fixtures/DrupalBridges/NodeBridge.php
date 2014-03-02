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
  protected function fixtureCreateNode(\StdClass $node) {
    $bodyValue = $node->body;
    $node->body = array();
    $node->body['und'][0]['value'] = $bodyValue;

    $node->body['und'][0]['format'] = 'full_html';
    $node->created = strtotime($node->date);
    $node->changed = $node->created;

    $pathValue = $node->path;
    $node->path = array('alias' => $pathValue);

    // return null in case of success
    node_save($node);

    if (isset($node->picture)
      && $node->picture != 0
      && FALSE != $existingUser = user_load_by_name($node->username)
    ) {
      $node->picture = $this->fixturesGetUserPictureId($node->picture, $existingUser->uid, TRUE);
      node_save($node);
    }

    return $node;
  }
}