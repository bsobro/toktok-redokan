<?php

/*
  Name: Grid
 */

use ContentEgg\application\helpers\TemplateHelper;

__('Grid', 'content-egg-tpl');
?>

<?php
foreach ($items as $key => $item)
{
    if ($item['img'] && strstr($item['img'], 'images-amazon.com'))
    {
        $items[$key]['img'] = str_replace('.jpg', '._AC_UL250_SR250,250_.jpg', $item['img']);
    }
}

$this->renderPartial('grid', array('items' => $items));
?>
      

