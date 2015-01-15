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

class MageWorx_CustomerPlus_Block_Adminhtml_Customer_Edit_Tab_Renderer_Name extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
    	$block = Mage::app()->getLayout()
			->createBlock('core/template')
			->setTemplate('mageworx/customerplus/purchases/grid-renderer-name.phtml')
			->addData(array('item' => $row))
			->toHtml();

        return $block;
    }

	public function renderExport(Varien_Object $_item)
    {
	    $res       = '';
    	$prefix    = ',';
    	$separator = ':';
    	$helper    = Mage::helper('mageworx_customerplus');

	    $res .= $_item->getName();
    	$res .= $prefix.$this->helper('mageworx_customerplus')->__('SKU').$separator.implode($prefix, Mage::helper('catalog')->splitSku($helper->getSku($_item)));

		$_orderOptions = $helper->getOrderOptions($_item);
	    if ($_orderOptions) {
		        foreach ($_orderOptions as $_option) {
	            $res .= $prefix.$_option['label'].$separator;
	            if (isset($_option['custom_view']) && $_option['custom_view']) {
	                $res .= $helper->getCustomizedOptionValue($_option);
	            } else {
	                $res .= Mage::helper('core/string')->truncate($_option['value'], 55, '');
	    		}
	    	}
	    }
		return $res;
    }
}