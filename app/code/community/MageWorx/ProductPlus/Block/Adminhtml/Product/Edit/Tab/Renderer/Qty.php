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

class MageWorx_Productplus_Block_Adminhtml_Product_Edit_Tab_Renderer_Qty extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
    	$block = Mage::app()->getLayout()
			->createBlock('core/template')
			->setTemplate('mageworx/productplus/purchases/grid-renderer-qty.phtml')
			->addData(array('item' => $row))
			->toHtml();

        return $block;
    }

	public function renderExport(Varien_Object $_item)
    {
    	$res       = '';
    	$prefix    = ',';
    	$separator = ':';

		$res .= Mage::helper('mageworx_productplus')->__('Ordered').$separator.$_item->getQtyOrdered()*1;

		if ((float) $_item->getQtyInvoiced()) {
	    	$res .= $prefix.Mage::helper('mageworx_productplus')->__('Invoiced').$separator.$_item->getQtyInvoiced()*1;
		}

	    if ((float) $_item->getQtyShipped()) {
	    	$res .= $prefix.Mage::helper('mageworx_productplus')->__('Shipped').$separator.$_item->getQtyShipped()*1;
	    }

	    if ((float) $_item->getQtyRefunded()) {
		    $res .= $prefix.Mage::helper('mageworx_productplus')->__('Refunded').$separator.$_item->getQtyRefunded()*1;
	    }

	    if ((float) $_item->getQtyCanceled()) {
	    	$res .= $prefix.Mage::helper('mageworx_productplus')->__('Canceled').$separator.$_item->getQtyCanceled()*1;
	    }
	    return $res;
    }
}