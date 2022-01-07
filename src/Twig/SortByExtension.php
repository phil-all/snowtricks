<?php

namespace App\Twig;

use Twig\TwigFilter;
use InvalidArgumentException;
use Twig\Extension\AbstractExtension;

class SortByExtension extends AbstractExtension
{
    /**
     * Field to sort by.
     *
     * @var string
     */
    private $field;

    public function getFilters()
    {
        return [
            new TwigFilter('sortBy', [$this, 'sortBy'])
        ];
    }

    /**
     * Filter to sort by field.
     *
     * @param mixed $item item to sort
     * @param string $field field to sort by
     * @param string $direction ordering way
     *
     * @return array
     */
    public function sortBy(mixed $item, string $field, string $direction = 'asc'): array
    {
        $this->setSortBy($field);
        $this->verifyCollection($item);

        $item = $item->toArray();

        if (count($item) >= 1) {
            @usort($item, function ($a, $b) use ($direction) {
                $flip = ($direction === 'desc') ? -1 : 1;

                $a_sort_value = $this->getSortedValue($a);
                $b_sort_value = $this->getSortedValue($b);

                return $flip * $this->scaleFactor($a_sort_value, $b_sort_value);
            });
        }

        return $item;
    }

    /**
     * Checks if item correspond to a doctrine persistent collection.
     *
     * @param mixed $item Collection item to be sorted
     *
     * @return void
     */
    private function verifyCollection(mixed $item): void
    {
        if (!is_a($item, 'Doctrine\ORM\PersistentCollection')) {
            throw new InvalidArgumentException('sortBy filter variable isn\'t doctrine persistent collection');
        }
    }

    /**
     * Returns an integer corresponding to a scale factor.
     *
     * @param mixed $a_value
     * @param mixed $b_value
     *
     * @return integer
     */
    private function scaleFactor(mixed $a_value, mixed $b_value): int
    {
        if ($a_value === $b_value) {
            return 0;
        }

        return ($a_value < $b_value) ? -1 : 1;
    }

    /**
     * Returns a value depending on a field.
     *
     * @param mixed $element
     *
     * @return mixed
     */
    private function getSortedValue(mixed $element): mixed
    {
        if (is_array($element)) {
            return $element[$this->field];
        } elseif (method_exists($element, 'get' . ucfirst($this->field))) {
            return $element->{'get' . ucfirst($this->field)}();
        } else {
            return $element->$this->field;
        }
    }

    /**
     * Sets field attribute.
     *
     * @param string $field
     *
     * @return void
     */
    private function setSortBy(string $field): void
    {
        $this->field = $field;
    }
}
