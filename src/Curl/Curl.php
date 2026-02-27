<?php

declare(strict_types=1);

namespace Brick\Std\Curl;

use function curl_copy_handle;
use function curl_error;
use function curl_exec;
use function curl_getinfo;
use function curl_init;
use function curl_setopt;
use function curl_setopt_array;
use function curl_version;

use const CURLOPT_RETURNTRANSFER;

/**
 * An object wrapper for cURL.
 */
final class Curl
{
    /**
     * The cURL handle.
     *
     * @var resource
     */
    private $curl;

    /**
     * Class constructor.
     */
    public function __construct(?string $url = null)
    {
        $this->curl = ($url === null) ? curl_init() : curl_init($url);
    }

    /**
     * @throws CurlException
     */
    public function execute(): string
    {
        // This option must always be set.
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($this->curl);

        if ($result === false) {
            throw CurlException::error(curl_error($this->curl));
        }

        return $result;
    }

    /**
     * Returns information about the last transfer.
     *
     * If opt is given, its value is returned. If opt is not recognized, false is returned.
     * Otherwise, an associative array of values is returned.
     *
     * @param int|null $opt One of the CURLINFO_* constants, or null to return all.
     */
    public function getInfo(?int $opt = null): mixed
    {
        if ($opt === null) {
            return curl_getinfo($this->curl);
        }

        return curl_getinfo($this->curl, $opt);
    }

    public function setOption(int $option, mixed $value): void
    {
        curl_setopt($this->curl, $option, $value);
    }

    public function setOptions(array $options): void
    {
        curl_setopt_array($this->curl, $options);
    }

    public static function getVersion(): array
    {
        return curl_version();
    }

    /**
     * Clone handler.
     */
    public function __clone()
    {
        $this->curl = curl_copy_handle($this->curl);
    }
}
