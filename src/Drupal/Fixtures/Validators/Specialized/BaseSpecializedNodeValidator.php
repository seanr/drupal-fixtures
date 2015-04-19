<?php
/**
 * Declares the BaseSpecializedNodeValidator class.
 *
 * @author     Mike Lohmann <mike.lohmann@deck36.de>
 */

namespace Drupal\Fixtures\Validators\Specialized;


use Drupal\Fixtures\Validators\BaseFixturesValidator;

abstract class BaseSpecializedNodeValidator extends BaseFixturesValidator implements
  SpecializedValidatorInterface {
  /**
   * @const string
   */
  const NAME = '';

  /**
   * {@inheritDoc}
   */
  public function getName() {
    if ('' == static::NAME) {
      throw new SpecializedValidatorException(
        'You have to give a name for a specialized validator.'
      );
    }

    return static::NAME;
  }

} 