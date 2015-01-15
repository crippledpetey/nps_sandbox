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

class MageWorx_CustomerPlus_Block_Adminhtml_Customer_Edit_Tab_Purchases
	extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
	protected $_customer;

	public function __construct()
    {
        parent::__construct();
        $this->setId('customerPlusPurchasesGrid');
        $this->setDefaultSort('order_created_at', Varien_Data_Collection::SORT_ORDER_DESC);
        $this->setUseAjax(true);

        $this->getCustomer();
    }

	public function getCustomer()
	{
		if (!$this->_customer) {
			$customerId = (int) $this->getRequest()->getParam('id');
	        $customer = Mage::getModel('customer/customer');

			if ($customerId) {
	            $this->_customer = $customer->load($customerId);
	        } else {
	        	$this->_customer = new Varien_Object();
	        }
	    }
	    return $this->_customer;
	}

    public function getGridUrl()
    {
        return $this->getUrl('mageworx/customerplus_purchases/grid', array('_current' => true));
    }

	protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('mageworx_customerplus/purchases_collection')
	        ->setCustomerFilter($this->_customer->getEntityId())
	        ->setParentItemIdFilter();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _getHelper()
    {
        return Mage::helper('mageworx_customerplus');
    }

    protected function _prepareColumns()
    {
    	$helper = $this->_getHelper();
    	$this->addColumn('increment_id', array(
            'header'    => $helper->__('Order #'),
            'width'     => 100,
            'index'     => 'increment_id',
        ));

        $this->addColumn('order_created_at', array(
            'header'    => $helper->__('Order Date'),
            'index'     => 'order_created_at',
            'filter_index' => 'sales_order.created_at',
            'type'      => 'datetime',
        ));

        $this->addColumn('name', array(
            'header'    => $helper->__('Product Name'),
            'index'     => 'name',
        	'renderer'  => 'mageworx_customerplus/adminhtml_customer_edit_tab_renderer_name',
        ));

        $this->addColumn('status', array(
            'header'    => $helper->__('Item Status'),
            'index'     => 'status',
        	'width'     => 80,
        	'align'     => 'center',
			'filter'    => false,
            'sortable'  => false,
        ));

        $this->addColumn('base_original_price', array(
            'header'    => $helper->__('Original Price'),
            'index'     => 'base_original_price',
			'align'     => 'right',
            'renderer'  => 'mageworx_customerplus/adminhtml_customer_edit_tab_renderer_originalprice',
        	'filter'    => false,
            'sortable'  => false,
        ));

        $this->addColumn('base_price', array(
            'header'    => $helper->__('Price'),
            'index'     => 'base_price',
			'align'     => 'right',
        	'renderer'  => 'mageworx_customerplus/adminhtml_customer_edit_tab_renderer_price',
        	'filter'    => false,
            'sortable'  => false,
        ));

        $this->addColumn('qty_ordered', array(
            'header'    => $helper->__('Qty'),
            'index'     => 'qty_ordered',
        	'width'     => 90,
        	'align'     => 'right',
        	'renderer'  => 'mageworx_customerplus/adminhtml_customer_edit_tab_renderer_qty',
        	'filter'    => false,
            'sortable'  => false,
        ));

        $this->addColumn('subtotal', array(
            'header'    => $helper->__('Subtotal'),
            'index'     => 'subtotal',
			'align'     => 'right',
        	'renderer'  => 'mageworx_customerplus/adminhtml_customer_edit_tab_renderer_subtotal',
        	'filter'    => false,
            'sortable'  => false,
        ));

        $this->addColumn('base_tax_amount', array(
            'header'    => $helper->__('Tax Amount'),
            'index'     => 'base_tax_amount',
        	'align'     => 'right',
			'renderer'  => 'mageworx_customerplus/adminhtml_customer_edit_tab_renderer_taxamount',
            'filter'    => false,
            'sortable'  => false,
        ));

        $this->addColumn('tax_percent', array(
            'header'    => $helper->__('Tax Percent'),
            'index'     => 'tax_percent',
			'align'     => 'right',
			'renderer'  => 'mageworx_customerplus/adminhtml_customer_edit_tab_renderer_taxpercent',
            'filter'    => false,
            'sortable'  => false,
        ));

        $this->addColumn('base_discount_amount', array(
            'header'    => $helper->__('Discount Amount'),
            'index'     => 'base_discount_amount',
        	'align'     => 'right',
			'renderer'  => 'mageworx_customerplus/adminhtml_customer_edit_tab_renderer_discountamount',
        	'filter'    => false,
            'sortable'  => false,
        ));

        $this->addColumn('base_row_total', array(
            'header'    => $helper->__('Row Total'),
            'index'     => 'base_row_total',
        	'align'     => 'right',
			'renderer'  => 'mageworx_customerplus/adminhtml_customer_edit_tab_renderer_rowtotal',
            'filter'    => false,
            'sortable'  => false,
        ));

    	if (!Mage::app()->isSingleStoreMode()) {
			$this->addColumn('store_id', array(
                'header'     => $helper->__('Bought From'),
                'index'      => 'store_id',
				'align'      => 'center',
                'type'       => 'store',
                'store_view' => true,
				'renderer'   => ($this->getExport() ? 'mageworx_customerplus/adminhtml_widget_grid_column_renderer_store_export' : ''),
            ));
        }

    	if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            $this->addColumn('action',
                array(
                    'header'    => $helper->__('Action'),
                	'index'     => 'stores',
                    'width'     => 70,
					'renderer'  => 'mageworx_customerplus/adminhtml_customer_edit_tab_renderer_vieworder',
                    'sortable'  => false,
                    'filter'    => false,
                    'is_system' => true,
            ));
        }
        $this->addExportType('mageworx/customerplus_purchases/exportCsv', $helper->__('CSV'));
		$this->addExportType('mageworx/customerplus_purchases/exportXml', $helper->__('XML'));

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
        return $this->_getHelper()->__('Products Ordered');
    }

    public function getTabTitle()
    {
        return $this->_getHelper()->__('Products Ordered');
    }

    public function getAfter()
    {
        return 'orders';
    }

    public function canShowTab()
    {
        if (Mage::registry('current_customer')->getId()) {
            return true;
        }
        return false;
    }

    public function isHidden()
    {
        if (Mage::registry('current_customer')->getId()) {
            return false;
        }
        return true;
    }
}