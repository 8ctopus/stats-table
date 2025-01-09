<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Tools;

class ParameterBag
{
    /**
     * @var array The parameter bag
     */
    private array $bag;

    /**
     * Constructor. Take either an array of a ParameterBag
     * @param  array|ParameterBag        $bag
     * @throws \InvalidArgumentException
     */
    public function __construct(array|self $bag = [])
    {
        if (!is_array($bag) && !($bag instanceof self)) {
            throw new \InvalidArgumentException('Bad constructor call');
        }

        if ($bag instanceof self) {
            $array = $bag->toArray();
        } else {
            $array = $bag;
        }

        $this->bag = $array;
    }

    /**
     * Check if key exists in bag
     * @param  string $key
     * @return bool
     */
    public function has(string $key) : bool
    {
        return array_key_exists($key, $this->bag);
    }

    /**
     * Retrieve value for key $key, returns $defaultValue if not found.
     * @param  string $key
     * @param  null   $defaultValue
     * @return null
     */
    public function get(string $key, $defaultValue = null)
    {
        return $this->has($key) ? $this->bag[$key] : $defaultValue;
    }

    /**
     * @return array
     */
    public function toArray() : array
    {
        return $this->bag;
    }
}
