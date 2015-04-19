<?php
/**
 * Declares the NodeFixturesValidatorInterface class.
 *
 * @author     Mike Lohmann <mike.lohmann@deck36.de>
 */

namespace Drupal\Fixtures\Validators;

use Drupal\Fixtures\Validators\Specialized\SpecializedValidatorInterface;

interface NodeFixturesValidatorInterface {
  /**
   * @param SpecializedValidatorInterface $validator
   *
   * @return mixed
   */
  public function addSpecializedValidator(
    SpecializedValidatorInterface $validator
  );

  /**
   * @param string $name
   *
   * @return Boolean
   */
  public function hasSpecializedValidator($name);

  /**
   * @param string $name
   *
   * @return ValidatorInterface | SpecializedValidatorInterface
   */
  public function getSpecializedValidator($name);
} 