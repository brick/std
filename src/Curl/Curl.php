<?php

declare(strict_types=1);

namespace Brick\Std\Curl;

/**
 * cURL object wrapper
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
     * @param array|null  $params
     */
    public function __construct(string $url = null, array $params = null)
    {
        $this->curl = curl_init();

        if ($url !== null) {
            $this->setUrl($url, $params);
        }
    }

    /**
     * @param string     $url
     * @param array|null $params
     *
     * @return void
     */
    public function setUrl(string $url, array $params = null) : void
    {
        $url .= ($params === null) ? '' : '?' . http_build_query($params);
        $this->setOption(CURLOPT_URL, $url);
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
        // This must always be set.
        $this->setOption(CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($this->curl);

        if ($result === false) {
            throw CurlException::error(curl_error($this->curl));
        }

        return $result;
    }

    /**
     * @param int $opt
     *
     * @return mixed
     */
    public function getInfo(int $opt)
    {
        return curl_getinfo($this->curl, $opt);
    }

    /**
     * @return mixed
     */
    public function getInfos() : array
    {
        return curl_getinfo($this->curl);
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
    public function getVersion() : array
    {
        return curl_version();
    }
}
