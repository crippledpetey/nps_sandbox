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

	private function generateSubDescListHtml($_product, $display_attrs, $region) {
		$html = null;

		foreach ($display_attrs as $attr) {
			//check for value
			if ($value = $_product->getResource()->getAttribute($attr['attribute_code'])->getFrontend()->getValue($_product)) {
				//start list if necessary
				if (empty($html)) {
					$html = '<ul id="sub-desc-' . $region . '-list" class="prd-sub-desc-list">';
				}

				$html .= '<li><span class="sub-desc-list-label">' . ucwords($attr['frontend_label']) . '</span><span class="sub-desc-list-value">' . $value . '</span></li>';
			}
		}
		if (!empty($html)) {$html = '</ul>';}

		return $html;
	}

	public function getFeatures($_product) {

		//get attributes
		$display_attrs = $this->getRelevantAttributes('feat');
		$attribute_supp = $this->generateSubDescListHtml($_product, $display_attrs, 'feat');

		$return = null;
		$value = Mage::getResourceModel('catalog/product')->getAttributeRawValue($_product->getId(), 'nps_desc_region_feat', $this->storeID);
		if (!empty($value)) {
			$return = '<div class="product-collateral product-subdescription"><div class="box-collateral box-description" style="width: 100%;"><h2>Features</h2><div class="product-subdescription-autopop">' . $attribute_supp . '</div><div class="std">' . $value . '</div></div><div class="clearer"></div></div>';
		}
		return $return;
	}

	public function getSpecs($_product) {
		//get attributes
		$display_attrs = $this->getRelevantAttributes('feat');
		$attribute_supp = $this->generateSubDescListHtml($_product, $display_attrs, 'feat');

		$return = null;
		$value = Mage::getResourceModel('catalog/product')->getAttributeRawValue($_product->getId(), 'nps_desc_region_specs', $this->storeID);
		if (!empty($value)) {
			$return = '<div class="product-collateral product-subdescription"><div class="box-collateral box-description" style="width: 100%;"><h2>Key Specifications</h2><div class="product-subdescription-autopop">' . $attribute_supp . '</div><div class="std">' . $value . '</div></div><div class="clearer"></div></div>';
		}
		return $return;
	}

	public function getTech($_product) {
		//get attributes
		$display_attrs = $this->getRelevantAttributes('feat');
		$attribute_supp = $this->generateSubDescListHtml($_product, $display_attrs, 'feat');

		$return = null;
		$value = Mage::getResourceModel('catalog/product')->getAttributeRawValue($_product->getId(), 'nps_desc_region_tech', $this->storeID);
		if (!empty($value)) {
			//get the manufacturer
			$manu = $_product->getResource()->getAttribute('manufacturer')->getFrontend()->getValue($_product);
			//Mage::getResourceModel('catalog/product')->getAttributeRawValue($_product->getId(), 'manufacturer', $this->storeID);
			$return = '<div class="product-collateral product-subdescription"><div class="box-collateral box-description" style="width: 100%;"><h2>' . ucwords($manu) . ' Technologies</h2><div class="product-subdescription-autopop">' . $attribute_supp . '</div><div class="std">' . $value . '</div></div><div class="clearer"></div></div>';
		}
		return $return;
	}

	public function getMaint($_product) {
		//get attributes
		$display_attrs = $this->getRelevantAttributes('feat');
		$attribute_supp = $this->generateSubDescListHtml($_product, $display_attrs, 'feat');

		$return = null;
		$value = Mage::getResourceModel('catalog/product')->getAttributeRawValue($_product->getId(), 'nps_desc_region_maint', $this->storeID);
		if (!empty($value)) {
			//get the manufacturer
			$manu = $_product->getResource()->getAttribute('manufacturer')->getFrontend()->getValue($_product);
			//Mage::getResourceModel('catalog/product')->getAttributeRawValue($_product->getId(), 'manufacturer', $this->storeID);
			$return = '<div class="product-collateral product-subdescription"><div class="box-collateral box-description" style="width: 100%;"><h2>' . ucwords($manu) . ' Suggested Maintenance</h2><div class="product-subdescription-autopop">' . $attribute_supp . '</div><div class="std">' . $value . '</div></div><div class="clearer"></div></div>';
		}
		return $return;
	}
	public function getRelevantAttributes($region) {

		//check for existing option
		$select = $this->sqlwrite->select()->from('nps_prd_desc_region_' . $region, array('attribute_id', 'options', 'parent_show', 'desc_show', 'attribute_code', 'frontend_label', 'frontend_input'));
		$rowsArray = $this->sqlread->fetchAll($select);
		return $rowsArray;

	}
}

?>