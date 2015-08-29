<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace SpomkyLabs\Service;

/**
 * Class Configuration.
 */
class Configuration
{
    /**
     * @var array
     */
    private $options;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return null|mixed
     */
    public function get($name, $default = null)
    {
        return isset($this->options[$name]) ? $this->options[$name] : $default;
    }

    /**
     * @param string     $name
     * @param null|mixed $value
     *
     * @return self
     */
    public function set($name, $value)
    {
        $this->options[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function remove($name)
    {
        if (array_key_exists($name, $this->options)) {
            unset($this->options[$name]);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getConfigurationKeys()
    {
        return array_keys($this->options);
    }
}
