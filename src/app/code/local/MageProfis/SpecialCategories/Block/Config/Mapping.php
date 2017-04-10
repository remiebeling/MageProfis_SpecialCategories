<?php

class MageProfis_SpecialCategories_Block_Config_Mapping extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{

    public function _prepareToRender()
    {
        $this->addColumn('cat_id', array(
            'label' => Mage::helper('customerfilter')->__('Category ID'),
            'style' => 'width:45px',
        ));
        $this->addColumn('attr_code', array(
            'label' => Mage::helper('customerfilter')->__('Attribute Code'),
            'style' => 'width:150px',
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('customerfilter')->__('Add');
    }

}
