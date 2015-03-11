<?php
class NPS_CustomAdminFunctions_Model_Attribute_Manager {

	/**
	 * Create Product attributes for select list
	 *
	 * @param string $attribute_code
	 * @param array $optionsArray
	 */

	public function addAttributeOptions($attribute_code, array $optionsArray) {
		$tableOptions = $this->getTable('eav_attribute_option');
		$tableOptionValues = $this->getTable('eav_attribute_option_value');
		$attributeId = (int) $this->getAttribute('catalog_product', $attribute_code, 'attribute_id');
		foreach ($optionsArray as $sortOrder => $label) {
			// add option
			$data = array(
				'attribute_id' => $attributeId,
				'sort_order' => $sortOrder,
			);
			$this->getConnection()->insert($tableOptions, $data);

			// add option label
			$optionId = (int) $this->getConnection()->lastInsertId($tableOptions, 'option_id');
			$data = array(
				'option_id' => $optionId,
				'store_id' => 0,
				'value' => $label,
			);
			$this->getConnection()->insert($tableOptionValues, $data);
		}
	}

}