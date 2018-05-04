<?php

namespace ContentEgg\application\components;

use ContentEgg\application\Plugin;
use ContentEgg\application\components\ModuleManager;
use ContentEgg\application\helpers\TextHelper;
use ContentEgg\application\helpers\InputHelper;

/**
 * ModuleApi class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2015 keywordrush.com
 */
class ModuleApi {

    const API_BASE = '-module-api';

    public function __construct()
    {
        \add_action('wp_ajax_content-egg-module-api', array($this, 'addApiEntry'));
    }

    public static function apiBase()
    {
        return Plugin::slug . self::API_BASE;
    }

    public function addApiEntry()
    {
        if (!\current_user_can('edit_posts'))
            throw new \Exception("Access denied.");

        if (empty($_GET['module']))
            throw new \Exception("Module is undefined.");

        $module_id = TextHelper::clear($_GET['module']);

        \check_ajax_referer('contentegg-metabox', '_contentegg_nonce');

        $parser = ModuleManager::getInstance()->parserFactory($module_id);

        if (!$parser->isActive())
            throw new \Exception("Parser module " . $parser->getId() . " is inactive.");

        $query = stripslashes(InputHelper::get('query', ''));
        $query = json_decode($query, true);

        if (!$query)
            throw new \Exception("Error: 'query' parameter cannot be empty.");

        if (empty($query['keyword']))
            throw new \Exception("Error: 'keyword' parameter cannot be empty.");

        if ($query['keyword'][0] == '[' || filter_var($query['keyword'], FILTER_VALIDATE_URL))
            $keyword = filter_var($query['keyword'], FILTER_SANITIZE_URL);
        else
            $keyword = TextHelper::clear_utf8($query['keyword']);
        if (!$keyword)
            throw new \Exception("Error: 'keyword' parameter cannot be empty.");

        try
        {
            $data = $parser->doRequest($keyword, $query);
            foreach ($data as $key => $item)
            {
                if (!$item->unique_id)
                    throw new \Exception('Item data "unique_id" must be specified.');

                if ($item->description)
                {
                    $item->description = TextHelper::br2nl($item->description);
                    $item->description = TextHelper::removeExtraBreaks($item->description);
                }
            }
            $this->formatJson(array('results' => $data, 'error' => ''));
        } catch (\Exception $e)
        {
            $this->formatJson(array('error' => $e->getMessage()));
        }
    }

    public function formatJson($data)
    {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data);
        \wp_die();
    }

}
