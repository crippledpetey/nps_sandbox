<?php

class NPS_CustomAdminFunctions_IndexController extends Mage_Core_Controller_Front_Action {
	public function indexAction() {
/*
 * Initialization of Mage_Core_Model_Layout model
 */
		$this->loadLayout();

/*
 * Building page according to layout confuration
 */
		$this->renderLayout();
	}

}

?>