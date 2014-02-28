<?php
/**
 * Declares the BasicUserFixtureValidator class.
 *
 * @author     Mike Lohmann <mike.lohmann@deck36.de>
 * @copyright  Copyright (c) 2014 DECK36 GmbH & Co. KG (http://www.deck36.de)
 */

namespace Drupal\Fixtures\Validators;


class BasicMenuFixturesValidator extends BaseFixturesValidator {

    /**
     * @return array
     */
    protected function getKeyMap()
    {
        return array(
            'title' => 1,
            'description' => 1,
            'items' => 1
        );
    }
}