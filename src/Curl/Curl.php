<?php

declare(strict_types=1);

namespace Brick\Std\Curl;

/**
 * An object wrapper for cURL.
 */
class Curl
{
    /**
     * The cURL handle.
     *
     * @var resource
     */
    private $curl;

    /**
     * Class constructor.
     *
     * @param string|null $url
     */
    public function __construct(string $url = null)
    {
        $this->curl = ($url === null) ? curl_init() : curl_init($url);
    }

    /**
     * Class destructor.
     */
    public function __destruct()
    {
        curl_close($this->curl);
    }

    /**
     * Clone handler.
     *
     * @return void
     */
    public function __clone()
    {
        $this->curl = curl_copy_handle($this->curl);
    }

    /**
     * @return string
     *
     * @throws CurlException
     */
    public function execute() : string
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
     * @param int $opt One of the CURLINFO_* constants.
     *
     * @return mixed
     */
    public function getInfo(int $opt = null)
    {
        if ($opt === null) {
            return curl_getinfo($this->curl);
        }

        return curl_getinfo($this->curl, $opt);
    }

    /**
     * @param int   $option
     * @param mixed $value
     *
     * @return void
     */
    public function setOption(int $option, $value) : void
    {
        curl_setopt($this->curl, $option, $value);
    }

    /**
     * @param array $options
     *
     * @return void
     */
    public function setOptions(array $options) : void
    {
        curl_setopt_array($this->curl, $options);
    }

    /**
     * @return array
     */
    public static function getVersion() : array
    {
        return curl_version();
    }
}
