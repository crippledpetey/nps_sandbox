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
}