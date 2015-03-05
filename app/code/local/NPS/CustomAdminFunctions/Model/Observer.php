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

		// Write a new line to var/log/product-updates.log
		$name = $product->getName();
		$sku = $product->getSku();
		/*$base_path = Mage::getBaseDir('base');*/
		/*$test_file = fopen($base_path . DIRECTORY_SEPARATOR . 'test_file.txt', 'w+');*/
		/*fwrite($test_file, "{$name} ({$sku}) updated");*/

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
}