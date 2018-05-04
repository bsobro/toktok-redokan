<?php

namespace ContentEgg\application\libs;

/**
 * ParserClient class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2016 keywordrush.com
 *
 */
class ParserClient {

    protected $charset = 'utf-8';
    protected $xpath;
    protected $url;
    protected static $_httpClient = null;

    public function __construct($url = null)
    {
        if ($url)
            $this->setUrl($url);
    }

    public function setUrl($url)
    {
        $this->url = $url;
        $this->xpath = null;
        $this->loadXPath($url);
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * Gets the HTTP client object.
     */
    public static function getHttpClient($opts = array())
    {
        $_opts = array(
            'sslverify' => false,
            'redirection' => 3,
            'timeout' => 10,
            'user-agent' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.9) Gecko/20071025 Firefox/2.0.0.9',
        );
        if ($opts)
            $_opts = $opts + $_opts;

        if (self::$_httpClient == null)
        {
            //Get WP http client
            self::$_httpClient = new WpHttpClient();
            self::$_httpClient->setHeaders('Accept-Charset', 'ISO-8859-1,utf-8');

            self::$_httpClient->setUserAgent($_opts['user-agent']);
            self::$_httpClient->setRedirection($_opts['redirection']);
            self::$_httpClient->setTimeout($_opts['timeout']);
            self::$_httpClient->setSslVerify($_opts['sslverify']);
        }

        return self::$_httpClient;
    }

    /**
     * Sets the HTTP client object to use for retrieving the feeds.  If none
     * is set, the default Http_Client will be used.
     */
    public static function setHttpClient($httpClient)
    {
        self::$_httpClient = $httpClient;
    }

    public function loadXPath($url, $query = null)
    {
        $this->xpath = $this->getXPath($url, $query);
    }

    public function getXPath($url, $query = null)
    {
        return $xpath = new \DomXPath($this->getDom($url, $query));
    }

    public function getDom($url, $query = null)
    {
        $dom = new \DomDocument();
        $dom->preserveWhiteSpace = false;
        libxml_use_internal_errors(true);
        if (!$dom->loadHTML($this->restGet($url, $query)))
            throw new \Exception('Can\'t load DOM Document.');
        return $dom;
    }

    public function restGet($uri, $query = null)
    {
        $client = self::getHttpClient();
        $client->resetParameters();
        $client->setUri($uri);
        if ($query)
            $client->setParameterGet($query);
        $body = $this->getResult($client->request('GET'));
        return $this->decodeCharset($body);
    }

    protected function getResult($response)
    {
        if (\is_wp_error($response))
        {
            $error_mess = "HTTP request fails: " . $response->get_error_code() . " - " . $response->get_error_message() . '.';
            throw new \Exception($error_mess);
        }

        $response_code = (int) \wp_remote_retrieve_response_code($response);

        if ($response_code != 200)
        {
            $response_message = \wp_remote_retrieve_response_message($response);
            $error_mess = "HTTP request status fails: " . $response_code . " - " . $response_message . '.';
            $error_mess .= ' Server replay: ' . \wp_remote_retrieve_body($response);
            throw new \Exception($error_mess);
        }

        return \wp_remote_retrieve_body($response);
    }

    public function decodeCharset($str)
    {
        $encoding_hint = '<?xml encoding="UTF-8">';

        if (strtolower($this->charset) != 'utf-8')
        {
            $str = $encoding_hint . '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . $str;
            return iconv($this->charset, 'utf-8', $str);
        } else
            return $encoding_hint . $str;
    }

    public function xpathScalar($path)
    {
        $res = $this->xpath->query($path);
        if ($res && $res->length > 0)
            return trim(strip_tags($res->item(0)->nodeValue));
        else
            return null;
    }

    public function xpathArray($path)
    {
        $res = $this->xpath->query($path);
        $return = array();
        if ($res && $res->length > 0)
        {
            foreach ($res as $r)
            {
                $return[] = trim(strip_tags($r->nodeValue));
            }
        }
        return $return;
    }

}
