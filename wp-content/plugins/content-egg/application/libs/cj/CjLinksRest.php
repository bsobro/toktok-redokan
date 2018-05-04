<?php

namespace ContentEgg\application\libs\cj;

use ContentEgg\application\libs\RestClient;

/**
 * CjLinksRest class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2017 keywordrush.com  
 * 
 * @link: http://cjsupport.custhelp.com/app/answers/detail/a_id/1552/kw/api
 *
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'RestClient.php';

class CjLinksRest extends RestClient {

    const API_URI_BASE = 'https://linksearch.api.cj.com/v2';

    private $dev_key;

    /**
     * @var array Response Format Types
     */
    protected $_responseTypes = array(
        'xml',
    );

    /**
     * Constructor
     * @param  string $responseType
     */
    public function __construct($dev_key, $responseType = 'xml')
    {
        $this->setResponseType($responseType);
        $this->setUri(self::API_URI_BASE);
        $this->dev_key = $dev_key;
    }

    public function search($query, array $params = array())
    {
        $params['keywords'] = $query;
        $this->setCustomHeaders(array('Authorization' => $this->dev_key));

        $response = $this->restGet('/link-search', $params);
        return $this->_decodeResponse($response);
    }

}
