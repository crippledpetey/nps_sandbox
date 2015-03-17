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
	static public function slugify($text) {
		// replace non letter or digits by -
		$text = preg_replace('~[^\\pL\d]+~u', '-', $text);

		// trim
		$text = trim($text, '-');

		// transliterate
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

		// lowercase
		$text = strtolower($text);

		// remove unwanted characters
		$text = preg_replace('~[^-\w]+~', '', $text);

		if (empty($text)) {
			return 'n-a';
		}

		return $text;
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

	public function updateUrlReWrite(Varien_Event_Observer $observer) {
		require_once Mage::getBaseDir('base') . '/app/code/local/NPS/BetterLayerNavigation/Helper/product.drop.class.php';

		//check fo make sure product is container product
		$containerPrdCheck = Mage::getModel("eav/entity_attribute_set")->load($observer->getEvent()->getProduct()->getAttributeSetId())->getData();
		if ($containerPrdCheck['attribute_set_name'] == 'Container Product') {

			//check to make sure required attributes are present
			$prdAttr = Mage::getModel('catalog/product')->load($observer->getEvent()->getProduct()->getId());

			//set variable that will be used
			$attr_manufacturer = $prdAttr->getAttributeText('manufacturer');
			$attr_container_productid = $prdAttr->getResource()->getAttribute('container_productid')->getFrontend()->getValue($prdAttr);
			$attr_url_key = $prdAttr->getResource()->getAttribute('url_key')->getFrontend()->getValue($prdAttr);

			//make sure required variables are present
			if (!empty($attr_container_productid) && !empty($attr_manufacturer)) {

				//get url ID
				$coreUrl = Mage::getModel('core/url_rewrite')->setStoreId(1)->loadByRequestPath($prdAttr->getUrlPath()); //
				$rwID = $coreUrl->getData()['url_rewrite_id'];

				//get existing rewrites
				$db_rewrites = $this->getRewrites($prdAttr->getId());

				//set static values for DB insertion
				$store_id = '1';
				$category_id = null;
				$product_id = null;
				$id_path = 'product/' . $prdAttr->getId();
				$target_path_base = $attr_url_key;
				$is_system = '0';
				$options = 'RP';
				$description = null;

				//compile new urls
				$rules = array();
				$url_manufacturer = self::slugify($attr_manufacturer);
				$url_container_productid = self::slugify($attr_container_productid);

				//start product drop class for obtaining custom option information
				$nps_options = new productDrop;
				if ($nps_options->getUrlOptionsForProduct($prdAttr->getId())) {

					//create array of rewrite URLS
					foreach ($nps_options->getUrlOptionsForProduct($prdAttr->getId()) as $key => $val) {

						//slugify finish
						$url_finish = self::slugify($val['title']);

						//set container product url
						$preferred = $url_manufacturer . '/' . $url_container_productid . '/' . $url_finish;
						$cp_target = $target_path_base . '.html?npsf=' . $val['npsf'] . '&chid=' . $val['chid'];

						//create redirects
						//$rules[] = 'Redirect 301 /product/' . $manufacturer . '/' . $url_container_productid . '/' . $url_finish . ' ' . $preferred;

						//create core rewrite
						$rules[] = $preferred . ' ' . $cp_target;

						//$urls[] = 'product/' . $url_manufacturer . '/' . $url_container_productid . '/' . $url_finish_title . '?npsf=' . $val['npsf'] . '&chid=' . $val['chid']);
					}
				}

				if (!empty($rules)) {
					//get/create product rewrites files
					$prd_rw_file_path = Mage::getBaseDir('base') . DIRECTORY_SEPARATOR . 'rewritemap.txt';

					//find string
					$current = file_get_contents($prd_rw_file_path);
					$search_string = "#### " . $prdAttr->getId() . " ####"; //check for existing product info

					//if the product already has records
					if (stripos($current, $search_string)) {

						//explode the string to isolate the product entries
						$file_array = explode($search_string, $current);

						//start of file content
						$new_string = array($file_array[0]);

						//replace old rules in file
						$new_string[] = "#### " . $prdAttr->getId() . " ####\n";
						foreach ($rules as $rule) {
							$new_string[] = $rule . "\n";
						}
						$new_string[] = "#### " . $prdAttr->getId() . " ####";

						//re-append the end of the file
						$new_string[] = $file_array[2];

						//recompile into a string
						$new_string = implode(null, $new_string);

					} else {

						//kill the end line
						$new_string = str_replace("##\n# END REWRITE MAP FILE\n##", null, $current);

						//write new rules to end of file
						$new_string .= "#### " . $prdAttr->getId() . " ####\n";
						foreach ($rules as $rule) {
							$new_string .= $rule . "\n";
						}
						$new_string .= "#### " . $prdAttr->getId() . " ####\n";

						//readd file ending
						$new_string .= "\n##\n# END REWRITE MAP FILE\n##";
					}

					file_put_contents($prd_rw_file_path, $new_string);
				}
			}
		}
	}

	protected function getRewrites($productID) {
		//start database connection and get rewrites from the DB
		$connection_read = Mage::getSingleton('core/resource')->getConnection('core_read');
		$connection_write = Mage::getSingleton('core/resource')->getConnection('core_write');
		$select = $connection_read->select()->from('core_url_rewrite', array('url_rewrite_id', 'store_id', 'category_id', 'product_id', 'id_path', 'request_path', 'target_path', 'is_system', 'options', 'description'))->where('`is_system` = 0 AND product_id=?', $productID);
		$rewrites = $connection_read->fetchAll($select);

		return $rewrites;
	}
}
/*
ob_start();
var_dump($prdAttr->getAttributeText('manufacturer'));
var_dump($prdAttr->getResource()->getAttribute('container_productid')->getFrontend()->getValue($prdAttr) );
$output = ob_get_clean();
$fileHandle = fopen(Mage::getBaseDir() . DIRECTORY_SEPARATOR . "testing.txt", "w");
fwrite($fileHandle, $output);
fclose($fileHandle);
 */