<?php

class RealEstate_ColumnModel_Helper_DbTableColumns implements RealEstate_ColumnModel_Helper_ColumnsList{
    private $_dbAdapter;

    public function setDbAdapter(Zend_Db_Adapter_Abstract $adapter) {
        $this->_dbAdapter = $adapter;
        return $this;
    }

    public function getDbAdapter(){
        if($this->_dbAdapter !== null) {
            return $this->_dbAdapter;
        }
        return Zend_Db_Table::getDefaultAdapter();
    }

    public function getColumnsList($dataSource) {
        $dataSource = (string) $dataSource;
        if(empty($dataSource)) {
            throw new Zend_Exception('Empty table name');
        }
        $db = $this->getDbAdapter();
        $tablesList = $db->listTables();
        if(!in_array($dataSource, $tablesList)) {
            throw new Zend_Exception('Table "' . $dataSource . '" is not exist');
        }
        $columns = $db->describeTable($dataSource);
        return array_keys($columns);
    }
}