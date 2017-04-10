<?php
class MageProfis_SpecialCategories_Adminhtml_SpecialcategoriesController extends Mage_Adminhtml_Controller_Action
{
    public function relationsAction()
    {
        try {
            Mage::getModel('specialcategories/catalog_category_relations')->addItemsToCategories();
            $this->_getSession()->addSuccess(Mage::helper('specialcategories')->__('set up realtions successfull'));
            Mage::app()->getResponse()->setRedirect($_SERVER['HTTP_REFERER']);
            Mage::app()->getResponse()->sendResponse();
                exit;

        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect($this->_redirectReferer());
        }
        
        
    }
    
}

