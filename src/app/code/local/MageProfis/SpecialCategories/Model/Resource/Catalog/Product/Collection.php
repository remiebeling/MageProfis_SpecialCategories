<?php

class MageProfis_SpecialCategories_Model_Resource_Catalog_Product_Collection
extends Mage_Catalog_Model_Resource_Product_Collection
{

    /**
     * Retrieve is flat enabled flag
     * Return always false if magento run admin
     *
     * @return bool
     */
    public function isEnabledFlat()
    {
        return false;
    }

}