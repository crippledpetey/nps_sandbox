<?php
/**
CUSTOM DROP PHP FUNCTIONS
 */
class productView {

	public function __construct() {
		//database read adapter
		$this->sqlread = Mage::getSingleton('core/resource')->getConnection('core_read');
		$this->sqlwrite = Mage::getSingleton('core/resource')->getConnection('core_write');
		//database table prefix
		$this->tablePrefix = (string) Mage::getConfig()->getTablePrefix();

		$this->storeID = Mage::app()->getStore()->getStoreId();
	}

	public function getFeatures($_product) {
		$return = null;
		$value = Mage::getResourceModel('catalog/product')->getAttributeRawValue($_product->getId(), 'nps_desc_region_feat', $this->storeID);
		if (!empty($value)) {
			$return = '<div class="product-collateral product-subdescription"><div class="box-collateral box-description" style="width: 100%;"><h2>Features</h2><div class="std">' . $value . '</div></div><div class="clearer"></div></div>';
		}
		return $return;
	}

	public function getSpecs($_product) {
		$return = null;
		$value = Mage::getResourceModel('catalog/product')->getAttributeRawValue($_product->getId(), 'nps_desc_region_specs', $this->storeID);
		if (!empty($value)) {
			$return = '<div class="product-collateral product-subdescription"><div class="box-collateral box-description" style="width: 100%;"><h2>Key Specifications</h2><div class="std">' . $value . '</div></div><div class="clearer"></div></div>';
		}
		return $return;
	}

	public function getTech($_product) {
		$return = null;
		$value = Mage::getResourceModel('catalog/product')->getAttributeRawValue($_product->getId(), 'nps_desc_region_tech', $this->storeID);
		if (!empty($value)) {
			//get the manufacturer
			$manu = $_product->getResource()->getAttribute('manufacturer')->getFrontend()->getValue($_product);
			//Mage::getResourceModel('catalog/product')->getAttributeRawValue($_product->getId(), 'manufacturer', $this->storeID);
			$return = '<div class="product-collateral product-subdescription"><div class="box-collateral box-description" style="width: 100%;"><h2>' . ucwords($manu) . ' Technologies</h2><div class="std">' . $value . '</div></div><div class="clearer"></div></div>';
		}
		return $return;
	}

	public function getMaint($_product) {
		$return = null;
		$value = Mage::getResourceModel('catalog/product')->getAttributeRawValue($_product->getId(), 'nps_desc_region_maint', $this->storeID);
		if (!empty($value)) {
			//get the manufacturer
			$manu = $_product->getResource()->getAttribute('manufacturer')->getFrontend()->getValue($_product);
			//Mage::getResourceModel('catalog/product')->getAttributeRawValue($_product->getId(), 'manufacturer', $this->storeID);
			$return = '<div class="product-collateral product-subdescription"><div class="box-collateral box-description" style="width: 100%;"><h2>' . ucwords($manu) . ' Suggested Maintenance</h2><div class="std">' . $value . '</div></div><div class="clearer"></div></div>';
		}
		return $return;
	}
}

?>