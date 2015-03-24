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
 * NPS Media Manager
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     NPS - Brandon Thomas <brandon@needplumbingsupplies.com>
 */

class NPS_CustomAdminFunctions_Block_Adminhtml_Tabs_Mediamanager extends Mage_Adminhtml_Block_Template {

	public function __construct() {
		parent::_construct();
		$this->setTemplate('catalog/product/tab/mediamanager.phtml');

		$this->_submissionHandler();

		//sql connector
		$this->resource = Mage::getSingleton('core/resource');
		$this->readConnection = $this->resource->getConnection('core_read');
		$this->writeConnection = $this->resource->getConnection('core_write');
	}
	private function _submissionHandler() {
		$refresh = false;
		if (!empty($_POST['nps_function'])) {
			if ($_POST['nps_function'] == 'nps-media-manager-upload' && !empty($_FILES)) {

				/**
				MAY NEED TO USE THE VARIEN OBJECT TO CREATE THE UPLOAD FORM AND MANIPULATE THE FILES. i HAVE A FEELING THAT THE FILE IS STILL IN USE AND SO MAGE IS GETTING AN ERROR WHEN IT TRIES TO ACCESS IT WHILE IT'S PERFORMING THE BACKEND FUNCTIONS
				 */
				$this->_uploadImageHandler();
				//$refresh = true;
			}
		}

		//if refresh is true then reload the page to prevent duplicate posting
		if ($refresh) {
			session_write_close();
			Mage::app()->getFrontController()->getResponse()->setRedirect($_SERVER['REQUEST_URI']);
		}
	}
	private function _uploadImageHandler() {
		//default return value
		$return = false;

		//vefiy file is an image outputs array(width, height, version, size string, bits, mime type)
		$check = getimagesize($_FILES["nps-media-manager-upload-input"]["tmp_name"]);

		//if is image
		if ($check) {

			//set file extension if is jpeg or png
			$ext = null;
			if ($check['mime'] == 'image/png') {
				$ext = '.png';
			} elseif ($check['mime'] == 'image/jpeg') {
				$ext = '.jpeg';
			} elseif ($check['mime'] == 'image/jpg') {
				$ext = '.jpg';
			}

			//if file extension is set
			if (!empty($ext)) {

				//manufacturer folder
				$manu_folder = strtolower(str_replace(array(' ', '-', '_'), null, $_POST['nps-media-gallery-product-manu']));

				//set the new image name
				$new_image_name = strtolower(str_replace(array(' ', '_', '#', '&', '(', ')'), array('-', '-', '-', '-', null, null), $_POST['nps-media-gallery-product-sku']) . '-' . $_POST['nps-media-gallery-start-count'] . $ext);

				//set the new image path to the temp folder
				$new_image_path = '/home/image_staging/' . $manu_folder . '/';

				//set root image
				$root_img = $_FILES["nps-media-manager-upload-input"]["tmp_name"];

				//move the image to the temp directory
				$move = move_uploaded_file($root_img, $new_image_path . $new_image_name);
				//$this->_imageLog('Move File Output');

				$ouput = shell_exec("/scripts/product_image_to_imagebase.sh " . $new_image_name . " " . $manu_folder . " 2>&1");

				//output records to the image script
				/*
				$this->_imageLog($_POST['nps-media-gallery-product-manu'] . ' ' . $_POST['nps-media-gallery-product-sku'] . ' - Shell Output' . "\n");
				$this->_imageLog($ouput);
				$this->_imageLog("\n\n");
				 */

				//insert the record into the db

				$this->_addImageGalleryImage(
					$_POST['nps-media-gallery-product-id'],
					$new_image_name,
					$_POST['nps-media-manager-image-order'],
					$_POST['nps-media-gallery-image-type']
				);

			}
		}
		return $return;
	}
	public function getProduct() {
		return Mage::registry('product');
	}
	public function isNew() {
		if ($this->getProduct()->getId()) {
			return false;
		}
		return true;
	}
	public function _getImages($product_id) {
		$query = "SELECT `id`,`product_id`,`file_name`,`order`, `type` FROM `nps_product_media_gallery` WHERE `product_id` = " . $product_id . " ORDER BY `order` ";

		$this->readConnection->query($query);
		$results = $this->readConnection->fetchAll($query);
		return $results;
	}
	public function _addImageGalleryImage($product_id, $file, $order, $type) {
		/*
		$query = "INSERT INTO `nps_product_media_gallery` (`product_id`,`file_name`,`order`,`type`) VALUES (" . $product_id . ",'" . $file . "'," . $order . ",'" . $type . "')";
		$this->writeConnection->query($query);
		 */
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$connection->beginTransaction();
		$__fields = array();
		$__fields['product_id'] = $product_id;
		$__fields['file_name'] = $file;
		$__fields['order'] = $order;
		$__fields['type'] = $type;
		$connection->insert('nps_product_media_gallery', $__fields);
		$connection->commit();
	}
	private function _imageLog($data) {

		//check file size is larger than 1mb and create a new one if so
		if (filesize(Mage::getBaseDir() . DIRECTORY_SEPARATOR . "image_upload.txt") > 1024) {
			rename(Mage::getBaseDir() . DIRECTORY_SEPARATOR . "image_upload.txt", Mage::getBaseDir() . DIRECTORY_SEPARATOR . "image_upload." . date('U') . ".txt");
		}
		//start output buffer
		ob_start();
		if (is_array($data)) {
			var_dump($data);
		} else {
			echo $data;
		}
		$output = ob_get_clean();
		$fileHandle = fopen(Mage::getBaseDir() . DIRECTORY_SEPARATOR . "image_upload.txt", "a+");
		fwrite($fileHandle, $output);
		fclose($fileHandle);
	}
}
if (!function_exists('outputToTestingText')) {
	function outputToTestingText($data, $continue = false) {

		ob_start();
		var_dump($data);
		$output = ob_get_clean();
		if ($continue) {
			$fileHandle = fopen(Mage::getBaseDir() . DIRECTORY_SEPARATOR . "testing.txt", "a+");
		} else {
			$fileHandle = fopen(Mage::getBaseDir() . DIRECTORY_SEPARATOR . "testing.txt", "w+");
		}

		fwrite($fileHandle, $output);
		fclose($fileHandle);
	}
}
