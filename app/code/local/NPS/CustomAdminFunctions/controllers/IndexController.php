<?php

class NPS_CustomAdminFunctions_IndexController extends Mage_Adminhtml_Controller_Action {

	public function indexAction() {

		//run all pre head commands
		$this->requestFunctions();

		//check for bthom function controlling the page output for primary content
		$btf = 0; //default welcome page
		if (isset($_GET['btf'])) {
			$btf = $_GET['btf'];
		}

		//function array to control output of primary content
		$displayModes = array(
			'npsWelcomePage',
			'createAddOptionsAttributeForm',
		);

		$primaryContent = '<style>' . file_get_contents(Mage::getBaseDir('base') . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'code' . DIRECTORY_SEPARATOR . 'local' . DIRECTORY_SEPARATOR . 'NPS' . DIRECTORY_SEPARATOR . 'CustomAdminFunctions' . DIRECTORY_SEPARATOR . 'Helper' . DIRECTORY_SEPARATOR . 'adminStyle.css') . '</style>';
		$primaryContent .= '<div id="nps-custom-attr-manager-container">' . call_user_func(array($this, $displayModes[$btf])) . '</div>';

		//load the layout
		$this->loadLayout();

		//set the menu item active
		$this->_setActiveMenu('catalog/nps_attribute_manager_menu');

		//set left block
		$leftBlock = $this->getLayout()
		                  ->createBlock('core/text')
		                  ->setText($this->leftColumnHtml());

		//compile the lyout
		$block = $this->getLayout()
		              ->createBlock('core/text', 'attr-mass-addition')
		              ->setText($primaryContent);

		//add content block to layout
		$this->_addLeft($leftBlock);
		$this->_addContent($block);

		//render the layout
		$this->renderLayout();
	}

	public function requestFunctions() {

		//check if form is subitted
		if (isset($_POST['nps_function'])) {
			$append_url = null;

			//if mass attribute addition
			if ($_POST['nps_function'] == 'mass_attr_option_add') {

				$append_url = 'btf=1';

				//set the attribute name
				$attr_code = $_POST['nps_attr_select'];

				//$start sorting
				$sort_start = $_POST['nps_attr_start_number'];

				//set the options
				$attr_options = $_POST['nps_attr_new_options'];
				$attr_options = explode(',', $_POST['nps_attr_new_options']);

				//process and remove any blanks prior to processing
				foreach ($attr_options as $key => $option) {if ($option == '' || empty($option)) {unset($attr_options[$key]);}}

				//process new options
				$this->addAttributeOptions($attr_code, $attr_options, $sort_start);
			}

			Mage::app()->getFrontController()->getResponse()->setRedirect($_SERVER['REQUEST_URI'] . '?' . $append_url);
		}
	}

	private function leftColumnHtml() {

		//set url parts
		$url = explode('?', $_SERVER['PHP_SELF']);
		$url_base = $url[0];
		if (!empty($url[1])) {
			$params = explode('&', $url[1]);
		} else {
			$params = array();
		}

		//title and list start
		$html = '<h2 style="border-bottom: 1px dotted #d9d9d9;font-size:15px;">NPS Custom Attribute Tools</h2>';
		$html .= '<ul id="nps-admin-custom-attr-nav">';

		//mass add options
		$html .= '<a href="' . $url_base . '?btf=1" title="Mass Attribute Option Addition"><li data-content-body="">Mass Attribute Option Addition</li></a>';

		//close the list
		$html .= '</ul>';

		return $html;
	}

	private function npsWelcomePage() {
		$html = '<h1>NPS Custom Attribute Tools</h1>';
		$html .= '<p>Please select a function from the left</p>';
		return $html;
	}

	private function createAddOptionsAttributeForm() {

		//start html output
		$html = '<h1>Mass Attribute Option Addition</h1>';

		//explanation
		$html .= '<p class="page-head-note">This area will allow you to insert a massive amount of attribute options for a selected attribute. Enter a comma separated list of values below to add them to the attribute you select. It is imperative that you <span style="color:red;font-weight:bold;">DO NOT</span> insert values that have comma in them or you will confuse the system and cause undesired results.</p>';
		$html .= '<p class="page-head-note">Options should not be duplicated and so any from your list that exist in the database will not be inserted. It should be noted that options that are inserted will be given sort ordering starting with 0 and progressing through the group of options. If you would prefer that the ordering start at a different number select it below.</p>';
		$html .= '<p class="page-head-note">You may only select options that are either drop down or multiselect</p>';

		//start form
		$html .= '<form id="nps_mass_attr_option_add" name="nps_mass_attr_option_add" method="post" action="' . $_SERVER['PHP_SELF'] . '" enctype="multipart/form-data">';

		//include hidden form key and function command
		$html .= '<input type="hidden" name="nps_function" value="mass_attr_option_add">';
		$html .= '<input type="hidden" name="form_key" value="' . Mage::getSingleton('core/session')->getFormKey() . '">';

		//start select box
		$html .= '<div class="half-block">';
		$html .= '<label for="nps_attr_select">Select Attribute</label>';
		$html .= '<select name="nps_attr_select" required value=""><option>SELECT ATTRIBUTE</option>';

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
		$html .= '</div>';

		//start number
		$html .= '<div class="half-block">';
		$html .= '<label for="nps_attr_start_number">Start Ordering From</label>';
		$html .= '<input type="number" name="nps_attr_start_number" value="0"><br>';
		$html .= '</div><div class="clearer"></div>';

		//add text area for adding comma separated values
		$html .= '<label for="nps_attr_new_options" class="full-width" style="display: block;">Comma Separated Values</label>';
		$html .= '<textarea id="nps_attr_new_options" name="nps_attr_new_options" class="full-width" required></textarea><br>';

		//submit button
		$html .= '<input type="submit" value="Update Attribute" style="border-width: 1px;border-style: solid;border-color: #ed6502 #a04300 #a04300 #ed6502;padding: 1px 7px 2px 7px;background: #ffac47 url(images/btn_bg.gif) repeat-x 0 100%;color: #fff;font: bold 12px arial, helvetica, sans-serif;cursor: pointer;text-align: center !important;white-space: nowrap;">';

		//close form
		$html .= '</form>';

		return $html;
	}

	public function addAttributeOptions($attribute_code, array $optionsArray, $sort_start) {

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
					'sort_order' => $sortOrder + $sort_start,
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