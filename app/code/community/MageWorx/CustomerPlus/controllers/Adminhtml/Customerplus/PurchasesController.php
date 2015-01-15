<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@mageworx.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 * or send an email to sales@mageworx.com
 *
 * @category   MageWorx
 * @package    MageWorx_Adminhtml
 * @copyright  Copyright (c) 2009 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * MageWorx Adminhtml extension
 *
 * @category   MageWorx
 * @package    MageWorx_Adminhtml
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */

class MageWorx_CustomerPlus_Adminhtml_Customerplus_PurchasesController extends Mage_Adminhtml_Controller_Action
{
	protected function _initCustomer($idFieldName = 'id')
    {
        $customerId = (int) $this->getRequest()->getParam($idFieldName);
        $customer = Mage::getModel('customer/customer');

        if ($customerId) {
            $customer->load($customerId);
        }

        Mage::register('current_customer', $customer);
        return $this;
    }

    public function gridAction()
    {
    	$this->_initCustomer();
	    $this->loadLayout();
        $this->getResponse()->setBody($this->getLayout()->createBlock('mageworx_customerplus/adminhtml_customer_edit_tab_purchases')->toHtml());
    }

	public function exportCsvAction()
    {
        $date = Mage::helper('mageworx')->getDateForFilename();
        $fileName = 'customerplus_'.$date.'.csv';
        $content  = $this->getLayout()->createBlock('mageworx_customerplus/adminhtml_customer_edit_tab_purchases')
            ->setExport(true)
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content, 'application/octet-stream; charset="utf-8"');
    }

    public function exportXmlAction()
    {
        $date = Mage::helper('mageworx')->getDateForFilename();
        $fileName = 'customerplus_'.$date.'.xml';
        $content  = $this->getLayout()->createBlock('mageworx_customerplus/adminhtml_customer_edit_tab_purchases')
            ->setExport(true)
            ->getXml();

        $this->_prepareDownloadResponse($fileName, $content);
    }

	protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('customer/manage');
    }
}