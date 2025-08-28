<?php
namespace Netsteps\PopulateBooks\Model\Category;

class DataProvider extends \Magento\Catalog\Model\Category\DataProvider
{

    protected function getFieldsMap()
    {
        $fields = parent::getFieldsMap();
        $fields['content'][] = 'category_thumb_image'; // custom image field

        return $fields;
    }
}
