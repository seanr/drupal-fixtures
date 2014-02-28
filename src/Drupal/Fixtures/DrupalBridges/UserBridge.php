<?php
/**
 *
 * PHP Version 5.3
 *
 * @author Mike Lohmann <mike.lohmann@bauermedia.com>
 * @copyright 2014 Bauer Digital KG
 */
namespace Drupal\Fixtures\DrupalBridges;

use Drupal\Fixtures\Exceptions\DrupalFixturesException;

/**
 * Class UserBridge is used to provide some functionality needed from drupal to create user data
 *
 * @package Drupal\Fixtures\DrupalBridges
 */
class UserBridge extends BaseBridge {

  /**
   * {@inheritDoc}
   */
  public function createFixtures(array $fixtureData) {
    $resultData = array();

    require_once DRUPAL_ROOT . '/' . variable_get('password_inc', 'includes/password.inc');
    foreach ($fixtureData as $key => $user) {
      $user->pass = user_hash_password($user->pass);
      // user->roles are a string coming from yaml
      // then converted into an array like $roles[<roleId>] = true;
      $user->roles = $this->fixturesGetUsersRoles($user->roles);
      $user->timezone = variable_get('date_default_timezone', date_default_timezone_get());
      $savedUser = $this->fixturesSaveUser($user);

      if (isset($user->picture) && $user->picture != 0) {
        $savedUser->picture = $this->fixturesGetUserPictureId($user->picture, $savedUser->uid, true);
        $this->fixturesSaveUser($savedUser);
      }

      $resultData[$key] = $savedUser;
    }

    return $resultData;
  }

  /**
   * @param \StdClass $user
   * @return bool|\StdClass|void
   * @throws DrupalFixturesException
   */
  protected function fixturesSaveUser(\StdClass $user) {
    if (false != $existingUser = user_load_by_mail($user->mail)) {
      $user->uid = $existingUser->uid;
      // actually we cannot edit fixtures and play them in. For that you have to change mail / name.
      $savedUser = true;
    } else {
      $savedUser = user_save($user);
    }

    if (false == $savedUser) {
      throw new DrupalFixturesException('Could not save user: ' . $user->name);
    }
    return $savedUser;
  }


  /**
   * Just extracts the roles out of a role value from the according yaml file.
   *
   * @param string $userRoles
   *
   * @return array
   * @throws DrupalFixturesException
   */
  protected function fixturesGetUsersRoles($userRoles) {
    $roles = array(DRUPAL_AUTHENTICATED_RID => TRUE);
    foreach (preg_split('/\s*,\s*/', $userRoles, 0, PREG_SPLIT_NO_EMPTY) as $role_name) {
      $role = user_role_load_by_name($role_name);
      if ($role != NULL) {
        $roles[$role->rid] = TRUE;
      }
      else {
        throw new DrupalFixturesException("User role not found: '$role_name'", 1);
      }
    }
    return $roles;
  }
}