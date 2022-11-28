<?php

declare(strict_types=1);

namespace Endroid\PropertyAccess;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor as BasePropertyAccessor;

final class PropertyAccessor
{
    private BasePropertyAccessor $accessor;
    private ExpressionLanguage $language;

    public function __construct()
    {
        $this->accessor = PropertyAccess::createPropertyAccessor();
        $this->language = new ExpressionLanguage();
    }

    /**
     * @param mixed $object
     *
     * @return mixed
     */
    public function getValue($object, string $path)
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
     * @param mixed $object
     * @param mixed $value
     */
    public function setValue($object, string $path, $value): void
    {
        $this->accessor->setValue($object, $path, $value);
    }

    /**
     * @param array<mixed> $objects
     *
     * @return array<mixed>
     */
    public function filter(array $objects, string $expression): array
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
