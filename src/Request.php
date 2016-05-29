<?php

namespace Fesor\RequestObject;

class Request implements ValidationRequiredRequest
{
    private $payload;

    /**
     * Request constructor.
     * @param $payload
     */
    public function __construct(array $payload = [])
    {
        $this->payload = $payload;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function validationGroup()
    {
        return null;
    }

    /**
     * @param string $name
     * @param mixed|null $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        return $this->has($name) ?
            $this->payload[$name] : $default;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->payload);
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->payload;
    }
}