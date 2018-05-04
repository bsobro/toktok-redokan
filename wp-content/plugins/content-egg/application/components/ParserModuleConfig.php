<?php

namespace ContentEgg\application\components;

/**
 * ParserModuleConfig abstract class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2015 keywordrush.com
 */
abstract class ParserModuleConfig extends ModuleConfig {

    public function options()
    {
        $tpl_manager = ModuleTemplateManager::getInstance($this->module_id);
        $options = array(
            'is_active' => array(
                'title' => __('Enable module', 'content-egg'),
                'description' => '',
                'callback' => array($this, 'render_checkbox'),
                'default' => 0,
                'section' => 'default',
            ),
            'embed_at' => array(
                'title' => __('Add', 'content-egg'),
                'description' => __('The place for content of module. Shortcodes will work in any place regardless of the setting.', 'content-egg'),
                'callback' => array($this, 'render_dropdown'),
                'dropdown_options' => array(
                    'post_bottom' => __('At the end of the post', 'content-egg'),
                    'post_top' => __('At the beginning of the post', 'content-egg'),
                    'shortcode' => __('Shortcodes only', 'content-egg'),
                ),
                'default' => 'post_bottom',
                'section' => 'default',
            ),
            'priority' => array(
                'title' => __('Priority', 'content-egg'),
                'description' => __('Priority sets order of modules in post. 0 - is the most highest priority.', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => 10,
                'validator' => array(
                    'trim',
                    'absint',
                ),
                'section' => 'default',
            ),
            'template' => array(
                'title' => __('Template', 'content-egg'),
                'description' => __('Default template', 'content-egg'),
                'callback' => array($this, 'render_dropdown'),
                'dropdown_options' => $tpl_manager->getTemplatesList(),
                'default' => $this->getModuleInstance()->defaultTemplateName(),
                'section' => 'default',
            ),
            'tpl_title' => array(
                'title' => __('Title', 'content-egg'),
                'description' => __('Templates may use title on data output.', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => '',
                'validator' => array(
                    'trim',
                ),
                'section' => 'default',
            ),
            'featured_image' => array(
                'title' => 'Featured image',
                'description' => __('Automatically set Featured image for post', 'content-egg'),
                'callback' => array($this, 'render_dropdown'),
                'dropdown_options' => array(
                    '' => __('Don\'t set', 'content-egg'),
                    'first' => __('First image', 'content-egg'),
                    'second' => __('Second image', 'content-egg'),
                    'rand' => __('Random image', 'content-egg'),
                    'last' => __('Last image', 'content-egg'),
                ),
                'default' => '',
                'section' => 'default',
            ),
            'set_local_redirect' => array(
                'title' => __('Redirect', 'content-egg'),
                'description' => __('Make links with local 301 redirect', 'content-egg'),
                'callback' => array($this, 'render_checkbox'),
                'default' => 0,
                'section' => 'default',
            ),
        );

        return
                array_merge(
                parent::options(), $options
        );
    }

}
