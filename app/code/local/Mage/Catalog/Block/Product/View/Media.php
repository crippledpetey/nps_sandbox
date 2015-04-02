<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Simple product data view
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Product_View_Media extends Mage_Catalog_Block_Product_View_Abstract {
	public function __construct() {
		parent::__construct();
		//sql connector
		$this->resource = Mage::getSingleton('core/resource');
		$this->readConnection = $this->resource->getConnection('core_read');
		$this->writeConnection = $this->resource->getConnection('core_write');
	}
	/**
	 * Flag, that defines whether gallery is disabled
	 *
	 * @var boolean
	 */
	protected $_isGalleryDisabled;

	/**
	 * Retrieve list of gallery images
	 *
	 * @return array|Varien_Data_Collection
	 */
	public function getGalleryImages() {
		if ($this->_isGalleryDisabled) {
			return array();
		}
		$collection = $this->getProduct()->getMediaGalleryImages();
		return $collection;
	}

	/**
	 * Retrieve gallery url
	 *
	 * @param null|Varien_Object $image
	 * @return string
	 */
	public function getGalleryUrl($image = null) {
		$params = array('id' => $this->getProduct()->getId());
		if ($image) {
			$params['image'] = $image->getValueId();
		}
		return $this->getUrl('catalog/product/gallery', $params);
	}

	/**
	 * Disable gallery
	 */
	public function disableGallery() {
		$this->_isGalleryDisabled = true;
	}

	public function _getOptionImage($product_id, $option_id) {
		$query = "";
	}
	public function _getSelectedFinish($product_id) {
		//check if chid is set
		if (isset($_GET['chid'])) {
			$selected = $_GET['chid'];
		} else {
			//check if there are children
			if ($this->_checkIfChildren()) {
				$children = $this->_getChildrenProducts();
				$selected = array_values($children)[0]['entity_id'];
			} else {
				$selected = $_product_id;
			}
		}
		return $selected;
	}
	public function _getSelectedStock($product_id) {
		//get the child id for the selected value
		$selected_id = $this->_getSelectedFinish($product_id);

	}
	public function _checkIfChildren() {
		//start product drop helper
		require_once Mage::getBaseDir('base') . '/app/code/local/NPS/BetterLayerNavigation/Helper/product.drop.class.php';
		$nps_prdctrl = new productDrop;
		$test = $nps_prdctrl->getUrlOptionsForProduct($this->_product->getId());
		if (!empty($test)) {
			return true;
		} else {
			return false;
		}
	}
	public function _getFirstChildLink() {
		//start product drop helper
		require_once Mage::getBaseDir('base') . '/app/code/local/NPS/BetterLayerNavigation/Helper/product.drop.class.php';
		$nps_prdctrl = new productDrop;
		$urls = $nps_prdctrl->getUrlOptionsForProduct($this->_product->getId());
		if (!empty($urls)) {
			return '?npsf=' . $urls[0]['npsf'] . '&chid=' . $urls[0]['chid'];
		} else {
			return null;
		}
	}
	public function _getStockStatus($product_id) {
		$inventory = $this->readConnection->fetchRow("SELECT `qty` FROM `cataloginventory_stock_status` WHERE `product_id` = " . $product_id);
		if (empty($inventory['qty'])) {
			return 0;
		}
		return substr($inventory['qty'], 0, stripos($inventory['qty'], "."));
	}
	public function _getChildrenProducts($product_id) {
		$return = array();
		$query = "SELECT DISTINCT * FROM `catalog_product_option` AS p INNER JOIN `catalog_product_option_type_value` AS o ON o.option_id = p.option_id INNER JOIN `catalog_product_entity` AS e ON e.sku = o.sku WHERE p.product_id = " . $product_id;
		$results = $this->readConnection->fetchAll($query);
		foreach ($results as $child) {
			//get inventory
			$stock = $this->_getStockStatus($child['entity_id']);
			$child['qty'] = $stock;

			//return children products
			$return[$child['entity_id']] = $child;
		}
		return $return;
	}
	public function _getImages($product_id) {
		$query = "SELECT `id`,`product_id`,`manu`,`file_name`,`order`, `type`, `title`, `in_gallery`, `default_img` FROM `nps_product_media_gallery` WHERE `product_id` = " . $product_id . " ORDER BY `order`";
		$this->readConnection->query($query);
		$results = $this->readConnection->fetchAll($query);
		return $results;
	}
	public function _getChildGalleryImages($product_id) {
		$images = array();
		//get all childs
		$children = $this->_getChildrenProducts($product_id);
		foreach ($children as $kid) {
			$kidImage = $this->_getImages($kid['entity_id']);
			foreach ($kidImage as $key => $row) {
				$images[] = $row;
			}
		}
		return $images;
	}
	public function _convertManuToFolder($manu) {
		return strtolower(str_replace(array(' ', '-', '_'), null, $manu));
	}
}
