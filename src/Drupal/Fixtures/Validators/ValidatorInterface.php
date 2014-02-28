<?php
/**
 *
 * PHP Version 5.3
 *
 * @author Mike Lohmann <mike.lohmann@bauermedia.com>
 * @copyright 2014 Bauer Digital KG
 */
namespace Drupal\Fixtures\Validators;

use Drupal\Fixtures\Exceptions\ValidationException;

/**
 * Interface ValidatorInterface provides possibility to validate fixtures.
 *
 * @package Drupal\Fixtures\Validators
 */
interface ValidatorInterface {
    /**
     * @param array $fixtures
     *
     * @return bool
     * @throws ValidationException
     */
    public function validate(array $fixtures);
}