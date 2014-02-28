<?php
/**
 * Declares the BaseFixturesValidator class.
 *
 * @author     Mike Lohmann <mike.lohmann@deck36.de>
 * @copyright  Copyright (c) 2014 DECK36 GmbH & Co. KG (http://www.deck36.de)
 */

namespace Drupal\Fixtures\Validators;


use Drupal\Fixtures\Exceptions\ValidationException;

abstract class BaseFixturesValidator implements ValidatorInterface {

    /**
     * @return array
     */
    abstract protected function getKeyMap();

    /**
     * {@inheritDoc}
     */
    public function validate(array $fixtures)
    {
        foreach ($fixtures as $fixture) {
            $numberKeysDiff = count(array_diff_key($this->getKeyMap(), (array)$fixture));
            if ($numberKeysDiff != 0) {
                throw new ValidationException(
                    'The fixtures ' . print_r($fixture, 1) . ' does not contain all needed
                    fields: ' . print_r($this->getKeyMap(), 1) . '.');
            }
        }

        return true;
    }
}