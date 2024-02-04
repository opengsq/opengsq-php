<?php declare(strict_types=1);

namespace OpenGSQ\Responses\Unreal2;

use ArrayAccess;
use JsonSerializable;

/**
 * Class Rules
 *
 * This class implements the ArrayAccess and JsonSerializable interfaces and provides methods for managing game rules data.
 */
class Rules implements ArrayAccess, JsonSerializable
{
    /**
     * @var array The main data storage.
     */
    private $data;

    /**
     * @var array Stores the mutators data.
     */
    public $mutators;

    /**
     * Rules constructor.
     *
     * Initializes the data and mutators arrays.
     */
    public function __construct()
    {
        $this->data = array();
        $this->mutators = array();
    }

    /**
     * Sets a value at the specified offset in the data array.
     *
     * @param mixed $offset The offset where the value should be set.
     * @param mixed $value The value to set.
     */
    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    /**
     * Checks if an offset exists in the data array.
     *
     * @param mixed $offset The offset to check.
     * @return bool True if the offset exists, false otherwise.
     */
    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }

    /**
     * Unsets an offset in the data array.
     *
     * @param mixed $offset The offset to unset.
     */
    public function offsetUnset($offset): void
    {
        unset($this->data[$offset]);
    }

    /**
     * Gets the value at the specified offset in the data array.
     *
     * @param mixed $offset The offset to get the value from.
     * @return string The value at the specified offset or null if the offset does not exist.
     */
    public function offsetGet($offset): string
    {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }

    /**
     * Specifies the data to be serialized to JSON.
     *
     * This method is called automatically by json_encode().
     *
     * @return array The data to be serialized.
     */
    public function jsonSerialize(): array
    {
        return array_merge($this->data, ['mutators' => $this->mutators]);
    }
}