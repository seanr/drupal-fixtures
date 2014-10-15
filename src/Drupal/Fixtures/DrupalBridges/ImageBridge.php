<?php
/**
 * Declares the ImageBridge class.
 *
 * @author     Mike Lohmann <mike.lohmann@deck36.de>
 */

namespace Drupal\Fixtures\DrupalBridges;


use Drupal\Fixtures\Exceptions\DrupalFixturesException;

trait ImageBridge {

  /**
   * @var array
   */
  private $imageValidators = array(
    'file_validate_is_image' => array()
  );

  /**
   * Loads a picture given and returns its id to be saved with the user/node.
   * Thanks a lot dmytro.
   *
   * @see: http://d.danylevskyi.com/node/7
   *
   * @param string $srcPicturePath
   * @param int    $uid
   * @param bool   $isUserImage
   *
   * @throws \Drupal\Fixtures\Exceptions\DrupalFixturesException
   * @return int
   */
  protected function fixturesGetPictureId(
    $srcPicturePath,
    $uid = -1,
    $isUserImage = false
  ) {

    $srcPicturePath = (string) $this->resolveSourceImagePath($srcPicturePath);

    if ($isUserImage) {
      $this->imageValidators = $this->prepareUserAttachedImageValidators(
        $this->imageValidators
      );
    }

    $file = $this->copyFileTo(
      $srcPicturePath,
      'image',
      pathinfo($srcPicturePath)['filename'] . '_img_.jpg',
      $uid
    );

    return $file->fid;
  }

  private function copyFileTo(
    $fileSource,
    $fileType,
    $fileDestination,
    $uid = FALSE
  ) {

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

    if (!$this->drupalIsWriteable('public://')) {
      throw new \RuntimeException('Directory: public:// is not writeable!');
    }

    $file = file_copy($file, $fileDestination, FILE_EXISTS_REPLACE);
    if (FALSE === $file) {
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

  /**
   * @param $path
   * @return bool
   */
  private function drupalIsWriteable($path) {
    //NOTE: use a trailing slash for folders!!!
    if ($path{strlen($path)-1} == '/') { // recursively return a temporary file path
      return $this->drupalIsWriteable($path.uniqid(mt_rand()).'.tmp');
    } elseif (is_dir($path)) {
      return $this->drupalIsWriteable($path.'/'.uniqid(mt_rand()).'.tmp');
    }

    // check tmp file for read/write capabilities
    $rm = file_exists($path);
    $f = @fopen($path, 'a');
    if ($f === FALSE) {
      return FALSE;
    }
    fclose($f);
    if (!$rm) {
      unlink($path);
    }
    return TRUE;
  }
} 