<?php

class NPS_CustomAdminFunctions_IndexController extends Mage_Adminhtml_Controller_Action {
	public function indexAction() {

		//check if form is subitted
		if (isset($_POST['nps_function'])) {

			//if mass attribute addition
			if ($_POST['nps_function'] == 'mass_attr_option_add') {
				//set the attribute name
				$attr_code = $_POST['nps_attr_select'];

				//set the options
				$attr_options = $_POST['nps_attr_new_options'];
				$attr_options = explode(',', $_POST['nps_attr_new_options']);

				//process and remove any blanks
				foreach ($attr_options as $key => $option) {
					if ($option == '' || empty($option)) {
						unset($attr_options[$key]);
					}
				}

				//process new options
				$this->addAttributeOptions($attr_code, $attr_options);
			}
		}

		//load the layout
		$this->loadLayout();

		//set the menu item active
		$this->_setActiveMenu('catalog/nps_attribute_manager_menu');

		//compile the lyout
		$block = $this->getLayout()
		              ->createBlock('core/text', 'attr-mass-addition')
		              ->setText($this->createAddOptionsAttributeForm());

		//add content block to layout
		$this->_addContent($block);

		//render the layout
		$this->renderLayout();
	}

	public function createAddOptionsAttributeForm() {
		//start html output
		$html = '<h1>Mass Attribute Option Addition</h1>';

		//start form
		$html .= '<form id="nps_mass_attr_option_add" name="nps_mass_attr_option_add" method="post" action="' . $_SERVER['PHP_SELF'] . '" enctype="multipart/form-data">';

		//include function command
		$html .= '<input type="hidden" name="nps_function" value="mass_attr_option_add">';

		//incude form key
		$html .= '<input type="hidden" name="form_key" value="' . Mage::getSingleton('core/session')->getFormKey() . '">';

		//start select box
		$html .= '<label for="nps_attr_select">Select Attribute</label><br>';
		$html .= '<select name="nps_attr_select" required><option>SELECT ATTRIBUTE</option>';

		//get the list of attribute that can have options selected
		$attributes = Mage::getResourceModel('catalog/product_attribute_collection')->getItems();
		foreach ($attributes as $attribute) {

			//make sure type is multiselect
			if ($attribute->getFrontendInput() == 'multiselect') {
				$html .= '<option data-attr-type="" value="' . $attribute->getAttributecode() . '">' . $attribute->getFrontendLabel() . '</option>';
			}

		}

		//close select box
		$html .= '</select>';
		$html .= '<p style="font-size: 11px; color: #999;">You may only select options that are either drop down or multiselect</p>';

		//add text area for adding comma separated values
		$html .= '<textarea name="nps_attr_new_options" style="width: 50%;height: 300px;"></textarea><br>';

		//submit button
		$html .= '<input type="submit" value="Update Attribute" style="border-width: 1px;border-style: solid;border-color: #ed6502 #a04300 #a04300 #ed6502;padding: 1px 7px 2px 7px;background: #ffac47 url(images/btn_bg.gif) repeat-x 0 100%;color: #fff;font: bold 12px arial, helvetica, sans-serif;cursor: pointer;text-align: center !important;white-space: nowrap;">';

		//close form
		$html .= '</form>';

		return $html;
	}

	public function addAttributeOptions($attribute_code, array $optionsArray) {

		//database read adapter
		$sqlread = Mage::getSingleton('core/resource')->getConnection('core_read');
		$sqlwrite = Mage::getSingleton('core/resource')->getConnection('core_write');
		$tablePrefix = (string) Mage::getConfig()->getTablePrefix();

		/*
		$tableOptions = Mage::getSingleton('core/resource')->getTable('eav_attribute_option');
		$tableOptionValues = Mage::getSingleton('core/resource')->getTable('eav_attribute_option_value');
		 */

		if ($attributeRaw = Mage::getSingleton("eav/config")->getAttribute('catalog_product', $attribute_code, 'attribute_id')) {

			//get and clean existing options
			$attribute_existing = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $attribute_code);
			if ($attribute_existing->usesSource()) {

				//compile the options
				$options = $attribute_existing->getSource()->getAllOptions(false);

				//loop through and remove any duplicates that exist from previous
				foreach ($options as $key => $val) {
					if ($new_key = array_search($val['label'], $optionsArray)) {

						unset($optionsArray[$new_key]);
					}
				}
			}

			$attributeData = $attributeRaw->getData();
			$attributeId = $attributeData['attribute_id'];

			foreach ($optionsArray as $sortOrder => $label) {
				// add option
				$data = array(
					'attribute_id' => $attributeId,
					'sort_order' => $sortOrder,
				);
				$sqlwrite->insert('eav_attribute_option', $data);

				// add option label
				$optionId = (int) $sqlread->lastInsertId('eav_attribute_option', 'option_id');
				$data = array(
					'option_id' => $optionId,
					'store_id' => 0,
					'value' => $label,
				);
				$sqlwrite->insert('eav_attribute_option_value', $data);
			}
		}

	}

}

?>