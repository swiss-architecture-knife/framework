<?php

namespace Swark\Frontend\Domain\Architecture;

use ArrayAccess;
use InvalidArgumentException;
use JsonSerializable;

class SafeArray implements JsonSerializable, ArrayAccess
{

    /**
     * @var array
     */
    private $data;

    /**
     * SafeArray constructor.
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * @param array $target
     * @param array $indices
     *
     * @return array|mixed|null
     */
    public function safeGet(array $target, $indices)
    {
        $movingTarget = $target;

        foreach ($indices as $index) {
            $isArray = is_array($movingTarget) || $movingTarget instanceof ArrayAccess;
            if (!$isArray || !isset($movingTarget[$index])) return NULL;

            $movingTarget = $movingTarget[$index];
        }

        return $movingTarget;
    }

    /**
     * @param array $keys
     *
     * @return array|mixed|null
     */
    public function getKeys(array $keys)
    {
        return static::safeGet($this->data, $keys);
    }

    /**
     * <p>Access nested array index values by providing a dot notation access string.</p>
     * <p>Example: $safeArrayGetter->get('customer.paymentInfo.ccToken') ==
     * $array['customer']['paymentInfo']['ccToken']</p>
     *
     * @param $accessString
     *
     * @return array|mixed|null
     */
    public function get($accessString)
    {
        return $this->getKeys($this->parseDotNotation($accessString));
    }

    /**
     * @param      $propString
     * @param      $value
     *
     * @return $this
     */
    public function set($propString, $value)
    {
        $movingTarget = &$this->data;
        $keys = $this->parseDotNotation($propString);
        $length = count($keys);

        foreach ($keys as $i => $key) {
            $lastKey = $i === $length - 1;
            $isset = isset($movingTarget[$key]);

            if ($isset && !$lastKey && !is_array($movingTarget[$key])) {
                throw new InvalidArgumentException(sprintf(
                    "Attempted to set/access the property %s like an array, but is of type: %s",
                    $key,
                    gettype($movingTarget[$key])
                ));
            }

            if (!$isset || !is_array($movingTarget[$key])) $movingTarget[$key] = [];

            $movingTarget = &$movingTarget[$key];
        }

        $movingTarget = $value;

        return $this;
    }

    /**
     * @param $string
     *
     * @return array
     */
    protected function parseDotNotation($string)
    {
        return explode('.', strval($string));
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * @param int $options
     * @param int $depth
     *
     * @return string
     */
    public function toJson($options = 0, $depth = 512)
    {
        return json_encode($this, $options, $depth);
    }

    /**
     * @param array $data
     *
     * @return static
     */
    public static function newFromArray(array $data)
    {
        return new static($data);
    }

    /**
     * @param \stdClass $data
     *
     * @return static
     */
    public static function newFromStdObject(\stdClass $data)
    {
        return new static(json_decode(json_encode($data), TRUE));
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return array data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     *
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset): bool
    {
        $movingTarget = $this->data;

        foreach ($this->parseDotNotation($offset) as $i) {
            if (!isset($movingTarget[$i])) {
                return false;
            }

            $movingTarget = $movingTarget[$i];
        }

        return $movingTarget;
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     *
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     *
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     *
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset): void
    {
        $movingTarget = &$this->data;
        $keys = $this->parseDotNotation($offset);
        $length = count($keys);

        foreach ($keys as $i => $key) {
            if ($i === $length - 1) {
                unset($movingTarget[$key]);
            } else {
                $movingTarget = &$movingTarget[$key];
            }
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return json_encode($this);
    }
}
