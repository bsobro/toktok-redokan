<?php

namespace ContentEgg\application\components;

use ContentEgg\application\Plugin;
use ContentEgg\application\admin\PluginAdmin;

/**
 * ModuleConfig abstract class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2015 keywordrush.com
 */
abstract class ModuleConfig extends Config {

    protected $module_id;

    protected function __construct($module_id = null)
    {        
        if ($module_id)
        {
            $this->module_id = $module_id;
        } else
        {
            $parts = explode('\\', get_class($this));
            $this->module_id = $parts[count($parts) - 2];
        }
        parent::__construct();
    }

    public function getModuleId()
    {
        return $this->module_id;
    }

    public function getModuleName()
    {
        return $this->getModuleInstance()->getName();
    }

    public function getModuleInstance()
    {
        return ModuleManager::factory($this->getModuleId());
    }

    public function page_slug()
    {
        return Plugin::slug() . '--' . $this->getModuleId();
    }

    public function option_name()
    {
        return Plugin::slug() . '_' . $this->getModuleId();
    }

    public function add_admin_menu()
    {
        \add_submenu_page('options.php', $this->getModuleId() . ' ' . __('settings', 'content-egg') . ' &lsaquo; Content Egg', '', 'manage_options', $this->page_slug, array($this, 'settings_page'));
    }

    public function settings_page()
    {
        PluginAdmin::render('settings', array('page_slug' => $this->page_slug(),
            'header' => $this->getModuleName() . ' ' . __('settings', 'content-egg'),
            'api_agreement' => $this->getModuleInstance()->getApiAgreement(),
            'description' => $this->getModuleInstance()->getDescription(),
            'module' => $this->getModuleInstance(),
        ));
    }

    public function options()
    {
        return array();
    }

}
