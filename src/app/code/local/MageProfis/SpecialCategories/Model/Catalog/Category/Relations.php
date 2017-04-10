<?php

class MageProfis_SpecialCategories_Model_Catalog_Category_Relations extends Mage_Core_Model_Abstract
{

    protected $_resource;
    protected $_attributeOptions = array();

    public function addItemsToCategories()
    {
        $cats = $this->getMapping();

        foreach ($cats as $category)
        {
            $products = $this->getAffectedProducts($category['attr_code'], true);
            $this->importCategoryRelations($category['cat_id'], $products);
        }
        
        $process = Mage::getSingleton('index/indexer')->getProcessByCode('catalog_category_product');
        $process->reindexEverything();
    }

    public function getMapping()
    {
        $setting = Mage::getStoreConfig('specialcategories/settings/mapping');
        $cats = array();
        if ($setting)
        {
            $setting = unserialize($setting);
            if (is_array($setting))
            {
                foreach ($setting as $cat)
                {
                    $cats[] = $cat;
                }
                return $cats;
            }
            return false;
        }
    }


   
    public function getAffectedProducts($attribute_code, $ids = false)
    {
        $option_id = $this->getYesOptionId($attribute_code);
        $products = Mage::getResourceModel('specialcategories/catalog_product_collection')
                ->addAttributeToFilter($attribute_code, $option_id)
                ->addAttributeToSelect(array('sku', $attribute_code));

            
        if ($ids)
        {
            $products->getAllIds();
        }

        return $products;
    }

    public function getProductsCategory($cats)
    {
        $cat = Mage::getModel('catalog/category')->getCollection()
                ->addAttributeToSelect('name')
                ->addAttributeToFilter('entity_id', array('in' => $cats))
                ->setPageSize(1)
                ->setCurPage(1)
                ->getFirstItem();

        return $cat;
    }

    public function getYesOptionId($attribute_code)
    {
        $attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', $attribute_code);
        $attr = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);

        if ($attr->usesSource())
        {
            $attributeOptions = $attr->getSource()->getAllOptions();
            foreach ($attributeOptions as $option)
            {
                if (in_array(trim(strtolower($option['label'])), array('yes', 'ja', 'si')))
                {
                    return $option['value'];
                }
            }
            return false;
        }
    }

    protected function importCategoryRelations($cat_id, $ids)
    {
        //delete all relations
        $where = $this->_writeConnection()->quoteInto('category_id = ?', $cat_id);
        $this->_writeConnection()->delete($this->getTableName('catalog_category_product'), $where);

        //set up the new ones
        foreach ($ids as $id)
        {
            if ($id instanceof Mage_Catalog_Model_Product)
            {
                $id = $id->getId();
            }
            $this->_writeConnection()->insertOnDuplicate($this->getTableName('catalog_category_product'), array(
                'product_id' => $id,
                'category_id' => $cat_id,
                'position' => 0,
            ));
        }
    }

    /**
     *
     * @return Mage_Core_Model_Resource
     */
    protected function _resource()
    {
        if (is_null($this->_resource))
        {
            $this->_resource = Mage::getSingleton('core/resource');
        }
        return $this->_resource;
    }

    /**
     *
     * @param string $name
     * @return string
     */
    protected function getTableName($name)
    {
        return $this->_resource()->getTableName($name);
    }

    /**
     *
     * @return Varien_Db_Adapter_Interface
     */
    protected function _readConnection()
    {
        return $this->_resource()->getConnection('core_read');
    }

    /**
     *
     * @return Varien_Db_Adapter_Interface
     */
    protected function _writeConnection()
    {
        return $this->_resource()->getConnection('core_read');
    }

}
