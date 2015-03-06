<?php
/**
 * Our class name should follow the directory structure of
 * our Observer.php model, starting from the namespace,
 * replacing directory separators with underscores.
 * i.e. app/code/local/SmashingMagazine/
 *                     LogProductUpdate/Model/Observer.php
 */
class NPS_BetterLayerNavigation_Model_Observer {
	/**
	 * Magento passes a Varien_Event_Observer object as
	 * the first parameter of dispatched events.
	 */
	public function logUpdate(Varien_Event_Observer $observer) {
		// Retrieve the product being updated from the event observer
		$product = $observer->getEvent()->getProduct();

		// Write a new line to var/log/product-updates.log
		$name = $product->getName();
		$sku = $product->getSku();
		Mage::log(
			"{$name} ({$sku}) updated",
			null,
			'product-updates.log'
		);
	}

	protected function getChildIDFromParent() {
		$query = "SELECT o.option_type_id FROM catalog_product_option AS p INNER JOIN catalog_product_option_type_value AS o ON o.option_id = p.option_id INNER JOIN catalog_product_entity AS e ON e.sku = o.sku WHERE e.entity_id = " . $entity_id;
		return $this->sqlread->fetchAll($query);
	}

	public function updateProductCookiesForNPSF(Varien_Event_Observer $observer, $npsf = null) {

		//check for existing cookie and decode if necessary
		$cookie_id = base64_encode('nps_previous_products');
		if (isset($_COOKIE[$cookie_id])) {
			$value_array = json_decode(base64_decode($_COOKIE[$cookie_id]), true);
		} else {
			$value_array = array();
		}

		//check for npsf
		$npsf_url = null;
		$npsf_cookie_append = null;
		if (isset($_GET['npsf'])) {
			$npsf = $_GET['npsf'];
			$npsf_url = 'npsf=' . $npsf;
			$npsf_cookie_append = '-' . $npsf;
		}

		$chid_url = null;
		$chid_cookie_append = null;
		if (isset($_GET['chid'])) {
			$chid = $_GET['chid'];
			$chid_url = 'chid=' . $chid;
			$chid_cookie_append = '-' . $chid;
		}

		//set the product that was clicked
		$product = $observer->getEvent()->getProduct();

		//set the title
		$manufacturer = $product->getAttributeText('manufacturer');
		$sku = $product->getSKU();
		$title = $product->getAttributeText('manufacturer') . ' ' . $product->getSKU() . ' - ' . $product->getName();

		//get image
		if (empty($chid)) {
			$image_id = $product->getID();
		} else {
			$image_id = $chid;
		}
		$img_prd = Mage::getModel('catalog/product')->load($image_id);
		$img_path_url = $img_prd->getImage();

		//check of product in array
		$value_array[$product->getID() . $npsf_cookie_append] = array(
			'parent_id' => $product->getID(),
			'npsf' => $npsf,
			'chid' => $chid,
			'img' => $img_path_url,
			'url' => $_SERVER['REQUEST_URI'],
			'title' => $title,
			'manufacturer' => $manufacturer,
			'sku' => $sku,
		);

		//set cookie values
		$cookieValue = base64_encode(json_encode($value_array));
		$cookieExpire = 0;
		$cookieDomain = '/';
		setcookie($cookie_id, $cookieValue, $cookieExpire, $cookieDomain);
	}
}