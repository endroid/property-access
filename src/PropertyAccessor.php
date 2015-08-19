<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\PropertyAccess;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor as BasePropertyAccessor;

class PropertyAccessor
{
    /**
     * @var BasePropertyAccessor
     */
    protected $accessor;

    /**
     * Creates a new instance.
     */
    public function __construct()
    {
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * Returns the value at the given path.
     *
     * @param mixed  $object
     * @param string $path
     *
     * @return mixed
     */
    public function getValue($object, $path)
    {
        $paths = preg_split('#(\[[^\]]+?=[^\[]+?\])#i', $path, null, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        while (count($paths) > 0) {
            $path = array_shift($paths);
            if (strpos($path, '=') === false) {
                $object = $this->accessor->getValue($object, $path);
            } else {
                list($filterPath, $filterValue) = explode('=', trim($path, '[]'));
                $object = $this->filter($object, $filterPath, $filterValue);
            }
        }

        return $object;
    }

    /**
     * Sets the value at the given path.
     *
     * @param mixed  $object
     * @param string $path
     * @param mixed  $value
     */
    public function setValue($object, $path, $value)
    {
        $this->accessor->setValue($object, $path, $value);
    }

    /**
     * Returns the objects filtered by the given path value.
     *
     * @param array  $objects
     * @param string $path
     * @param string $value
     *
     * @return array
     */
    public function filter(array $objects, $path, $value)
    {
        $filteredObjects = array();

        foreach ($objects as $key => $object) {
            if ($this->accessor->getValue($object, $path) == $value) {
                $filteredObjects[] = $object;
            }
        }

        return $filteredObjects;
    }
}
