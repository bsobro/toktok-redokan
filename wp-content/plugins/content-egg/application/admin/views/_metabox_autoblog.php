<?php

use ContentEgg\application\components\ModuleManager;
use ContentEgg\application\helpers\AdminHelper;

?>
<table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
    <tbody>

        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="name"><?php _e('Name', 'content-egg'); ?></label>
            </th>
            <td>
                <input id="name" name="item[name]" type="text" value="<?php echo esc_attr($item['name']) ?>"
                       size="50" class="code" placeholder="<?php _e('Name for autoblogging (optional)', 'content-egg'); ?>">
            </td>
        </tr>

        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="status"><?php _e('Task status', 'content-egg'); ?></label>
            </th>
            <td>
                <select id="status" name="item[status]">
                    <option value="1"<?php if ($item['status']) echo ' selected="selected"'; ?>><?php _e('Works', 'content-egg'); ?></option>
                    <option value="0"<?php if (!$item['status']) echo ' selected="selected"'; ?>><?php _e('Stoped', 'content-egg'); ?></option>
                </select>
                <p class="description"><?php _e('You can stop autoblogging.', 'content-egg'); ?></p>                                
            </td>
        </tr>        

        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="run_frequency"><?php _e('Work frequency', 'content-egg'); ?></label>
            </th>
            <td>
                <select id="run_frequency" name="item[run_frequency]">
                    <option value="3600"<?php if ($item['run_frequency'] == 3600) echo ' selected="selected"'; ?>><?php _e('Every hour', 'content-egg'); ?></option>
                    <option value="17280"<?php if ($item['run_frequency'] == 17280) echo ' selected="selected"'; ?>><?php _e('5 times in a day', 'content-egg'); ?></option>
                    <option value="43200"<?php if ($item['run_frequency'] == 43200) echo ' selected="selected"'; ?>><?php _e('Twice daily', 'content-egg'); ?></option>
                    <option value="86400"<?php if ($item['run_frequency'] == 86400) echo ' selected="selected"'; ?>><?php _e('Once a day', 'content-egg'); ?></option>
                    <option value="259200"<?php if ($item['run_frequency'] == 259200) echo ' selected="selected"'; ?>><?php _e('Each three days', 'content-egg'); ?></option>
                    <option value="604800"<?php if ($item['run_frequency'] == 604800) echo ' selected="selected"'; ?>><?php _e('Once a week', 'content-egg'); ?></option>
                    <option value="1209600"<?php if ($item['run_frequency'] == 1209600) echo ' selected="selected"'; ?>><?php _e('Once in 2 weeks', 'content-egg'); ?></option>
                </select>  
                <p class="description"><?php _e('How often autoblogging will run this task', 'content-egg'); ?></p>                
            </td>
        </tr> 
        <?php if (!$batch): ?>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="keywords"><?php _e('Keywords', 'content-egg'); ?></label>
                </th>
                <td>

                    <table width='100%'>
                        <tr>
                            <td valign="top" style="vertical-align: top;" width="50%">
                                <div style="margin-bottom: 10px;">
                                    <button id="tool_capitalise" title="<?php _e('Uppercase First Letter for Each Word', 'content-egg'); ?>"><?php _e('Uppercase First Letter for Each Word', 'content-egg'); ?></button>
                                    <button href="#" id="tool_upper_first" title="<?php _e('Uppercase first letter', 'content-egg'); ?>"><?php _e('Uppercase first letter', 'content-egg'); ?></button>
                                    <button href="#" id="tool_sort" title="<?php _e('Sort with alpha order', 'content-egg'); ?>"><?php _e('Sort with alpha order', 'content-egg'); ?></button>
                                    <button href="#" id="tool_add_minus" title="<?php _e('All words are inactive', 'content-egg'); ?>"><?php _e('All words are inactive', 'content-egg'); ?></button>
                                    <button href="#" id="tool_del_minus" title="<?php _e('All words are active', 'content-egg'); ?>"><?php _e('All words are active', 'content-egg'); ?></button>
                                    <button href="#" id="tool_delete" title="<?php _e('Clear list', 'content-egg'); ?>"><?php _e('Clear list', 'content-egg'); ?></button>
                                </div>    
                                <textarea rows="28" id="keywords" name="item[keywords]" class="small-text"><?php echo esc_html($item['keywords']) ?></textarea>
                                <div>
                                    <?php _e('Total', 'content-egg'); ?>: <b><span id="k_count">0</span></b>
                                </div>
                            </td>
                            <td valign="top" style="vertical-align: top;">
                                <div id="cegg-parsers-tabs">
                                    <ul>
                                        <li><a href="#fragment-1"><?php _e('Hints', 'content-egg'); ?></a></li>
                                        <li><a href="#fragment-2"><?php _e('Trends', 'content-egg'); ?></a></a></li>
                                        <li><a href="#fragment-3"><?php _e('Products', 'content-egg'); ?></a></a></li>
                                    </ul>
                                    <div id="fragment-1">
                                        <div id="sug_btn_group" class="btn-group" style="margin-bottom: 10px;">
                                            <input id="sug_google" name="sug_radio" value="sug_google" type="radio" checked="checked"><label for="sug_google">Google</label>
                                            <input id="sug_amazon" name="sug_radio" value="sug_amazon" type="radio"><label for="sug_amazon">Amazon</label>
                                            <?php if (\ContentEgg\application\admin\GeneralConfig::getInstance()->option('lang') == 'ru'): ?>
                                                <input id="sug_yandex" name="sug_radio" value="sug_yandex" type="radio"><label for="sug_yandex"><?php _e('Yandex', 'content-egg'); ?></label>
                                                <input id="sug_market" name="sug_radio" value="sug_market" type="radio"><label for="sug_market"><?php _e('Yandex.Market', 'content-egg'); ?></label>
                                            <?php endif; ?>
                                        </div>    
                                        <input type="text" id="sug_query" placeholder="<?php _e('Start enter keyword', 'content-egg'); ?>" />
                                        <select multiple="multiple" id="sug_keywords" style="width: 98%" size="23"></select>
                                    </div>
                                    <div id="fragment-2">
                                        <div style="margin-bottom: 10px;">
                                            <button id="trend_google" type="button">Hot Trends...</button>
                                        </div>
                                        <select multiple="multiple" id="trend_keywords" style="width: 98%" size="24"></select>
                                    </div>
                                    <div id="fragment-3">
                                        <div style="margin-bottom: 10px;">

                                            <select id='amazon_categ'>
                                                <?php foreach ($item['amazon_categs'] as $ac_value => $ac_name): ?>
                                                    <option value='<?php echo $ac_value; ?>'><?php echo $ac_name; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <select id='amazon_section'>
                                                <option value='bestsellers'>Bestsellers</option>
                                                <option value='new-releases'>New Releases</option>
                                                <option value='movers-and-shakers'>Movers and Shakers</option>
                                                <option value='top-rated'>Top Rated</option>
                                                <option value='most-wished-for'>Most Wished For</option>
                                                <option value='most-gifted'>Most Gifted</option>
                                            </select>                                        
                                            <button id="trend_goods" type="button"><?php _e('Load...', 'content-egg'); ?></button>

                                        </div>
                                        <select multiple="multiple" id="goods_keywords" style="width: 98%" size="24"></select>


                                    </div>
                                </div>
                            </td>                      
                        </tr>
                    </table>
                    <p class="">
                        <?php _e('Each keyword from separate line.', 'content-egg'); ?>
                        <?php _e('One keyword is one post.', 'content-egg'); ?>
                        <?php _e('Handled keywords are marked by [brackets].', 'content-egg'); ?>
                        <?php _e('When all keywords will be processed, task will stop.', 'content-egg'); ?>
                    </p>
                    <p class="">
                        <?php _e('You can also use separate keywords for some modules:', 'content-egg'); ?><br>
                        <code>Main Keyword;ModuleId1:Keyword 1;ModuleId2:Keyword 2;</code>
                    </p>                    

                </td>
            </tr>        
        <?php endif; ?>
        <?php if ($batch): ?>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="keywords_file"><?php _e('Keywords', 'content-egg'); ?></label>
                </th>

                <td>
                    <input id="keywords_file" type="file" name="item[keywords_file]" value="" />               

                    <p class="description">

                        <?php _e('Two format are supported: ', 'content-egg'); ?>
                        <br>
                        <br>
                        <b>1. <?php _e('CSV files in format:', 'content-egg'); ?></b>                    
                        <br>
                        <code>category 1;keyword 1<br>
                            category 1;keyword 2<br>
                            category 2;keyword 3<br>
                            category 2;keyword 4<br>
                            ...
                        </code>
                        <br>
                        <?php _e('Divider -  ";"', 'content-egg'); ?><br>
                        <?php _e('For each category will be created separate task for autoblog.', 'content-egg'); ?>
                        <br><br>
                        <b>2. <?php _e('TXT files:', 'content-egg'); ?></b><br>
                        <?php _e('Simple text file with list of keywords (each word from separate line).', 'content-egg'); ?>
                        <br> 
                        <?php _e('File must be in UTF-8', 'content-egg'); ?>
                        <br><br> 
                        <?php _e('You can also use separate keywords for some modules:', 'content-egg'); ?><br>
                        <code>Main Keyword;ModuleId1:Keyword 1;ModuleId2:Keyword 2;</code>

                    </p>                
                </td>
            </tr>        

        <?php endif; ?>

        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="keywords_per_run"><?php _e('Keywords for handle', 'content-egg'); ?></label>
            </th>
            <td>
                <input id="keywords_per_run" name="item[keywords_per_run]" value="<?php echo esc_attr($item['keywords_per_run']) ?>"
                       type="number" class="small-text">
                <p class="description"><?php _e('How many keywords to process at once. We don\'t recommend to use more than 5 keywords.', 'content-egg'); ?></p>
            </td>
        </tr>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="template_title"><?php _e('Title template', 'content-egg'); ?></label>
            </th>
            <td>

                <input id="template_title" name="item[template_title]" value="<?php echo esc_attr($item['template_title']) ?>"
                       type="text" class="regular-text ltr">
                <p class="description">
                    <?php _e('Template for title of post', 'content-egg'); ?>
                    <?php _e('Use tags:', 'content-egg'); ?> %KEYWORD%, %KEYWORD.ModuleID%, %PRODUCT.title%, %PRODUCT.price%, %PRODUCT.merchant%, %PRODUCT.domain%, %PRODUCT.manufacturer%,...<br>
                    <?php _e('For display data of plugin use special tags, for example:', 'content-egg'); ?> %Amazon.title%.<br>
                    <?php _e('You also can set index number for access to data of plugin', 'content-egg'); ?> %Amazon.0.price%.<br>
                    <?php _e('You can use "formulas" with synonyms, of which one will be selected with a random option, for example, {Discount|Sale|Cheap}.', 'content-egg'); ?>
                </p>                
            </td>
        </tr>          

        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="template_body"><?php _e('Template for post.', 'content-egg'); ?></label>
            </th>
            <td>

                <textarea rows="4" id="template_body" name="item[template_body]"><?php echo esc_html($item['template_body']) ?></textarea>
                <p class="description">
                    <?php _e('Template for body of post.', 'content-egg'); ?><br>
                    <?php _e('You can use shortcodes, for example:', 'content-egg'); ?>                    
                    [content-egg module=Amazon template=grid]<br>
                    <?php _e('"Formulas", and also all tags from title template, will also work here.', 'content-egg'); ?><br>

                </p>                
            </td>
        </tr>  
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="include_modules"><?php _e('Only choosed modules', 'content-egg'); ?></label>
            </th>
            <td>                
                <div class="cegg-checkboxgroup">
                    <?php foreach (ModuleManager::getInstance()->getParserModules(false) as $module): ?>
                        <div class="cegg-checkbox">
                            <label><input <?php if (in_array($module->getId(), $item['include_modules'])) echo 'checked'; ?> value="<?php echo esc_attr($module->getId()); ?>" type="checkbox" name="item[include_modules][]" /><?php echo $module->getName(); ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <p class="description">
                    <?php _e('Run only definite modules for this task.', 'content-egg'); ?>
                    <?php _e('If you don\'t choose anything, all active modules will be used.', 'content-egg'); ?>
                </p>                
            </td>
        </tr>

        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="exclude_modules"><?php _e('Exclude modules', 'content-egg'); ?></label>
            </th>
            <td>                
                <div class="cegg-checkboxgroup">
                    <?php foreach (ModuleManager::getInstance()->getParserModules(false) as $module): ?>
                        <div class="cegg-checkbox">
                            <label><input <?php if (in_array($module->getId(), $item['exclude_modules'])) echo 'checked'; ?> value="<?php echo esc_attr($module->getId()); ?>" type="checkbox" name="item[exclude_modules][]" /><?php echo $module->getName(); ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <p class="description">
                    <?php _e('Chosen modules will not run in this configuration. ', 'content-egg'); ?>
                </p>                
            </td>
        </tr>        

        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="post_status"><?php _e('Post status', 'content-egg'); ?></label>
            </th>
            <td>
                <select id="post_status" name="item[post_status]">
                    <option value="1"<?php if ($item['post_status'] == 1) echo ' selected="selected"'; ?>>Publish</option>                    
                    <option value="0"<?php if ($item['post_status'] == 0) echo ' selected="selected"'; ?>>Pending</option>
                </select>                
            </td>
        </tr>

        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="post_type"><?php _e('Post type', 'content-egg'); ?></label>
            </th>
            <td>
                <?php
                $post_types = \get_post_types(array('public' => true), 'names');
                if (isset($post_types['attachment']))
                    unset($post_types['attachment']);
                ?>
                <select id="post_type" name="item[post_type]">
                    <?php foreach ($post_types as $post_type): ?>
                        <option value="<?php echo \esc_attr($post_type); ?>"<?php if ($item['post_type'] == $post_type) echo ' selected="selected"'; ?>><?php echo \esc_html($post_type); ?></option>
                    <?php endforeach; ?>
                </select>                
            </td>
        </tr>         

        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="user_id"><?php _e('User', 'content-egg'); ?></label>
            </th>
            <td>
                <?php
                \wp_dropdown_users(array('name' => 'item[user_id]',
                    'who' => 'authors', 'id' => 'user_id', 'selected' => $item['user_id']));
                ?>
                <p class="description"><?php _e('This user will be author of posts.', 'content-egg'); ?></p>                
            </td>
        </tr> 

        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="category"><?php _e('Category ', 'content-egg'); ?></label>
            </th>
            <td>
                <?php $categories = AdminHelper::getCategoryList(); ?>

                <select name="item[category]" id="category">
                    <?php if ($batch): ?>
                        <option value="-1" <?php \selected($item['category'], -1); ?>>[ <?php _e('Create Automatically', 'content-egg');?> ]</option>
                    <?php endif; ?>
                    <?php foreach ($categories as $c_id => $c_name): ?>
                        <option value="<?php echo \esc_attr($c_id); ?>" <?php \selected($item['category'], $c_id); ?>>
                            <?php echo \esc_attr($c_name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>


                <p class="description">
                    <?php _e('Default category for posts.', 'content-egg'); ?>
                    <?php if ($batch): ?>
                        <?php _e('"Create Automatically" means, that categories will be created based on data of CSV file with keywords and categories.', 'content-egg'); ?>                    
                    <?php endif; ?>
                </p>
            </td>
        </tr>      

        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="dynamic_categories"><?php _e('Dynamic categories', 'content-egg'); ?></label>
            </th>
            <td>
                <select id="post_status" name="item[config][dynamic_categories]">
                    <option value="0"<?php if ($item['config']['dynamic_categories'] == 0) echo ' selected="selected"'; ?>><?php _e('Do not create', 'content-egg'); ?></option>
                    <option value="1"<?php if ($item['config']['dynamic_categories'] == 1) echo ' selected="selected"'; ?>><?php _e('Create category', 'content-egg'); ?></option>
                    <option value="2"<?php if ($item['config']['dynamic_categories'] == 2) echo ' selected="selected"'; ?>><?php _e('Create nested categories', 'content-egg'); ?></option>
                </select>                
                <p class="description"><?php _e('Create a category automatically, if the main product has a category data.', 'content-egg'); ?></p>                
            </td>
        </tr>         

        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="min_modules_count"><?php _e('Minimum modules required', 'content-egg'); ?></label>
            </th>
            <td>
                <input id="min_modules_count" name="item[min_modules_count]" value="<?php echo esc_attr($item['min_modules_count']) ?>"
                       type="number" class="small-text">
                <p class="description"><?php _e('Post will not be published if no content for such number of modules.', 'content-egg'); ?></p>                
            </td>
        </tr>      

        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="min_comment_count"><?php _e('Minimum reviews required', 'content-egg'); ?></label>
            </th>
            <td>
                <input id="min_modules_count" name="item[config][min_comments_count]" value="<?php echo esc_attr($item['config']['min_comments_count']) ?>"
                       type="number" class="small-text">
                <p class="description"><?php _e('Post will not be published if there are no user reviews. This option works only for AE modules.', 'content-egg'); ?></p>                
            </td>
        </tr>         

        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="autoupdate_modules"><?php _e('Automatic update', 'content-egg'); ?></label>
            </th>
            <td>                
                <div class="cegg-checkboxgroup">
                    <?php foreach (ModuleManager::getInstance()->getAffiliateParsers(false) as $module): ?>
                        <div class="cegg-checkbox">
                            <label><input <?php if (in_array($module->getId(), $item['autoupdate_modules'])) echo 'checked'; ?> value="<?php echo esc_attr($module->getId()); ?>" type="checkbox" name="item[autoupdate_modules][]" /><?php echo $module->getName(); ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <p class="description">
                    <?php _e('For selected modules, the current keyword will be used as a keyword for autoupdate. Data of the module will be updated periodically In accordance with the configuration of the lifetime of the cache.', 'content-egg'); ?>
                </p>                
            </td>
        </tr>         
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="required_modules"><?php _e('Required modules', 'content-egg'); ?></label>
            </th>
            <td>                
                <div class="cegg-checkboxgroup">
                    <?php foreach (ModuleManager::getInstance()->getParserModules(false) as $module): ?>
                        <div class="cegg-checkbox">
                            <label><input <?php if (in_array($module->getId(), $item['required_modules'])) echo 'checked'; ?> value="<?php echo esc_attr($module->getId()); ?>" type="checkbox" name="item[required_modules][]" /><?php echo $module->getName(); ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <p class="description">
                    <?php _e('Post will not be publicized if no results for these modules.', 'content-egg'); ?>
                </p>                
            </td>
        </tr>         

        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="main_product"><?php _e('Main product', 'content-egg'); ?></label>
            </th>
            <td>
                <select id="main_product" name="item[main_product]">
                    <option value="min_price"<?php if ($item['main_product'] == 'min_price') echo ' selected="selected"'; ?>><?php _e('Minimum price', 'content-egg'); ?></option>
                    <option value="max_price"<?php if ($item['main_product'] == 'max_price') echo ' selected="selected"'; ?>><?php _e('Maximum price', 'content-egg'); ?></option>
                    <option value="random"<?php if ($item['main_product'] == 'random') echo ' selected="selected"'; ?>><?php _e('Random', 'content-egg'); ?></option>
                </select>
                <p class="description">
                    <?php _e('How to choose "main" product?', 'content-egg'); ?>
                    <?php _e('Then you can use tags:', 'content-egg'); ?> %PRODUCT.title%, %PRODUCT.price%, %PRODUCT.merchant%, %PRODUCT.domain%, %PRODUCT.manufacturer%,...
                </p>
            </td>
        </tr>   

        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="tags"><?php _e('Tags', 'content-egg'); ?></label>
            </th>
            <td>

                <input id="tags" name="item[tags]" value="<?php echo esc_attr($item['tags']) ?>"
                       type="text" class="regular-text ltr">
                <p class="description">
                    <?php _e('Comma separated list of tags.', 'content-egg'); ?>
                    <?php _e('"Formulas", and also all tags from title template, will also work here.', 'content-egg'); ?><br>
                </p>                
            </td>
        </tr>          


        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="custom_fields"><?php _e('Add custom fields', 'content-egg'); ?></label>
            </th>
            <td>
                <div style="width: 50%;float:left;">
                    <?php for ($i = 0; $i < count($item['custom_field_names']); $i++): ?>
                        <input type="text" value="<?php echo esc_attr($item['custom_field_names'][$i]); ?>" name="item[custom_field_names][]" placeholder="<?php _e('Custom Field', 'content-egg'); ?> <?php echo $i + 1; ?>" id="custom_fields" /> 
                    <?php endfor; ?>
                </div>
                <div style="width: 50%;float:left;">
                    <?php for ($i = 0; $i < count($item['custom_field_values']); $i++): ?>
                        <input value="<?php echo esc_attr($item['custom_field_values'][$i]); ?>" type="text" name="item[custom_field_values][]" placeholder="<?php _e('Value', 'content-egg'); ?> <?php echo $i + 1; ?>" />
                    <?php endfor; ?>
                </div>
                <p class="description">
                    <?php _e('"Formulas", and also all tags from title template, will also work here.', 'content-egg'); ?><br>
                </p>                  
            </td>
        </tr>    

    </tbody>
</table>
