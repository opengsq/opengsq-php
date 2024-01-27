<?php declare(strict_types=1);

namespace OpenGSQ\Responses\EOS;

/**
 * Class Matchmaking
 *
 * Represents the response from a matchmaking request.
 *
 * @package OpenGSQ\Responses\EOS
 */
class Matchmaking
{
    /**
     * @var array<string, mixed> The list of sessions returned by the matchmaking request.
     * Each session is represented as an associative array of string keys and mixed values.
     */
    public array $sessions = [];

    /**
     * @var int The count of sessions returned by the matchmaking request.
     */
    public int $count;
}
