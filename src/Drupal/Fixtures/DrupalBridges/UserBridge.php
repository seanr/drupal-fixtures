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

      if (isset($user->picture)) {
        $savedUser->picture = $this->fixturesGetUserPictureId($user->picture, $savedUser->uid);
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
    if (false == $savedUser = user_save($user)) {
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

  /**
   * Loads a picture given and returns its id to be saved with the user.
   * Thanks a lot dmytro.
   *
   * @see: http://d.danylevskyi.com/node/7
   *
   * @param string $userPicturePath
   * @param int $uid
   *
   * @return int
   */
  protected function fixturesGetUserPictureId($userPicturePath, $uid) {
    $userPicturePath = (string) $userPicturePath;
    $uid = (int) $uid;
    if (false == file_exists($userPicturePath)) {
      throw new DrupalFixturesException($userPicturePath . ' does not exists.');
    }

    $image_info = image_get_info($userPicturePath);
    // create file object
    $file = new \StdClass();
    $file->uid = $uid;
    $file->uri = $userPicturePath;
    $file->filemime = $image_info['mime_type'];
    $file->status = 0; // Yes! Set status to 0 in order to save temporary file.
    $file->filesize = $image_info['file_size'];

    // standard Drupal validators for user pictures
    $validators = array(
      'file_validate_is_image' => array(),
      'file_validate_image_resolution' => array(variable_get('user_picture_dimensions', '85x85')),
      'file_validate_size' => array(variable_get('user_picture_file_size', '30') * 1024),
    );

    // here all the magic :)
    $errors = file_validate($file, $validators);
    if (empty($errors)) {
      $savedFile = file_save($file);
      return $savedFile;
    } else {
      throw new DrupalFixturesException('Could not save file: ' . $userPicturePath);
    }
  }
}