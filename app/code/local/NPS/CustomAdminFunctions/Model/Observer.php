<?php
/**
 * Our class name should follow the directory structure of
 * our Observer.php model, starting from the namespace,
 * replacing directory separators with underscores.
 * i.e. app/code/local/SmashingMagazine/
 *                     LogProductUpdate/Model/Observer.php
 */
class NPS_CustomAdminFunctions_Model_Observer {
	/**
	 * Magento passes a Varien_Event_Observer object as
	 * the first parameter of dispatched events.
	 */

	public function __construct() {

	}

	public function updateProductTypeConfig(Varien_Event_Observer $observer) {

		$product = $observer->getEvent()->getProduct();

		$sql = 'UPDATE `catalog_product_entity_varchar` SET `value` = ? WHERE `entity_id` = ? AND `attribute_id` = ?';
		$connection_write = Mage::getSingleton('core/resource')->getConnection('core_write');
		$connection_write->query($sql, array($product->getTypeId(), $product->getId(), 1598));

		//check if product is container type and update attributes if so
		$attributeSet = Mage::getModel("eav/entity_attribute_set")->load($product->getAttributeSetId())->_data['attribute_set_name'];

		//check if type is grouped
		if ($product->getAttributeSetId() == 93) {
			$this->updateContainerProductAttributes($observer);
		}
	}

	public function updateGroupedProductPricing(Varien_Event_Observer $observer) {
		$product = $observer->getEvent()->getProduct();
		$productType = $product->getTypeID();

		//check if type is grouped
		if ($productType == 'grouped') {
			$price = 0;

			//get associated products
			$associatedProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);

			//add price
			foreach ($associatedProducts as $ass_prd) {
				$price += number_format($ass_prd->getPrice(), 2);
			}

			$sql = 'UPDATE `catalog_product_entity_decimal` SET `value` = ? WHERE `entity_id` = ? AND `attribute_id` = ?';
			$connection_write = Mage::getSingleton('core/resource')->getConnection('core_write');
			$connection_write->query($sql, array($price, $product->getId(), 99));

		}
	}

	public function updateGroupedProductInventory(Varien_Event_Observer $observer) {
		$product = $observer->getEvent()->getProduct();
		$productType = $product->getTypeID();

		//check if type is grouped
		if ($productType == 'grouped') {
			$inv_total = 0;

			//get associated products
			$associatedProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);

			//add price
			foreach ($associatedProducts as $ass_prd) {
				$stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($ass_prd);
				if ($stock->getQty() < $inv_total) {
					$inv_total = $stock->getQty();
				}
			}

			//check if over 0
			$in_stock = 0;
			if ($inv_total > 0) {$in_stock = 1;}

			$sql = 'UPDATE `cataloginventory_stock_item` SET `qty` = ?, is_in_stock = ? WHERE `product_id` = ?';
			$connection_write = Mage::getSingleton('core/resource')->getConnection('core_write');
			$connection_write->query($sql, array($inv_total, $inv_total, $product->getId()));

		}
	}
	private function getChildrenProducts($product_id) {
		$query = "SELECT DISTINCT e.entity_id FROM catalog_product_option AS p INNER JOIN catalog_product_option_type_value AS o ON o.option_id = p.option_id INNER JOIN catalog_product_entity AS e ON e.sku = o.sku WHERE p.product_id = " . $product_id;
		return Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($query);
	}

	public function updateContainerProductAttributes(Varien_Event_Observer $observer) {
		//get products and child IDs
		$product = $observer->getEvent()->getProduct();
		$children = $this->getChildrenProducts($product->getId());

		//set connection
		$connection_write = Mage::getSingleton('core/resource')->getConnection('core_read');

		//blank array for updates
		$updates = array();

		//get attributes to carry over
		$select = $connection_write->select()->from('nps_attribute_options', array('id', 'attribute_id', 'options', 'parent_show', 'desc_show'))->where('parent_show=?', true);
		$attributes = $connection_write->fetchAll($select);

		//loop through attributes
		foreach ($attributes as $attr) {
			//set control data
			$data = json_decode($attr['options']);
			$child_temp = array();

			//loop through children to get attr values
			foreach ($children as $child) {
				if ($value = Mage::getResourceModel('catalog/product')->getAttributeRawValue($child['entity_id'], $data->attribute_id, Mage::app()->getStore()->getStoreId())) {
					if ($data->attr_option_duplicate_handling == 'override') {
						$updates[$data->attribute_id] = $value;
					} elseif ($data->attr_option_duplicate_handling == 'append') {
						if (empty($updates[$data->attribute_id])) {$updates[$data->attribute_id] = '';}
						if (!stripos($updates[$data->attribute_id], $value)) {$updates[$data->attribute_id] .= ',' . $value;}
					} elseif ($data->attr_option_duplicate_handling == 'hide') {
						if (!empty($updates[$data->attribute_id]) && $updates[$data->attribute_id] !== $value) {unset($updates[$data->attribute_id]);}
					} elseif ($data->attr_option_duplicate_handling == 'popular') {
						$updates[$data->attribute_id] = $value;
					}
				}
			}
		}

		//loop through updates
		foreach ($updates as $attr_code => $attr_val) {
			$product->setData($attr_code, $attr_val)->getResource()->saveAttribute($product, $attr_code);
		}

	}
}