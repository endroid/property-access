<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\PropertyAccess;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor as BasePropertyAccessor;

class PropertyAccessor
{
    /**
     * @var BasePropertyAccessor
     */
    protected $accessor;

    /**
     * @var ExpressionLanguage
     */
    protected $language;

    /**
     * Creates a new instance.
     */
    public function __construct()
    {
        $this->accessor = PropertyAccess::createPropertyAccessor();
        $this->language = new ExpressionLanguage();
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
        $paths = (array) preg_split('#(\[((?>[^\[\]]+)|(?R))*\])#i', $path, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        for ($i = 0; $i < count($paths); ++$i) {
            $path = trim((string) $paths[$i], '.');
            if ('[' == substr($path, 0, 1)) {
                ++$i;
            }
            if (preg_match('#[^a-z0-9\.\]\[]+#i', $path)) {
                $expression = trim($path, '[]');
                $object = $this->filter($object, $expression);
            } else {
                $object = $this->accessor->getValue($object, $path);
            }

            if (null === $object) {
                break;
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
     * @param string $expression
     *
     * @return array
     */
    public function filter(array $objects, $expression)
    {
        $filteredObjects = [];

        foreach ($objects as $key => $object) {
            try {
                if (@$this->language->evaluate('object.'.$expression, ['object' => $object])) {
                    $filteredObjects[] = $object;
                }
            } catch (\Exception $exception) {
                // Property does not exist: ignore this item
            }
        }

        return $filteredObjects;
    }
}
