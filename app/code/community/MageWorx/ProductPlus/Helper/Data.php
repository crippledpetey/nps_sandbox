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
 * @package    MageWorx_ProductPlus
 * @copyright  Copyright (c) 2009 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Product extension
 *
 * @category   MageWorx
 * @package    MageWorx_ProductPlus
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */

class MageWorx_ProductPlus_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getOrderOptions($itemObj)
    {
        $result = array();
        if ($options = $itemObj->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (!empty($options['attributes_info'])) {
                $result = array_merge($options['attributes_info'], $result);
            }
        }
        return $result;
    }

    public function getCustomizedOptionValue($optionInfo)
    {
        // render customized option view
        $_default = $optionInfo['value'];
        if (isset($optionInfo['option_type'])) {
            try {
                $group = Mage::getModel('catalog/product_option')->groupFactory($optionInfo['option_type']);
                return $group->getCustomizedView($optionInfo);
            } catch (Exception $e) {
                return $_default;
            }
        }
        return $_default;
    }

    public function getSku($itemObj)
    {
        if ($itemObj->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            return $itemObj->getProductOptionByCode('simple_sku');
        }
        return $itemObj->getSku();
    }

	public function displayPrices($_item, $separator = ',')
    {
    	$res = '';
    	$basePrice = $_item->getBaseRowTotal() - $_item->getBaseDiscountAmount() + $_item->getBaseTaxAmount() + $_item->getBaseWeeeTaxAppliedRowAmount();
        $price = $_item->getRowTotal() - $_item->getDiscountAmount() + $_item->getTaxAmount() + $_item->getWeeeTaxAppliedRowAmount();

        $order = Mage::getModel('sales/order')->load($_item->getOrderId());
        if ($order->isCurrencyDifferent()) {
            $res.= $order->getBaseCurrency()->format($basePrice, array(), false);
            $res.= $separator;
            $res.= $order->formatPriceTxt($price);
        } else {
            $res = $order->formatPriceTxt($price);
        }
        return $res;
    }
}