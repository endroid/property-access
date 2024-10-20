<?php

declare(strict_types=1);

namespace Endroid\PropertyAccess;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor as BasePropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

final readonly class PropertyAccessor implements PropertyAccessorInterface
{
    private BasePropertyAccessor $accessor;

    public function __construct(
        private ExpressionLanguage $language = new ExpressionLanguage(),
    ) {
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    /** @param object|array<mixed> $objectOrArray */
    public function getValue(object|array $objectOrArray, PropertyPathInterface|string $propertyPath): mixed
    {
        $paths = (array) preg_split('#(\[((?>[^\[\]]+)|(?R))*\])#i', strval($propertyPath), -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        for ($i = 0; $i < count($paths); ++$i) {
            $path = trim(strval($paths[$i]), '.');
            if (str_starts_with($path, '[')) {
                ++$i;
            }
            if (preg_match('#[^a-z0-9\.\]\[]+#i', $path)) {
                $expression = trim($path, '[]');
                if (!is_array($objectOrArray)) {
                    throw new \Exception('Filtering is only supported for arrays');
                }
                $objectOrArray = $this->filter($objectOrArray, $expression);
            } else {
                $objectOrArray = $this->accessor->getValue($objectOrArray, $path);
            }

            if (null === $objectOrArray) {
                break;
            }
        }

        return $objectOrArray;
    }

    /** @param object|array<mixed> $objectOrArray */
    public function setValue(object|array &$objectOrArray, PropertyPathInterface|string $propertyPath, mixed $value): void
    {
        $this->accessor->setValue($objectOrArray, $propertyPath, $value);
    }

    /**
     * @param array<mixed> $objectOrArray
     *
     * @return array<mixed>
     */
    public function filter(array &$objectOrArray, string $expression): array
    {
        $filteredObjects = [];

        foreach ($objectOrArray as $object) {
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

    /** @param object|array<mixed> $objectOrArray */
    public function isWritable(object|array $objectOrArray, PropertyPathInterface|string $propertyPath): bool
    {
        return $this->accessor->isWritable($objectOrArray, $propertyPath);
    }

    /** @param object|array<mixed> $objectOrArray */
    public function isReadable(object|array $objectOrArray, PropertyPathInterface|string $propertyPath): bool
    {
        return $this->accessor->isReadable($objectOrArray, $propertyPath);
    }
}
