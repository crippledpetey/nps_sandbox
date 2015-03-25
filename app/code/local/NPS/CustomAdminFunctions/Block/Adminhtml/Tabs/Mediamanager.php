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

	/**
	CORE FUNCTIONS
	 */
	public function __construct() {
		parent::_construct();
		$this->setTemplate('catalog/product/tab/mediamanager.phtml');
		//sql connector
		$this->resource = Mage::getSingleton('core/resource');
		$this->readConnection = $this->resource->getConnection('core_read');
		$this->writeConnection = $this->resource->getConnection('core_write');
		//run submissiont handler
		$this->_submissionHandler();
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
	private function _submissionHandler() {
		$refresh = false;
		if (!empty($_POST['nps_function'])) {
			if ($_POST['nps_function'] == 'nps-media-manager-upload' && !empty($_FILES)) {
				$this->_uploadImageHandler();
				$refresh = true;
			} elseif ($_POST['nps_function'] == 'nps-remove-gallery-image') {
				$this->_removeImageGalleryImage($_POST['nps-remove-image']);
				$refresh = true;
			}
		}
		//if refresh is true then reload the page to prevent duplicate posting
		if ($refresh) {
			session_write_close();
			Mage::app()->getFrontController()->getResponse()->setRedirect($_SERVER['REQUEST_URI']);
		}
	}
	/**
	SUBMISSION HANDLER CHILD FUNCTIONS
	 */
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
			} elseif ($check['mime'] == 'image/gif') {
				$ext = '.gif';
			}
			//if file extension is set
			if (!empty($ext)) {
				//manufacturer folder
				$manu_folder = $this->convertManuToFolder($_POST['nps-media-gallery-product-manu']);
				//set the new image name
				$new_image_name = strtolower(str_replace(array(' ', '_', '#', '&', '(', ')'), array('-', '-', '-', '-', null, null), $_POST['nps-media-gallery-product-sku']) . '-' . $_POST['nps-media-gallery-start-count'] . $ext);
				//set the new image path to the temp folder
				$new_image_path = '/home/image_staging/' . $manu_folder . '/';
				//set root image
				$root_img = $_FILES["nps-media-manager-upload-input"]["tmp_name"];
				//move the image to the temp directory
				$move = move_uploaded_file($root_img, $new_image_path . $new_image_name);
				//run script
				$ouput = shell_exec("/scripts/product_image_to_imagebase.sh " . $new_image_name . " " . $manu_folder . " 2>&1");
				//insert the record into the db as JPEG
				$this->_addImageGalleryImage(
					$_POST['nps-media-gallery-product-id'],
					$this->convertFileNameToJPEG($new_image_name),
					$_POST['nps-media-manager-image-order'],
					$_POST['nps-media-gallery-image-type']
				);

			}
		}
		return $return;
	}
	private function _removeImageGalleryImage($image_id) {
		$DS = DIRECTORY_SEPARATOR;
		//get image info
		$img = $this->_getImage($image_id);
		//create the removal file
		$remove_file = Mage::getBaseDir() . $DS . 'var' . $DS . 'img_tmp' . $DS . $image_id . '.txt';
		$fileHandle = fopen($remove_file, "w+");
		//set raw delete
		$raw_delete = 'rm -f /home/img_usr/catalog/product/' . $img['manu'];
		//size folders
		$size_folders = array('65x65', '75x75', '80x80', '100x100', '185x185', '200x200', '250x250', '300x300', 'x1200', '1800x', 'full');
		//set base output
		$output = array();
		foreach ($size_folders as $size) {
			$output[] = $raw_delete . "/" . $size . "/" . $img['file_name'] . "\n";
		}
		//if there is content for the file write it
		if (!empty($output)) {
			$output = implode("\n", $output);
			fwrite($fileHandle, $output);
		}
		//close the file connection
		fclose($fileHandle);
		//run shell command to send file to image base
		shell_exec("/scripts/remove_images_from_imagebase.sh " . $image_id . ".txt 2>&1");
		//sttempt to remove the file (protects from running on local)
		if (unlink($remove_file)) {
			//remove database record
			$this->_removeDBImage($image_id);
		}
	}
	/**
	LOGGING FUNCTIONS
	 */
	private function _imageLog($data) {
		$DS = DIRECTORY_SEPARATOR;
		//check file size is larger than 1mb and create a new one if so
		if (filesize(Mage::getBaseDir() . $DS . "image_upload.txt") > 1024) {
			rename(Mage::getBaseDir() . $DS . "image_upload.txt", Mage::getBaseDir() . $DS . "image_upload." . date('U') . ".txt");
		}
		//start output buffer
		ob_start();
		if (is_array($data)) {
			var_dump($data);
		} else {
			echo $data;
		}
		$output = ob_get_clean();
		$fileHandle = fopen(Mage::getBaseDir() . $DS . "image_upload.txt", "a+");
		fwrite($fileHandle, $output);
		fclose($fileHandle);
	}
	/**
	STRING FUNCTIONS
	 */
	private function convertFileNameToJPEG($filename) {
		return substr($filename, 0, strripos($filename, '.')) . '.jpeg';
	}
	public function convertManuToFolder($manu) {
		return strtolower(str_replace(array(' ', '-', '_'), null, $manu));
	}
	/**
	DATABASE FUNCTIONS
	 */
	public function _removeDBImage($image_id) {
		$query = "DELETE FROM `nps_product_media_gallery` WHERE `id` = " . $image_id;
		$this->readConnection->query($query);
	}
	public function _getImage($image_id) {
		$query = "SELECT `id`,`product_id`,`manu`,`file_name`,`order`, `type` FROM `nps_product_media_gallery` WHERE `id` = " . $image_id;
		$this->readConnection->query($query);
		$results = $this->readConnection->fetchRow($query);
		return $results;
	}
	public function _getImages($product_id) {
		$query = "SELECT `id`,`product_id`,`manu`,`file_name`,`order`, `type` FROM `nps_product_media_gallery` WHERE `product_id` = " . $product_id . " ORDER BY `order`";
		$this->readConnection->query($query);
		$results = $this->readConnection->fetchAll($query);
		return $results;
	}
	public function _addImageGalleryImage($product_id, $file, $order, $type) {
		$manu_folder = $this->convertManuToFolder($_POST['nps-media-gallery-product-manu']);
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$connection->beginTransaction();
		$__fields = array();
		$__fields['product_id'] = $product_id;
		$__fields['file_name'] = $file;
		$__fields['order'] = $order;
		$__fields['type'] = $type;
		$__fields['manu'] = $manu_folder;
		$connection->insert('nps_product_media_gallery', $__fields);
		$connection->commit();
	}
}
if (!function_exists('outputToTestingText')) {
	function outputToTestingText($data, $continue = false) {
		$DS = DIRECTORY_SEPARATOR;
		ob_start();
		var_dump($data);
		$output = ob_get_clean();
		if ($continue) {
			$fileHandle = fopen(Mage::getBaseDir() . $DS . "testing.txt", "a+");
		} else {
			$fileHandle = fopen(Mage::getBaseDir() . $DS . "testing.txt", "w+");
		}

		fwrite($fileHandle, $output);
		fclose($fileHandle);
	}
}
