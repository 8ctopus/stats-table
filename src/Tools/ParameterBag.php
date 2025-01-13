<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Tools;

class ParameterBag
{
    private array $bag;

    /**
     * Constructor
     *
     * @param array|ParameterBag $bag
     */
    public function __construct(array|self $bag = [])
    {
        if ($bag instanceof self) {
            $value = $bag->toArray();
        } else {
            $value = $bag;
        }

        $this->bag = $value;
    }

    /**
     * Check if key exists
     *
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key) : bool
    {
        return array_key_exists($key, $this->bag);
    }

    /**
     * Get value for key
     *
     * @param string $key
     * @param mixed  $defaultValue
     *
     * @return mixed
     */
    public function get(string $key, mixed $defaultValue = null) : mixed
    {
        return $this->has($key) ? $this->bag[$key] : $defaultValue;
    }

    /**
     * Convert to array
     *
     * @return array
     */
    public function toArray() : array
    {
        return $this->bag;
    }
}
