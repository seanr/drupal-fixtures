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
   * @var array
   */
  private $imageValidators = array(
    'file_validate_is_image' => array()
  );

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
   * @param string $srcPicturePath
   * @param int    $uid
   *
   * @param bool   $isUserImage
   *
   * @throws \Drupal\Fixtures\Exceptions\DrupalFixturesException
   * @return int
   */
  protected function fixturesGetUserPictureId (
    $srcPicturePath,
    $uid = false,
    $isUserImage = false) {

    $srcPicturePath = (string) $this->resolveSourceImagePath($srcPicturePath);

    if ($isUserImage) {
      $this->imageValidators = $this->prepareUserAttachedImageValidators($this->imageValidators);
    }

    $file = $this->copyFileTo($srcPicturePath, 'image', time() . 'img_.jpg', $uid);

    return $file->fid;
  }

  private  function copyFileTo($fileSource, $fileType, $fileDestination,
    $uid = false) {

    $fileDestination = 0 === strpos(
      $fileDestination,
      'public:'
    ) ? $fileDestination : 'public://' . $fileDestination;

    $file = new \StdClass();
    $file->uri = $fileSource;

    if ($uid) {
      $file->uid = (int) $uid;
    }

    // required by file_entity.module line 2275
    $file->type = $fileType;

    if (!drupal_is_writable('public://')) {
      throw new \RuntimeException('Directory: public:// is not writeable!');
    }

    $file = file_copy($file, $fileDestination, FILE_EXISTS_REPLACE);
    if (false === $file) {
      throw new \RuntimeException('file could not be created.');
    }

    return $file;
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

  /**
   * @param $picturePath
   *
   * @return array|bool|mixed
   * @throws DrupalFixturesException
   */
  private function resolveSourceImagePath($picturePath) {
    if (file_exists($picturePath)) {
      $imgSourcePath = realpath($picturePath);
    }
    else if (file_exists(DRUPAL_ROOT . $picturePath)) {
      $imgSourcePath = realpath(DRUPAL_ROOT . $picturePath);
    }
    else {
      throw new DrupalFixturesException(
        $picturePath . ' or ' . DRUPAL_ROOT . $picturePath . ' does not exists.'
      );
    }

    return $imgSourcePath;
  }
}