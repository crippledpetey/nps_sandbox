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

			//set options
			$options = json_decode($attr['options'], true);
			//check for value
			if ($value = $_product->getResource()->getAttribute($attr['attribute_code'])->getFrontend()->getValue($_product)) {
				//start list if necessary
				if (empty($html)) {
					$html = '<ul id="sub-desc-' . $region . '-list" class="prd-sub-desc-list">';
				}

				//check for supplemental information
				$value_supp = null;
				if (!empty($options['nps_attr_option_specs_list_supp'])) {
					$value_supp = '<span class="sub-desc-list-supp">(' . $options['nps_attr_option_specs_list_supp'] . ')</span>';
				}

				//check for uom
				$uom = null;
				if (!empty($options['attr_option_add_uom'])) {
					$uom = '<span class="sub-desc-list-uom">' . $options['attr_option_add_uom'] . '</span>';
				}

				$html .= '<li><span class="sub-desc-list-label">' . ucwords($attr['frontend_label']) . ':</span><span class="sub-desc-list-value">' . $value . $uom . '</span>' . $value_supp . '</li>';
			}
		}
		if (!empty($html)) {$html .= '</ul>';}

		return $html;
	}

	public function getFeatures($_product, $_shortcode_class) {

		//get attributes
		$display_attrs = $this->getRelevantAttributes('feat');
		$attribute_supp = $this->generateSubDescListHtml($_product, $display_attrs, 'feat');

		$return = null;
		$value = Mage::getResourceModel('catalog/product')->getAttributeRawValue($_product->getId(), 'nps_desc_region_feat', $this->storeID);
		if (!empty($value) || !empty($attribute_supp)) {
			$return = '<div class="product-collateral product-subdescription"><div class="box-collateral box-description" style="width: 100%;"><h2>Features</h2><div class="product-subdescription-autopop">' . $attribute_supp . '</div><div class="std">' . $value . '</div></div><div class="clearer"></div></div>';
		}
		$return = $this->processShortcodes($_shortcode_class, $return);
		return $return;
	}

	public function getSpecs($_product, $_shortcode_class) {
		//get attributes
		$display_attrs = $this->getRelevantAttributes('specs');
		$attribute_supp = $this->generateSubDescListHtml($_product, $display_attrs, 'specs');

		$return = null;
		$value = Mage::getResourceModel('catalog/product')->getAttributeRawValue($_product->getId(), 'nps_desc_region_specs', $this->storeID);
		if (!empty($value) || !empty($attribute_supp)) {
			$return = '<div class="product-collateral product-subdescription"><div class="box-collateral box-description" style="width: 100%;"><h2>Key Specifications</h2><div class="product-subdescription-autopop">' . $attribute_supp . '</div><div class="std">' . $value . '</div></div><div class="clearer"></div></div>';
		}
		$return = $this->processShortcodes($_shortcode_class, $return);
		return $return;
	}

	public function getTech($_product, $_shortcode_class) {
		//get attributes
		$display_attrs = $this->getRelevantAttributes('tech');
		$attribute_supp = $this->generateSubDescListHtml($_product, $display_attrs, 'tech');

		$return = null;
		$value = Mage::getResourceModel('catalog/product')->getAttributeRawValue($_product->getId(), 'nps_desc_region_tech', $this->storeID);
		if (!empty($value) || !empty($attribute_supp)) {
			//get the manufacturer
			$manu = $_product->getResource()->getAttribute('manufacturer')->getFrontend()->getValue($_product);
			//Mage::getResourceModel('catalog/product')->getAttributeRawValue($_product->getId(), 'manufacturer', $this->storeID);
			$return = '<div class="product-collateral product-subdescription"><div class="box-collateral box-description" style="width: 100%;"><h2>' . ucwords($manu) . ' Technologies</h2><div class="product-subdescription-autopop">' . $attribute_supp . '</div><div class="std">' . $value . '</div></div><div class="clearer"></div></div>';
		}
		$return = $this->processShortcodes($_shortcode_class, $return);
		return $return;
	}

	public function getMaint($_product, $_shortcode_class) {
		//get attributes
		$display_attrs = $this->getRelevantAttributes('maint');
		$attribute_supp = $this->generateSubDescListHtml($_product, $display_attrs, 'maint');

		$return = null;
		$value = Mage::getResourceModel('catalog/product')->getAttributeRawValue($_product->getId(), 'nps_desc_region_maint', $this->storeID);
		if (!empty($value) || !empty($attribute_supp)) {
			//get the manufacturer
			$manu = $_product->getResource()->getAttribute('manufacturer')->getFrontend()->getValue($_product);
			//Mage::getResourceModel('catalog/product')->getAttributeRawValue($_product->getId(), 'manufacturer', $this->storeID);
			$return = '<div class="product-collateral product-subdescription"><div class="box-collateral box-description" style="width: 100%;"><h2>' . ucwords($manu) . ' Suggested Maintenance</h2><div class="product-subdescription-autopop">' . $attribute_supp . '</div><div class="std">' . $value . '</div></div><div class="clearer"></div></div>';
		}

		$return = $this->processShortcodes($_shortcode_class, $return);
		return $return;
	}
	protected function processShortcodes($_shortcode_object, $content) {
		//get array of description shortcode locations
		$shortcodes = $_shortcode_object->getShotcodeLocations($content);

		//if there are shortcodes process them
		if ($shortcodes) {
			//get the data
			$shortcodes = $_shortcode_object->getShortcodeData($shortcodes, $content);
			//reset the description with the new shortcodes
			$content = $_shortcode_object->processShortcodeData($shortcodes, $content);
		}

		//ouput description
		return $content;
	}
	public function getRelevantAttributes($region) {

		//check for existing option
		$select = $this->sqlwrite->select()->from('nps_prd_desc_region_' . $region, array('attribute_id', 'options', 'parent_show', 'desc_show', 'attribute_code', 'frontend_label', 'frontend_input'));
		$rowsArray = $this->sqlread->fetchAll($select);
		return $rowsArray;

	}
}

?>