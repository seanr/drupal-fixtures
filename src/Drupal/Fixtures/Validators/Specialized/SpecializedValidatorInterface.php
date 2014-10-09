<?php
/**
 * Declares the SpecializedValidatorInterface class.
 *
 * @author     Mike Lohmann <mike.lohmann@deck36.de>
 */

namespace Drupal\Fixtures\Validators\Specialized;


interface SpecializedValidatorInterface {
  /**
   * @throws SpecializedValidatorException
   * @return string
   */
  public function getName();
} 