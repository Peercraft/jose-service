<?php

namespace SpomkyLabs\Service;

/**
 * Class Configuration.
 */
class Configuration
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->options = $options;
    }

    /**
     * @inheritdoc
     */
    public function get($name, $default = null)
    {
        return isset($this->options[$name]) ? $this->options[$name] : $default;
    }

    /**
     * @inheritdoc
     */
    public function set($name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
     * @inheritdoc
     */
    public function remove($name)
    {
        if (array_key_exists($name, $this->options)) {
            unset($this->options[$name]);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getConfigurationKeys()
    {
        return array_keys($this->options);
    }
}
