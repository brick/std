<?php

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
    public function __construct($url = null, array $params = null)
    {
        $this->curl = curl_init();

        if ($url !== null) {
            $this->setUrl($url, $params);
        }
    }

    /**
     * @param string     $url
     * @param array|null $params
     */
    public function setUrl($url, array $params = null)
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
    public function execute()
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
     * @param  integer $opt
     * @return mixed
     */
    public function getInfo($opt)
    {
        return curl_getinfo($this->curl, $opt);
    }

    /**
     * @return mixed
     */
    public function getInfos()
    {
        return curl_getinfo($this->curl);
    }

    /**
     * @param  integer $option
     * @param  mixed   $value
     * @return Curl
     */
    public function setOption($option, $value)
    {
        curl_setopt($this->curl, $option, $value);

        return $this;
    }

    /**
     * @param  array $options
     * @return Curl
     */
    public function setOptions(array $options)
    {
        curl_setopt_array($this->curl, $options);

        return $this;
    }

    /**
     * @return array
     */
    public function getVersion()
    {
        return curl_version();
    }
}
