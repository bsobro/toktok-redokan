<?php

namespace ContentEgg\application\libs\amazon;

use ContentEgg\application\libs\RestClient;

/**
 * PHP interface to Amazon Product Advertising API
 * Modified version of Services_Amazon pear class
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2015 keywordrush.com
 *
 * @link http://docs.amazonwebservices.com/AWSECommerceService/latest/DG/ Amazon Product Advertising API
 */

/**
 * Original PEAR License:
 *
 * LICENSE: Copyright 2004-2009 John Downey. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * o Redistributions of source code must retain the above copyright notice, this
 *   list of conditions and the following disclaimer.
 * o Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE FREEBSD PROJECT "AS IS" AND ANY EXPRESS OR
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO
 * EVENT SHALL THE FREEBSD PROJECT OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,
 * EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation are
 * those of the authors and should not be interpreted as representing official
 * policies, either expressed or implied, of The PEAR Group.
 *
 * @category  Web Services
 * @package   Services_Amazon
 * @author    John Downey <jdowney@gmail.com>
 * @author    Tatsuya Tsuruoka <tatsuya.tsuruoka@gmail.com>
 * @copyright 2004-2009 John Downey
 * @license   http://www.freebsd.org/copyright/freebsd-license.html 2 Clause BSD License
 * @version   CVS: $Id: Amazon.php,v 1.4 2007/12/16 17:27:44 ttsuruoka Exp $
 * @link      http://pear.php.net/package/Services_Amazon/
 * @filesource
 */
class AmazonProduct extends RestClient {

    const API_VERSION = '2011-08-01';

    private $_access_key_id;
    private $_secret_access_key;
    private $_associate_tag;
    private $_timestamp = null;
    private $_locale;

    /**
     * @var array Response Format Types
     */
    protected $_responseTypes = array(
        'xml',
    );

    //private static $_httpClient;

    public function __construct($access_key_id, $secret_access_key, $associate_tag)
    {
        $this->_access_key_id = $access_key_id;
        $this->_secret_access_key = $secret_access_key;
        $this->setAssociateTag($associate_tag);
        $this->setLocale('us');
        $this->setResponseType('xml');
    }

    public function setAssociateTag($associate_tag)
    {
        $this->_associate_tag = $associate_tag;
    }

    /**
     * Sets the locale passed when making a query to Amazon
     * Currently us, uk, de, jp, fr, ca, cn, it, es are supported
     * @param string $locale The new locale to use
     * @link: http://docs.aws.amazon.com/AWSECommerceService/latest/DG/Locales.html
     */
    public function setLocale($locale)
    {
        // не все urls соответствуют документации
        // @link: http://docs.aws.amazon.com/AWSECommerceService/latest/DG/AnatomyOfaRESTRequest.html
        $urls = array(
            'us' => 'http://ecs.amazonaws.com/onca/xml',
            'uk' => 'http://ecs.amazonaws.co.uk/onca/xml',
            'de' => 'http://ecs.amazonaws.de/onca/xml',
            'jp' => 'http://webservices.amazon.co.jp/onca/xml',
            'cn' => 'http://webservices.amazon.cn/onca/xml',
            'fr' => 'http://ecs.amazonaws.fr/onca/xml',
            'it' => 'http://webservices.amazon.it/onca/xml',
            'es' => 'http://webservices.amazon.es/onca/xml',
            'ca' => 'http://ecs.amazonaws.ca/onca/xml',
            'br' => 'http://webservices.amazon.com.br/onca/xml',
            'in' => 'http://webservices.amazon.in/onca/xml',
            'mx' => 'http://webservices.amazon.com.mx/onca/xml',
        );
        if (!isset($urls[$locale]))
        {
            throw new \Exception('Invalid amazon locale');
        }
        $this->_locale = $locale;
        $this->setUri($urls[$locale]);
        return true;
    }

    /**
     * Sets a timestamp (for debugging)
     *
     * @param integer $time A timestamp
     */
    public function setTimestamp($time)
    {
        $this->_timestamp = $time;
    }

