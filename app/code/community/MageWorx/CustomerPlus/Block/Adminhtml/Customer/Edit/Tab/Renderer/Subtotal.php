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

class MageWorx_CustomerPlus_Block_Adminhtml_Customer_Edit_Tab_Renderer_Subtotal extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
		$block = Mage::app()->getLayout()
			->createBlock('mageworx_customerplus/adminhtml_sales_items_default')
			->setTemplate('mageworx/customerplus/purchases/grid-renderer-subtotal.phtml')
			->addData(array('item' => $row))
			->setPriceDataObject($row)
			->toHtml();

        return $block;
    }

	public function renderExport(Varien_Object $_item)
    {
    	$res       = '';
    	$prefix    = ',';
    	$separator = ':';
    	$helper    = Mage::helper('mageworx_customerplus');

		if ($this->helper('tax')->displayCartBothPrices() || $this->helper('tax')->displayCartPriceExclTax()) {

			if ($this->helper('tax')->displayCartBothPrices()) {
	             $res .= $this->__('Excl. Tax').$separator;
			}

			if (Mage::helper('weee')->typeOfDisplay($_item, array(0, 1, 4), 'sales')) {
			    $res .= $helper->displayPrices(
			        $_item->getBaseRowTotal() + $_item->getBaseWeeeTaxAppliedRowAmount() + $_item->getBaseWeeeTaxRowDisposition(),
			        $_item->getRowTotal() + $_item->getWeeeTaxAppliedRowAmount() + $_item->getWeeeTaxRowDisposition(),
			        $_item->getOrderId()
			    );
			} else {
			    $res .= $helper->displayPrices($_item->getBaseRowTotal(), $_item->getRowTotal(), $_item->getOrderId());
			}

			if (Mage::helper('weee')->getApplied($_item)) {
				if (Mage::helper('weee')->typeOfDisplay($_item, 1, 'sales')) {
					foreach (Mage::helper('weee')->getApplied($_item) as $tax) {
						$res .= $prefix.$tax['title'].$separator.$helper->displayPrices($tax['base_row_amount'], $tax['row_amount']);
         			}
				} elseif (Mage::helper('weee')->typeOfDisplay($_item, 2, 'sales')) {
					foreach (Mage::helper('weee')->getApplied($_item) as $tax) {
                     	$res .= $prefix.$tax['title'].$separator.$helper->displayPrices($tax['base_row_amount_incl_tax'], $tax['row_amount_incl_tax']);
					}
				} elseif (Mage::helper('weee')->typeOfDisplay($_item, 4, 'sales')) {
					foreach (Mage::helper('weee')->getApplied($_item) as $tax) {
	                     $res .= $prefix.$tax['title'].$separator.$helper->displayPrices($tax['base_row_amount_incl_tax'], $tax['row_amount_incl_tax']);
					}
				}

				if (Mage::helper('weee')->typeOfDisplay($_item, 2, 'sales')) {
					if ($res) { $res .= $prefix; }
					$res .= Mage::helper('weee')->__('Total').$separator;
                 	$res .= $helper->displayPrices(
						$_item->getBaseRowTotal() + $_item->getBaseWeeeTaxAppliedRowAmount() + $_item->getBaseWeeeTaxRowDisposition(),
						$_item->getRowTotal() + $_item->getWeeeTaxAppliedRowAmount() + $_item->getWeeeTaxRowDisposition(),
						$_item->getOrderId()
					);
				}
			}
		}

		if ($this->helper('tax')->displayCartBothPrices() || $this->helper('tax')->displayCartPriceInclTax()) {

			if ($this->helper('tax')->displayCartBothPrices()) {
				if ($res) { $res .= $prefix; }
				$res .= $this->__('Incl. Tax').$separator;
			}
			$_incl = $this->helper('checkout')->getSubtotalInclTax($_item);
			$_baseIncl = $this->helper('checkout')->getBaseSubtotalInclTax($_item);

			if (Mage::helper('weee')->typeOfDisplay($_item, array(0, 1, 4), 'sales')) {
				$res .= $helper->displayPrices($_baseIncl + $_item->getBaseWeeeTaxAppliedRowAmount(), $_incl + $_item->getWeeeTaxAppliedRowAmount(), $_item->getOrderId());
			} else {
				$res .= $helper->displayPrices($_baseIncl - $_item->getBaseWeeeTaxRowDisposition(), $_incl - $_item->getWeeeTaxRowDisposition(), $_item->getOrderId());
			}

			if (Mage::helper('weee')->getApplied($_item)) {
				if (Mage::helper('weee')->typeOfDisplay($_item, 1, 'sales')) {
					foreach (Mage::helper('weee')->getApplied($_item) as $tax) {
                    	$res .= $prefix.$tax['title'].$separator.$helper->displayPrices($tax['base_row_amount'], $tax['row_amount']);
					}
				} elseif (Mage::helper('weee')->typeOfDisplay($_item, 2, 'sales')) {
					foreach (Mage::helper('weee')->getApplied($_item) as $tax) {
						$res .= $prefix.$tax['title'].$separator.$helper->displayPrices($tax['base_row_amount_incl_tax'], $tax['row_amount_incl_tax']);
					}
				} elseif (Mage::helper('weee')->typeOfDisplay($_item, 4, 'sales')) {
					foreach (Mage::helper('weee')->getApplied($_item) as $tax) {
						$res .= $prefix.$tax['title'].$separator.$helper->displayPrices($tax['base_row_amount_incl_tax'], $tax['row_amount_incl_tax']);
					}
				}

				if (Mage::helper('weee')->typeOfDisplay($_item, 2, 'sales')) {
					if ($res) { $res .= $prefix; }
                 	$res .= Mage::helper('weee')->__('Total').$separator;
                 	$res .= $helper->displayPrices(
                 		$_baseIncl + $_item->getBaseWeeeTaxAppliedRowAmount(),
                 		$_incl + $_item->getWeeeTaxAppliedRowAmount(),
                 		$_item->getOrderId()
                 	);
				}
			}
		}
	    return $res;
    }
}