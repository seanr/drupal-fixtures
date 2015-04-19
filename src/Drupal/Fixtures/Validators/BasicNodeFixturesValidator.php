<?php
/**
 * Declares the BasicMenuFixturesValidator class.
 *
 * @author     Mike Lohmann <mike.lohmann@deck36.de>
 * @copyright  Copyright (c) 2014 DECK36 GmbH & Co. KG (http://www.deck36.de)
 */

namespace Drupal\Fixtures\Validators;

use Drupal\Fixtures\Exceptions\ValidationException;
use Drupal\Fixtures\Validators\Specialized\SpecializedValidatorException;
use Drupal\Fixtures\Validators\Specialized\SpecializedValidatorInterface;

class BasicNodeFixturesValidator implements ValidatorInterface, NodeFixturesValidatorInterface {

  /**
   * @var ValidatorInterface[]
   */
  private $specializedValidators = array();

  /**
   * {@inheritDoc}
   */
  public function validate(array $fixtures) {
    if (0 == count($this->specializedValidators)) {
      return TRUE;
    }

    foreach ($fixtures as $node_name => $node) {
      if (is_array($node)) {
        foreach ($node as $singleNode) {
          $singleNode = (object) $singleNode;
          $this->validateNode($singleNode, $fixtures);
        }
      }
      else {
        $node = (object) $node;
        $this->validateNode($node, $fixtures);
      }
    }
    return TRUE;
  }

  private function validateNode(\StdClass $node, $fixtures) {
    if ($this->hasSpecializedValidator($node->type)) {
      $this->getSpecializedValidator($node->type)->validate($fixtures);
    }
    else {
      watchdog(
        sprintf(
          "There is no validator for node type: %s.", $node->type),
        'info'
      );
    }
  }

  /**
   * {@inheritDoc}
   */
  public function addSpecializedValidator(
    SpecializedValidatorInterface $validator
  ) {
    $this->specializedValidators[$validator->getName()] = $validator;
  }

  /**
   * {@inheritDoc}
   */
  public function hasSpecializedValidator($name) {
    return array_key_exists($name, $this->specializedValidators);
  }

  /**
   * {@inheritDoc}
   */
  public function getSpecializedValidator($name) {
    if (!$this->hasSpecializedValidator($name)) {
      throw new SpecializedValidatorException(
        'Validator ' . $name . ' does not exists.'
      );
    }

    return $this->specializedValidators[$name];
  }
}