    /**
     * Searches for products
     *
     * Example:
     * <code>
     * <?php
     * $amazon = new Services_Amazon('[your Access Key ID here]', '[your Secret Access key here]');
     * $options = array();
     * $options['Keywords'] = 'sushi';
     * $options['Sort'] = 'salesrank';
     * $options['ResponseGroup'] = 'ItemIds,ItemAttributes,Images';
     * $result = $amazon->ItemSearch('Books', $options);
     * ?>
     * </code>
     *
     * @access public
     * @param  string $search_index A search index
     * @param  array $options The optional parameters
     * @return array The array of information returned by the query
     */
    public function ItemSearch($search_index, $options = array())
    {
        $params = $options;
        $params['Operation'] = 'ItemSearch';
        $params['SearchIndex'] = $search_index;

        $response = $this->signedGet('', $params);
        $decoded = $this->_decodeResponse($response);
        return $this->_parseResult($decoded);
    }

    /**
     * Retrieves information for products
     *
     * Example:
     * <code>
     * <?php
     * $amazon = new Services_Amazon('[your Access Key ID here]', '[your Secret Access key here]');
     * $options = array();
     * $options['ResponseGroup'] = 'Large';
     * $result = $amazon->ItemLookup('[ASIN(s)]', $options);
     * ?>
     * </code>
     *
     * @access public
     * @param string $item_id Product IDs
     * @param array $options The optional parameters
     * @return array The array of information returned by the query
     * @see ItemSearch()
     */
    function ItemLookup($item_id, $options = array())
    {
        $params = $options;
        $params['Operation'] = 'ItemLookup';
        if (is_array($item_id))
        {
            $item_id = implode(',', $item_id);
        }
        // One or more (up to ten) positive integers that uniquely identify an item. 
        // The meaning of the number is specified by IdType.
        $params['ItemId'] = $item_id;

        $response = $this->signedGet('', $params);
        $decoded = $this->_decodeResponse($response);
        return $this->_parseResult($decoded);
    }
    
    /**
     * Encode URL according to RFC 3986
     * @param string $str UTF-8 string
     * @return string Encoded string
     */
    private function _urlencode($str)
    {
        return str_replace('%7E', '~', rawurlencode($str));
    }

    /**
     * Create an HMAC-SHA256
     * @param string $string_to_sign
     * @param string $secret_access_key
     * @return string hash
     */
    private function _hash($string_to_sign, $secret_access_key)
    {
        if (function_exists('hash_hmac'))
        {
            return hash_hmac('sha256', $string_to_sign, $secret_access_key, true);
        } elseif (function_exists('mhash'))
        {
            return mhash(MHASH_SHA256, $string_to_sign, $secret_access_key);
        } else
        {
            throw new \Exception('hash_hmac or mhash function is required.');
        }
    }

    /**
     * Builds a URL
     *
     * @access private
     * @param array $params
     * @return string URL
     */
    function _buildUrl($params)
    {
        $params['Service'] = 'AWSECommerceService';
        $params['AWSAccessKeyId'] = $this->_access_key_id;
        if (!empty($this->_associate_tag))
        {
            $params['AssociateTag'] = $this->_associate_tag;
        }
        $params['Timestamp'] = gmdate('Y-m-d\TH:i:s\Z', is_null($this->_timestamp) ? time() : $this->_timestamp);
        $params['Version'] = self::API_VERSION;

        // sort parameters
        ksort($params);

        // create a canonical string
        $canonical_string = '';
        foreach ($params as $k => $v)
        {
            $canonical_string .= '&' . $this->_urlencode($k) . '=' . $this->_urlencode($v);
        }
        $canonical_string = substr($canonical_string, 1);

        // create a signature for request
        $parsed_url = parse_url($this->getUri());
        $string_to_sign = "GET\n{$parsed_url['host']}\n{$parsed_url['path']}\n{$canonical_string}";
        $signature = $this->_hash($string_to_sign, $this->_secret_access_key);
        $signature = base64_encode($signature);

        // create a signed url
        $url = $this->getUri() . '?' . $canonical_string . '&Signature=' . $this->_urlencode($signature);

        return $url;
    }

    public function signedGet($path, array $query = null)
    {
        $url = $this->_buildUrl($query);
        return $this->restGet($url, $query);
    }

