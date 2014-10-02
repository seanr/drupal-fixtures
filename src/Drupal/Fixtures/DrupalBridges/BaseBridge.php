<?php
/**
 *
 * PHP Version 5.3
 *
 * @author    Mike Lohmann <mike.lohmann@bauermedia.com>
 * @copyright 2014 Bauer Digital KG
 */
namespace Drupal\Fixtures\DrupalBridges;


use Drupal\Fixtures\Exceptions\DrupalFixturesException;
use Drupal\Fixtures\Validators\ValidatorInterface;

abstract class BaseBridge implements BridgeInterface {

  /**
   * @var ValidatorInterface[]
   */
  private $validators = array();

  /**
   * {@inheritDoc}
   */
  abstract public function createFixtures(array $fixtureData);


  /**
   * {@inheritDoc}
   */
  public function validateFixtures(array $fixtureData) {
    /** @var ValidatorInterface $validator */
    foreach ($this->validators as $validator) {
      $validator->validate($fixtureData);
    }
  }

  /**
   * {@inheritDoc}
   */
  public function addValidator(ValidatorInterface $validator) {
    $this->validators[] = $validator;
  }

  /**
   * Loads a picture given and returns its id to be saved with the user/node.
   * Thanks a lot dmytro.
   *
   * @see: http://d.danylevskyi.com/node/7
   *
   * @param string $picturePath
   * @param int    $uid
   *
   * @param bool   $isUserImage
   *
   * @throws \Drupal\Fixtures\Exceptions\DrupalFixturesException
   * @return int
   */
  protected function fixturesGetUserPictureId (
    $picturePath,
    $uid,
    $isUserImage = false) {

    $picturePath = (string) $picturePath;

    if (file_exists($picturePath)) {
      $image_info = image_get_info($picturePath);
    } else if (file_exists(DRUPAL_ROOT . $picturePath)) {
      $image_info = image_get_info(DRUPAL_ROOT. $picturePath);
    } else {
      throw new DrupalFixturesException(
        $picturePath . ' or ' . DRUPAL_ROOT . $picturePath . ' does not exists.'
      );
    }

    // create file object
    $file = new \StdClass();
    $file->uri = $picturePath;
    $file->uid = (int) $uid;

    $existingFile = $this->receiveSavedFileByUri($file);

    if (false == $existingFile) {
      $validators = array(
        'file_validate_is_image' => array()
      );

      if ($isUserImage) {
        $validators = $this->prepareUserAttachedImageValidators($validators);
      }

      $file = $this->createImageFile(
        $validators,
        $file,
        $image_info
      );
    } else {
      $file = $existingFile;
    }

    return (array) $file;
  }

  /**
   * @param \StdClass $file
   *
   * @return bool|\StdClass
   */
  private function receiveSavedFileByUri(\StdClass $file) {

    $savedFile = reset(file_load_multiple(array(), (array) $file));
    if (false == $savedFile) {
      $savedFile = file_save($file);
    };

    return $savedFile;
  }

  /**
   * @param $uid
   * @param $isUserImage
   * @param $file
   * @param $image_info
   *
   * @throws DrupalFixturesException
   */
  private function createImageFile(
    array $validators,
    \StdClass $file,
    array $image_info
  ) {
    $file->filemime = $image_info['mime_type'];
    $file->status = 0; // Yes! Set status to 0 in order to save temporary file.
    $file->filesize = $image_info['file_size'];


    // here all the magic :)
    $errors = file_validate($file, $validators);
    if (empty($errors)) {
      $savedFile = file_save($file);

      return $savedFile->fid;
    }
    else {
      throw new DrupalFixturesException(
        'Could not save file:  due to ' . print_r(
          $errors,
          TRUE
        ) . '.'
      );
    }
  }

  /**
   * @param $validators
   *
   * @return array
   */
  private function prepareUserAttachedImageValidators($validators) {

    return array_merge(
      $validators,
      array(
        'file_validate_image_resolution' => array(
          variable_get(
            'user_picture_dimensions',
            '85x85'
          )
        ),
        'file_validate_size' => array(
          variable_get(
            'user_picture_file_size',
            '30'
          ) * 1024
        ),
      )
    );

  }
}