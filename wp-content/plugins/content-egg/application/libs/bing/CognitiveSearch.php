<?php

namespace ContentEgg\application\libs\bing;

use ContentEgg\application\libs\RestClient;

/**
 * CognitiveSearch class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2016 keywordrush.com
 * 
 * @link: https://msdn.microsoft.com/en-us/library/dn760794.aspx#parameters
 * @link: https://msdn.microsoft.com/en-us/library/mt604056.aspx
 *
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'RestClient.php';

class CognitiveSearch extends RestClient {
                          
    const API_URI_BASE = 'https://api.cognitive.microsoft.com/bing/v7.0';

    private $subscription_key = null;
    protected $_responseTypes = array(
        'json'
    );

    /**
     * Constructor
     */
    public function __construct($subscription_key)
    {
        $this->setSubscriptionKey($subscription_key);
        $this->setUri(self::API_URI_BASE);
        $this->setResponseType('json');
    }

    public function setSubscriptionKey($subscription_key)
    {
        $this->subscription_key = $subscription_key;
    }

    public function getSubscriptionKey()
    {
        return $this->subscription_key;
    }

    /**
     * Image Search API
     * @link: https://docs.microsoft.com/en-us/rest/api/cognitiveservices/bing-images-api-v7-reference
     */
    public function images($query, $params = array())
    {
        $params['q'] = $query;
        $response = $this->restGet('/images/search', $params);
        return $this->_decodeResponse($response);
    }

    /**
     * Autosuggest API
     * @link: https://msdn.microsoft.com/en-us/library/mt711406.aspx
     */
    public function autosuggest($query, $params = array())
    {
        $params['q'] = $query;
        $response = $this->restGet('/Suggestions', $params);
        return $this->_decodeResponse($response);
    }

    public function restGet($path, array $query = null)
    {
        $this->setCustomHeaders(array('Ocp-Apim-Subscription-Key' => $this->getSubscriptionKey()));
        return parent::restGet($path, $query);
    }

}
