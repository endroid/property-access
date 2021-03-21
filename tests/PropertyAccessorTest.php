<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\PropertyAccess\PropertyAccessorTest;

use Endroid\PropertyAccess\PropertyAccessor;
use PHPUnit\Framework\TestCase;
use stdClass;

class PropertyAccessorTest extends TestCase
{
    public function testEquals()
    {
        $data = $this->getData();

        $items = $this->getPropertyAccessor()->getValue($data, 'subs[number==1]');

        $this->assertTrue(1 == count($items));
        $this->assertTrue(1 == $items[0]->number);
    }

    public function testNotEquals()
    {
        $data = $this->getData();

        $items = $this->getPropertyAccessor()->getValue($data, 'subs[number!=1]');

        $this->assertTrue(4 == count($items));
        $this->assertTrue(2 == $items[1]->number);
    }

    public function testGreaterThan()
    {
        $data = $this->getData();

        $items = $this->getPropertyAccessor()->getValue($data, 'subs[number>1]');

        $this->assertTrue(3 == count($items));
        $this->assertTrue(2 == $items[0]->number);
    }

    public function testLowerThan()
    {
        $data = $this->getData();

        $items = $this->getPropertyAccessor()->getValue($data, 'subs[number<3]');

        $this->assertTrue(3 == count($items));
        $this->assertTrue(0 == $items[0]->number);
    }

    private function getPropertyAccessor()
    {
        return new PropertyAccessor();
    }

    private function getData()
    {
        $data = new stdClass();
        $data->subs = [];
        for ($n = 0; $n < 5; ++$n) {
            $item = new stdClass();
            $item->number = $n;
            $data->subs[] = $item;
        }

        return $data;
    }
}
