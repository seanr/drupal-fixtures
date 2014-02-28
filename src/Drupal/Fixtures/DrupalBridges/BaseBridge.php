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
use Drupal\Fixtures\Validators\ValidatorInterface;

abstract class BaseBridge implements BridgeInterface{

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
    public function validateFixtures(array $fixtureData)
    {
        /** @var ValidatorInterface $validator */
        foreach ($this->validators as $validator) {
            $validator->validate($fixtureData);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function addValidator(ValidatorInterface $validator)
    {
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
    protected function fixturesGetUserPictureId($picturePath, $uid, $isUserImage = false) {
        $picturePath = (string) $picturePath;
        $uid = (int) $uid;
        if (false == file_exists($picturePath)) {
            throw new DrupalFixturesException($picturePath . ' does not exists.');
        }

        $image_info = image_get_info($picturePath);
        // create file object
        $file = new \StdClass();
        $file->uid = $uid;
        $file->uri = $picturePath;
        $file->filemime = $image_info['mime_type'];
        $file->status = 0; // Yes! Set status to 0 in order to save temporary file.
        $file->filesize = $image_info['file_size'];

        $validators = array(
            'file_validate_is_image' => array()
        );

        // standard Drupal validators for user pictures
        if ($isUserImage) {
            $validators = array_merge($validators, array(
                'file_validate_image_resolution' => array(variable_get('user_picture_dimensions', '85x85')),
                'file_validate_size' => array(variable_get('user_picture_file_size', '30') * 1024),
            ));
        }

        // here all the magic :)
        $errors = file_validate($file, $validators);
        if (empty($errors)) {
            $savedFile = file_save($file);
            return $savedFile;
        } else {
            throw new DrupalFixturesException('Could not save file: ' . $picturePath);
        }
    }
}