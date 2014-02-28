<?php
/**
 *
 * PHP Version 5.3
 *
 * @author Mike Lohmann <mike.lohmann@bauermedia.com>
 * @copyright 2014 Bauer Digital KG
 */
namespace Drupal\Fixtures\DrupalBridges;


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
}