    /**
     * Parses raw XML result
     *
     * @param string $raw_result
     * @return array amazon items.
     */
    private function _parseResult($data)
    {
        // Обработка ошибки
        if (isset($data['Items']['Request']['Errors']['Error']))
        {
            $err_message = 'Unknown error';
            $errors = $data['Items']['Request']['Errors']['Error'];
            if (isset($errors['Message']))
                $err_message = $errors['Message'];

            throw new \Exception($err_message);
        }

        if (!isset($data['Items']['Item']))
            return array();
        if (!isset($data['Items']['Item'][0]) && isset($data['Items']['Item']['ASIN']))
            $data['Items']['Item'] = array($data['Items']['Item']);

        return $data;
    }

    /**
     * Amazon customer reviews parser
     * @param string $url iframe reviews url
     */
    public function parseCustomerReviews($url, $locale = 'us')
    {
        try
        {
            $html = $this->restGet($url);
        } catch (\Exception $e)
        {
            return array();
        }
        if (!$html)
            return array();

        // Исправим кодировку
        // US, UK - ISO-8859-1
        // DE, FR, CA - ISO-8859-15
        // JP - Shift_JIS
        if ($locale == "jp" && function_exists('iconv'))
            $html = iconv("Shift_JIS", "utf-8", $html);
        else
            $html = utf8_encode($html);

        if (strstr($html, 'There are no customer reviews for this item'))
            return array();
        $result = array();

        // Сначала парсим общую информацию по обзорам
        $totalreviews = $avarage_rating = 0;
        preg_match("/=\"crIFrameHeaderLeftColumn.+?=\"crIFrameHeaderHistogram/msi", $html, $header_html);
        if ($header_html)
        {
            $header_html = $header_html[0];

            //prnx($header_html);
            // Total Reviews
            preg_match('/>([\d,]+)\s.+?\)<\/span>/', $header_html, $matches);
            $totalreviews = $matches ? $matches[1] : 0;

            // Avarage Rating
            preg_match('/<img.+?alt="(\d\.\d).+?"/', $header_html, $matches);
            $avarage_rating = $matches ? $matches[1] : 0;
            unset($header_html);
        }
        $result['AverageRating'] = $avarage_rating;
        $result['TotalReviews'] = $totalreviews;

        // Парсим блоки с Review
        preg_match_all("/<\!\-\-\sBOUNDARY\s\-\->(.+?)<div\sstyle=\"padding-top:\s10px;\sclear:\sboth;\swidth:\s100%;\">/msi", $html, $matches);
        unset($html);
        if (!$matches)
            return array();

        $reviews = array();
        $i = 0;
        foreach ($matches[1] as $review_blok)
        {
            $reviews[$i] = array();

            // Reviews Content
            preg_match("/class=\"reviewText\">(.+?)<\/div>/msi", $review_blok, $matches);
            if (!$matches)
                preg_match("/^.+<\/div>?(.+?)\z/msi", $review_blok, $matches);
            if (!$matches)
                continue;
            $content = '';
            $content = $matches[1];
            $content = preg_replace("/<a.+?<\/a>/", "", $content);
            $content = trim(strip_tags($content));
            $content = preg_replace("/\r/", " ", $content);
            $content = preg_replace("/\n/", " ", $content);
            if (!$content)
                continue;
            $reviews[$i]['Content'] = $content;

            // Reviews Summary & Date
            preg_match("/<b>(.+)<\/b>,\s<nobr>(.+)<\/nobr>/", $review_blok, $matches);
            if ($matches)
            {
                $reviews[$i]['Summary'] = trim(strip_tags($matches[1])); //Summary
                $reviews[$i]['Date'] = strtotime(trim(strip_tags($matches[2]))); //Date
                if (!$reviews[$i]['Date'])
                    $reviews[$i]['Date'] = time();
            } else
            {
                $reviews[$i]['Summary'] = '';
                $reviews[$i]['Date'] = time();
            }

            // Customer Name
            preg_match("/<span.+?>(.+?)<\/span><\/a>/", $review_blok, $matches);
            if ($matches)
                $reviews[$i]['Name'] = trim(strip_tags($matches[1]));
            else
                $reviews[$i]['Name'] = '';

            // Rating
            preg_match('/<img.+?alt="(\d\.\d).+?"/', $review_blok, $matches);
            if ($matches)
                $reviews[$i]['Rating'] = trim(strip_tags($matches[1]));
            else
                $reviews[$i]['Rating'] = 4.0;

            $i++;
        }
        unset($matches);
        $result['Reviews'] = $reviews;
        //prnx($result);
        return $result;
    }

}
