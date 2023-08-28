<?php

declare(strict_types=1);

namespace Endroid\PropertyAccess\PropertyAccessorTest;

use Endroid\PropertyAccess\PropertyAccessor;
use PHPUnit\Framework\TestCase;

final class PropertyAccessorTest extends TestCase
{
    public function testEquals(): void
    {
        $propertyAccessor = new PropertyAccessor();
        $data = $this->getData();

        $items = $propertyAccessor->getValue($data, 'subs[number==1]');

        $this->assertTrue(1 == count($items));
        $this->assertTrue(1 == $items[0]->number);
    }

    public function testNotEquals(): void
    {
        $propertyAccessor = new PropertyAccessor();
        $data = $this->getData();

        $items = $propertyAccessor->getValue($data, 'subs[number!=1]');

        $this->assertTrue(4 == count($items));
        $this->assertTrue(2 == $items[1]->number);
    }

    public function testGreaterThan(): void
    {
        $propertyAccessor = new PropertyAccessor();
        $data = $this->getData();

        $items = $propertyAccessor->getValue($data, 'subs[number>1]');

        $this->assertTrue(3 == count($items));
        $this->assertTrue(2 == $items[0]->number);
    }

    public function testLowerThan(): void
    {
        $propertyAccessor = new PropertyAccessor();
        $data = $this->getData();

        $items = $propertyAccessor->getValue($data, 'subs[number<3]');

        $this->assertTrue(3 == count($items));
        $this->assertTrue(0 == $items[0]->number);
    }

    private function getData(): \stdClass
    {
        $data = new \stdClass();
        $data->subs = [];
        for ($n = 0; $n < 5; ++$n) {
            $item = new \stdClass();
            $item->number = $n;
            $data->subs[] = $item;
        }

        return $data;
    }
}
