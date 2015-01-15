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
 * @copyright  Copyright (c) 2012 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * MageWorx Adminhtml extension
 *
 * @category   MageWorx
 * @package    MageWorx_Adminhtml
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */

class MageWorx_ProductPlus_Block_Adminhtml_Product_Edit_Tab_Purchases
	extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
	protected $_product;

	public function __construct()
    {
        parent::__construct();
        $this->setId('productPlusPurchasesGrid');
        $this->setDefaultSort('sales_order_created_at', Varien_Data_Collection::SORT_ORDER_ASC);
        $this->setUseAjax(true);

        $this->getProduct();
    }

	public function getProduct()
	{
	    if (!$this->_product) {
		    $productId = (int) $this->getRequest()->getParam('id');
	        $product = Mage::getModel('catalog/product');

	        if ($productId) {
	            $this->_product = $product->load($productId);
	        } else {
	        	$this->_product = new Varien_Object();
	        }
	    }
	    return $this->_product;
	}

    public function getGridUrl()
    {
        return $this->getUrl('mageworx/productplus_purchases/grid', array('_current' => true));
    }

	protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('mageworx_productplus/purchases_collection')
	        ->setProductFilter($this->_product->getId())
	        ->setParentItemIdFilter();
	
		$coreResource = Mage::getSingleton('core/resource');
		
	    $collection->getSelect()
	    	->joinLeft(array('shipping_address' => $coreResource->getTableName('sales_flat_order_address')),
	    				'`sales_order`.`shipping_address_id` = `shipping_address`.`entity_id`',
	    				array('shipping_street'		=>	'shipping_address.street',
	    					'shipping_city'			=>	'shipping_address.city',
	    					'shipping_state'		=>	'shipping_address.region',
	    					'shipping_zip'			=>	'shipping_address.postcode'
	    				))
	    	->joinLeft(array('billing_address' => $coreResource->getTableName('sales_flat_order_address')),
	    				'`sales_order`.`billing_address_id` = `billing_address`.`entity_id`',
	    				array('customer_firstname'	=>	'billing_address.firstname', 
	    					'customer_lastname'		=>	'billing_address.lastname',
	    					'customer_email'		=>	'billing_address.email',
							'billing_street'		=>	'billing_address.street',
	    					'billing_city'			=>	'billing_address.city',
	    					'billing_state'		=>	'billing_address.region',
	    					'billing_zip'			=>	'billing_address.postcode'
	    				));
	            
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _getHelper()
    {
    	return Mage::helper('mageworx_productplus');
    }

    protected function _prepareColumns()
    {
    	$helper = $this->_getHelper();
		$this->addColumn('increment_id', array(
            'header'    => $helper->__('Order #'),
            'width'     => 100,
            'index'     => 'increment_id',
        ));

		$this->addColumn('customer_firstname', array(
            'header'    => $helper->__('Customer First Name'),
            'index'     => 'customer_firstname',
        ));

		$this->addColumn('customer_lastname', array(
            'header'    => $helper->__('Customer Last Name'),
            'index'     => 'customer_lastname',
        ));

		$this->addColumn('customer_email', array(
            'header'    => $helper->__('Customer Email'),
            'index'     => 'customer_email',
			'renderer'  => 'mageworx_productplus/adminhtml_product_edit_tab_renderer_email',
        ));

		$this->addColumn('customer_shippingaddress', array(
            'header'    => $helper->__('Shipping Address'),
            'index'     => 'shipping_street',
        	'filter'    => false,
            'sortable'  => false,
        ));

		$this->addColumn('customer_shippingcity', array(
            'header'    => $helper->__('Shipping City'),
            'index'     => 'shipping_city',
        	'filter'    => false,
            'sortable'  => false,
        ));

        $this->addColumn('customer_shippingstate', array(
            'header'    => $helper->__('Shipping State'),
            'index'     => 'shipping_state',
        	'filter'    => false,
            'sortable'  => false,
        ));

        $this->addColumn('customer_shippingzip', array(
            'header'    => $helper->__('Shipping Zip'),
            'index'     => 'shipping_zip',
            'filter'    => false,
            'sortable'  => false,
        ));

        $this->addColumn('customer_billingaddress', array(
            'header'    => $helper->__('Billing Address'),
            'index'     => 'billing_street',
            'filter'    => false,
            'sortable'  => false,
        ));

        $this->addColumn('customer_billingcity', array(
            'header'    => $helper->__('Billing City'),
            'index'     => 'billing_city',
            'filter'    => false,
            'sortable'  => false,
        ));

        $this->addColumn('customer_billingstate', array(
            'header'    => $helper->__('Billing State'),
            'index'     => 'billing_state',
            'filter'    => false,
            'sortable'  => false,
        ));

        $this->addColumn('customer_billingzip', array(
            'header'    => $helper->__('Billing Zip'),
            'index'     => 'billing_zip',
            'filter'    => false,
            'sortable'  => false,
        ));

        $this->addColumn('sales_order_created_at', array(
            'header'    => $helper->__('Order Date'),
            'index'     => 'sales_order_created_at',
            'filter_index' => 'sales_order.created_at',
            'type'      => 'datetime',
        ));

        $this->addColumn('base_row_total', array(
            'header'    => $helper->__('Price'),
            'index'     => 'base_row_total',
        	'align'     => 'right',
			'renderer'  => 'mageworx_productplus/adminhtml_product_edit_tab_renderer_rowtotal',
            'filter'    => false,
            'sortable'  => false,
        ));

        $this->addColumn('qty_ordered', array(
            'header'    => $helper->__('Quantity'),
            'index'     => 'qty_ordered',
            'width'     => 100,
            'align'     => 'right',
            'renderer'  => 'mageworx_productplus/adminhtml_product_edit_tab_renderer_qty',
            'filter'    => false,
            'sortable'  => false,
        ));

        $this->addColumn('custom_options', array(
            'header'    => $helper->__('Custom Options'),
            'index'     => 'product_options',
            'renderer'  => 'mageworx_productplus/adminhtml_product_edit_tab_renderer_customoptions',
            'filter'    => false,
            'sortable'  => false,
        ));

    	if (Mage::getSingleton('admin/session')->isAllowed('customer/manage')) {
            $this->addColumn('action',
                array(
                    'header'    => $helper->__('Action'),
                	'index'     => 'stores',
                    'width'     => 100,
					'renderer'  => 'mageworx_productplus/adminhtml_product_edit_tab_renderer_view',
                    'sortable'  => false,
                    'filter'    => false,
                    'is_system' => true,
            ));
        }
        $this->addExportType('mageworx/productplus_purchases/exportCsv', $helper->__('CSV'));
        $this->addExportType('mageworx/productplus_purchases/exportXml', $helper->__('XML'));

        return parent::_prepareColumns();
    }

	public function getXml()
    {
        $this->_isExport = true;
        $this->_prepareGrid();
        $this->getCollection()->getSelect()->limit();
        $this->getCollection()->setPageSize(0);
        $this->getCollection()->load();
        $this->_afterLoadCollection();

		$n   = "\n";
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'.$n;
        $xml.= '<items>'.$n;
        foreach ($this->getCollection() as $item) {
            $data = array();
            foreach ($this->_columns as $key => $column) {
                if (!$column->getIsSystem()) {
                   $data[$key] = str_replace(array('"', '\\'), array('""', '\\\\'), $column->getRowFieldExport($item));
                }
            }
            $xml.= $this->_toXml($data);
        }
        $xml.= '</items>'.$n;
        return $xml;
    }

	private function _toXml(array $arrAttributes = array(), $rootName = 'item')
    {
    	$n   = "\n";
        $xml = '';
        $xml.= '<'.$rootName.'>'.$n;
        foreach ($arrAttributes as $fieldName => $fieldValue) {
            $fieldValue = "<![CDATA[{$fieldValue}]]>";
            $xml.= "<{$fieldName}>{$fieldValue}</{$fieldName}>".$n;
        }
        $xml.= '</'.$rootName.'>'.$n;
        return $xml;
    }

    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return $this->_getHelper()->__('Orders by Customers');
    }

    public function getTabTitle()
    {
        return $this->_getHelper()->__('Orders by Customers');
    }

    public function getAfter()
    {
        return 'customer_options';
    }

    public function canShowTab()
    {
        if (Mage::registry('product')->getId()) {
            return true;
        }
        return false;
    }

    public function isHidden()
    {
        if (Mage::registry('product')->getId()) {
            return false;
        }
        return true;
    }
}