<?php
/**
CUSTOM DROP PHP FUNCTIONS
*/
class productDrop {

	public function __construct(){
		$this->connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $this->tablePrefix = (string) Mage::getConfig()->getTablePrefix();

        // transfer relation
        //$select = $connection->select()->from($tablePrefix . 'catalog_product_option', array('option_id', 'in_group_id'))->where('product_id = XX AND in_group_id > 65535');
	}

	public function getLayerID($_filter){
		return $_filter->getAttributeModel()->getAttributeCode();
	}
}

?>