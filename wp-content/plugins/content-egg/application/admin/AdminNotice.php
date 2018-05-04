<?php

namespace ContentEgg\application\admin;

/**
 * AdminNotice class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2016 keywordrush.com
 */
class AdminNotice {

    const GET_NOTICE_PARAM = 'egg-notice';
    const GET_LEVEL_PARAM = 'egg-notice-level';
    const GET_ID_PARAM = 'egg-notice-id';

    protected static $instance = null;

    public function getMassages()
    {
        return array(
            'autoblog_saved' => __('Task for autoblogging is saved.', 'content-egg') . ' <a href="?page=content-egg-autoblog&action=run&id=%%ID%%">' . __('Run now', 'content-egg') . '</a>',
            'autoblog_create_error' => __('While saving task error was occurred.', 'content-egg'),
            'autoblog_csv_file_error' => __('Error while handling file with keywords.', 'content-egg'),
            'autoblog_batch_created' => __('Tasks for autoblogging are saved.', 'content-egg') . ' %%ID%%.',
            'license_reset_error' => __('License can\'t be deactivated. Write to support of plugin.', 'content-egg'),
            'license_reset_success' => __('License was deactivated. You must deactivate and delete plugin from current domain to enable it on another one.', 'content-egg'),
                // 'license_reset_error_info' => __('Лицензия не может быть откреплена.', 'content-egg') . ' %%ID%%.',
        );
    }

    public static function getInstance()
    {
        if (self::$instance == null)
            self::$instance = new self;

        return self::$instance;
    }

    private function __construct()
    {
        //$this->adminInit();
    }

    public function adminInit()
    {
        \add_action('admin_notices', array($this, 'displayNotice'));
    }

    public function getMessage($message_id = null)
    {
        if (!$message_id && !empty($_GET[self::GET_NOTICE_PARAM]))
            $message_id = $_GET[self::GET_NOTICE_PARAM];
        else
            return '';

        $all = $this->getMassages();
        if (!array_key_exists($message_id, $all))
            return '';

        $message = $all[$message_id];

        if (!empty($_GET[self::GET_ID_PARAM]))
        {
            $id = (int) $_GET[self::GET_ID_PARAM];
            $message = str_replace('%%ID%%', $id, $message);
        }

        return $message;
    }

    public function displayNotice()
    {
        if (empty($_GET[self::GET_NOTICE_PARAM]))
            return;

        $level = 'info';
        if (!empty($_GET[self::GET_LEVEL_PARAM]))
        {
            $level = $_GET[self::GET_LEVEL_PARAM];
            if (!in_array($level, array('error', 'warning', 'info', 'success')))
                $level = 'info';
        }
        echo '<div class="notice notice-' . $level . ' is-dismissible"><p>' . $this->getMessage() . '</p></div>';
    }

    public static function add2Url($url, $message, $level = null, $id = null)
    {
        $url = add_query_arg(self::GET_NOTICE_PARAM, $message, $url);
        if ($level)
            $url = add_query_arg(self::GET_LEVEL_PARAM, $level, $url);
        if ($id)
            $url = add_query_arg(self::GET_ID_PARAM, $id, $url);
        return $url;
    }

}
