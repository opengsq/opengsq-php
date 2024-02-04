<?php declare(strict_types=1);

namespace OpenGSQ\Exceptions;

/**
 * Class InvalidPacketException
 *
 * Represents errors that occur during application execution when a packet is invalid.
 */
class InvalidPacketException extends \Exception
{
    /**
     * Initializes a new instance of the InvalidPacketException class with a specified error message.
     *
     * @param string $message The message that describes the error.
     */
    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    /**
     * Checks if the received value is equal to the expected value.
     *
     * @param mixed $received The received value.
     * @param mixed $expected The expected value.
     * @throws InvalidPacketException Thrown when the received value does not match the expected value.
     */
    public static function throwIfNotEqual($received, $expected): void
    {
        if (is_array($received) && is_array($expected)) {
            if ($received !== $expected) {
                throw new InvalidPacketException(self::getCustomMessage($received, $expected));
            }
        } elseif ($received !== $expected) {
            throw new InvalidPacketException(self::getCustomMessage($received, $expected));
        }
    }

    /**
     * Generates a custom error message for packet header mismatches.
     *
     * @param mixed $received The received value.
     * @param mixed $expected The expected value.
     * @return string The custom error message.
     */
    private static function getCustomMessage($received, $expected): string
    {
        $receivedStr = is_array($received) ? implode('', $received) : (string) $received;
        $expectedStr = is_array($expected) ? implode('', $expected) : (string) $expected;

        return "Packet header mismatch. Received: {$receivedStr}. Expected: {$expectedStr}.";
    }
}