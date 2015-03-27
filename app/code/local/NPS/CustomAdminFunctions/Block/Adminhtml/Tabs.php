<?php

class NPS_CustomAdminFunctions_Block_Adminhtml_Tabs extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs {
	public $parent;

	protected function _prepareLayout() {
		//set object product
		$this->_product = $this->getProduct();

		//This will get all existing tabs which is Default in Magento
		$this->parent = parent::_prepareLayout();

		//Now here we are adding new tab
		$this->addTab('mediamanager', array(
			'label' => Mage::helper('catalog')->__('NPS Media Manager'),
			'content' => $this->getLayout()->createBlock('customadminfunctions/adminhtml_tabs_mediamanager')->toHtml(),
		));

		return $this->parent;
	}
}