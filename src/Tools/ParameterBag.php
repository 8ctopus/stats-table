<?php

declare(strict_types=1);

namespace Oct8pus\StatsTable\Tools;

class ParameterBag
{
    private array $options;

    /**
     * Constructor
     *
     * @param array|self $options
     */
    public function __construct(array|self $options = [])
    {
        if ($options instanceof self) {
            $options = $options->toArray();
        }

        $this->options = array_merge([
            'decimals_count' => 2,
            'decimals_separator' => '.',
            'thousands_separator' => '\'',
        ], $options);
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
        return array_key_exists($key, $this->options);
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
        return $this->has($key) ? $this->options[$key] : $defaultValue;
    }

    /**
     * Set value for key
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return self
     */
    public function set(string $key, mixed $value) : self
    {
        $this->options[$key] = $value;

        return $this;
    }

    /**
     * Convert to array
     *
     * @return array
     */
    public function toArray() : array
    {
        return $this->options;
    }
}
