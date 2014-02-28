<?php
/**
 * Declares the BasicUserFIxturesValidator class.
 *
 * @author     Mike Lohmann <mike.lohmann@deck36.de>
 * @copyright  Copyright (c) 2014 DECK36 GmbH & Co. KG (http://www.deck36.de)
 */

namespace Drupal\Fixtures\Validators;


use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Parser;

class BasicUserFixturesValidatorTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @var Finder
     */
    protected $finder;

    /**
     * @var ValidatorInterface
     */
    private $subjectToTest;

    /**
     * {@inheritDoc}
     */
    public function setUp() {
        $this->parser = new Parser();
        $this->finder = new Finder();
    }

    /**
     * Validates that fixtures are ok
     */
    public function testValidateUserFixturesOk()
    {
        $this->subjectToTest = new BasicUserFixturesValidator();
        $this->validateOk('user*.yaml');
    }

    /**
     * @expectedException \Drupal\Fixtures\Exceptions\ValidationException
     */
    public function testValidateUserFixturesException()
    {
        $this->subjectToTest = new BasicUserFixturesValidator();
        $this->validateOk('wronguser*.yaml');
    }

    /**
     * Validates that fixtures are ok
     */
    public function testValidateNodeFixturesOk()
    {
        $this->subjectToTest = new BasicNodeFixturesValidator();
        $this->validateOk('node--*.yaml');
    }

    /**
     * @expectedException \Drupal\Fixtures\Exceptions\ValidationException
     */
    public function testValidateNodeFixturesException()
    {
        $this->subjectToTest = new BasicNodeFixturesValidator();
        $this->validateOk('wrongnode--*.yaml');
    }

    /**
     * Validates that fixtures are ok
     */
    public function testValidateMenuFixturesOk()
    {
        $this->subjectToTest = new BasicMenuFixturesValidator();
        $this->validateOk('menu--*.yaml');
    }

    /**
     * @expectedException \Drupal\Fixtures\Exceptions\ValidationException
     */
    public function testValidateMenuFixturesException()
    {
        $this->subjectToTest = new BasicMenuFixturesValidator();
        $this->validateException('wrongnode--*.yaml');
    }

    /**
     * @param string $filenamePattern
     */
    private function validateOk($filenamePattern)
    {
        /** @var SplFileInfo $file */
        foreach ($this->finder->files()->name($filenamePattern)->in(__DIR__ . '/../fixtures') as $file) {
            $loadedFixtures = $this->parser->parse($file->getContents());
            $this->assertTrue($this->subjectToTest->validate($loadedFixtures));
        }
    }

    /**
     * @param string $filenamePattern
     */
    private function validateException($filenamePattern)
    {
        /** @var SplFileInfo $file */
        foreach ($this->finder->files()->name($filenamePattern)->in(__DIR__ . '/../fixtures') as $file) {
            $loadedFixtures = $this->parser->parse($file->getContents());
            $this->subjectToTest->validate($loadedFixtures);
        }
    }
}
