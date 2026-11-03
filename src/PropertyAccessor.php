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
    #[\Override]
    public function getValue(object|array $objectOrArray, PropertyPathInterface|string $propertyPath): mixed
    {
        $paths = (array) preg_split(
            '#(\[((?>[^\[\]]+)|(?R))*\])#i',
            strval($propertyPath),
            -1,
            PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE,
        );

        for ($i = 0; $i < count($paths); ++$i) {
            $path = trim(strval($paths[$i] ?? ''), characters: '.');
            if (str_starts_with($path, '[')) {
                ++$i;
            }
            $isExpression = preg_match('#[^a-z0-9\.\]\[]+#i', $path);
            if ($isExpression) {
                $expression = trim($path, characters: '[]');
                if (!is_array($objectOrArray)) {
                    throw new \Exception('Filtering is only supported for arrays');
                }
                $items = $objectOrArray;
                $objectOrArray = $this->filter($items, $expression);
            }
            if (!$isExpression) {
                if (!is_array($objectOrArray) && !is_object($objectOrArray)) {
                    throw new \TypeError('Property access requires an object or array');
                }
                /** @var mixed $objectOrArray Property reads can return any value, including scalar leaves. */
                $objectOrArray = $this->accessor->getValue($objectOrArray, $path);
            }

            if (null === $objectOrArray) {
                break;
            }
        }

        return $objectOrArray;
    }

    /**
     * @template T of object|array<mixed>
     * @param T $objectOrArray
     * @param-out ($objectOrArray is array ? array<mixed> : T) $objectOrArray
     */
    #[\Override]
    public function setValue(
        object|array &$objectOrArray,
        PropertyPathInterface|string $propertyPath,
        mixed $value,
    ): void {
        $this->accessor->setValue($objectOrArray, $propertyPath, $value);
    }

    /**
     * @template T
     * @param array<T> $objectOrArray
     *
     * @return list<T>
     */
    public function filter(array &$objectOrArray, string $expression): array
    {
        $filteredObjects = [];

        foreach ($objectOrArray as $object) {
            try {
                if (@$this->language->evaluate('object.' . $expression, ['object' => $object])) {
                    $filteredObjects[] = $object;
                }
            } catch (\Exception $exception) {
                // Property does not exist: ignore this item
                continue;
            }
        }

        return $filteredObjects;
    }

    /** @param object|array<mixed> $objectOrArray */
    #[\Override]
    public function isWritable(object|array $objectOrArray, PropertyPathInterface|string $propertyPath): bool
    {
        return $this->accessor->isWritable($objectOrArray, $propertyPath);
    }

    /** @param object|array<mixed> $objectOrArray */
    #[\Override]
    public function isReadable(object|array $objectOrArray, PropertyPathInterface|string $propertyPath): bool
    {
        return $this->accessor->isReadable($objectOrArray, $propertyPath);
    }
}
