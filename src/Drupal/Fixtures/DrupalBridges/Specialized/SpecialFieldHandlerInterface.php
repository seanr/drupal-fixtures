<?php
/**
 * Declares the SpecialFieldHandlerInterface class.
 *
 * @author     Mike Lohmann <mike.lohmann@deck36.de>
 */

namespace Drupal\Fixtures\DrupalBridges\Specialized;

interface SpecialFieldHandlerInterface {
    /**
     * @param \StdClass $node
     * @return int
     */
    public function solveCategory(\StdClass $node);

    /**
     * @param \StdClass $node
     * @return int
     */
    public function solveChannel(\StdClass $node);

    /**
     * @param \StdClass $node
     * @return array
     */
    public function solveTags(\StdClass $node);
} 