<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\PropertyAccess\PropertyAccessorTest;

use Endroid\PropertyAccess\PropertyAccessor;
use PHPUnit_Framework_TestCase;
use stdClass;

class PropertyAccessorTest extends PHPUnit_Framework_TestCase
{
    /**
     * Check the equals operator.
     */
    public function testEquals()
    {
        $data = $this->getData();

        $items = $this->getPropertyAccessor()->getValue($data, 'subs[number==1]');

        $this->assertTrue(count($items) == 1);
        $this->assertTrue($items[0]->number == 1);
    }

    /**
     * Check the not equals operator.
     */
    public function testNotEquals()
    {
        $data = $this->getData();

        $items = $this->getPropertyAccessor()->getValue($data, 'subs[number!=1]');

        $this->assertTrue(count($items) == 4);
        $this->assertTrue($items[1]->number == 2);
    }

    /**
     * Check the greater than operator.
     */
    public function testGreaterThan()
    {
        $data = $this->getData();

        $items = $this->getPropertyAccessor()->getValue($data, 'subs[number>1]');

        $this->assertTrue(count($items) == 3);
        $this->assertTrue($items[0]->number == 2);
    }

    /**
     * Check the greater than operator.
     */
    public function testLowerThan()
    {
        $data = $this->getData();

        $items = $this->getPropertyAccessor()->getValue($data, 'subs[number<3]');

        $this->assertTrue(count($items) == 3);
        $this->assertTrue($items[0]->number == 0);
    }

    /**
     * Returns the property accessor.
     *
     * @return PropertyAccessor
     */
    protected function getPropertyAccessor()
    {
        return new PropertyAccessor();
    }

    /**
     * Returns some example data to work with.
     *
     * @return stdClass
     */
    protected function getData()
    {
        $data = new stdClass();
        $data->subs = array();
        for ($n = 0; $n < 5; $n++) {
            $item = new stdClass();
            $item->number = $n;
            $data->subs[] = $item;
        }

        return $data;
    }
}
