<?php
/**
 *
 * PHP Version 5.3
 *
 * @author Mike Lohmann <mike.lohmann@bauermedia.com>
 * @copyright 2014 Bauer Digital KG
 */
namespace Drupal\Fixtures\DrupalBridges;

/**
 * Class MenuBridge is used to provide some functionality needed from drupal to create menu data
 *
 * @package Drupal\Fixtures\DrupalBridges
 */
class MenuBridge implements BridgeInterface {

  /**
   * {@inheritDoc}
   */
  public function createFixtures(array $fixtureData) {
    foreach ($fixtureData as $menu_name => $menu_array) {
      // entering loop for each defined menu in this file.
      // check if menu with key doesn't already exist, otherwise create new menu
      $menu = menu_load($menu_name);
      if (!$menu) {
        $menu = array();
        $menu['menu_name'] = $menu_name;
        $menu['title'] = $menu_array['title'];
        $menu['description'] = $menu_array['description'];
        menu_save($menu);
        watchdog(sprintf("Created menu %s.", $menu_name), 'info');
      }
      else {
        watchdog(sprintf("Menu %s already exists.", $menu_name), 'info');
      }

      // Create menu items
      $this->fixturesCreateMenuItem($menu_array['items'], $menu, 0);
    }
  }


  protected function fixturesCreateMenuItem($items, $menu, $plid = 0) {
    foreach ($items as $item_key => $item_value) {
      $item = array();

      // Menu item may be pre-existing to allow adding children to existing item
      if (!empty($item_value['exists']) && $item_value['exists'] == TRUE) {
        $item = menu_link_get_preferred($item_value['link'], $menu['menu_name']);
        watchdog('fixtures', print_r($item, TRUE));
      }
      else {
        $path = path_load(array('alias' => $item_value['link']));
        if ($path) {
          $item['link_path'] = $path['source'];
        }
        else {
          // Default path if no path is found or the current menu item.
          $item['link_path'] = 'node';
        }

        $item['module'] = 'menu';
        $item['link_title'] = $item_value['title'];
        $item['menu_name'] = $menu['menu_name'];
        $item['plid'] = $plid;
        menu_link_save($item);
      }
      if (array_key_exists('items', $item_value) && is_array($item_value['items'])) {
        $this->fixturesCreateMenuItem($item_value['items'], $menu, $item['mlid']);
      }
    }
  }
}