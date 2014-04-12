<?php

class Lis_Grid_Data_DbSelect extends Lis_Grid_Data_Database {

    public function attachSource($source) {
        if(!$source instanceof Zend_Db_Select) {
            throw new Lis_Grid_Data_Exception('Input parametr source, must be instanceof Zend_Db_Select');
        }
        $this->resetMemory();
        $this->_source = $source;
        return $this;
    }
    public function getRows() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $columnsForDisplay = $this->getColumnsForDisplay();
        $columns = count($columnsForDisplay) > 0 ? array_merge($this->getIndexColumns(), $columnsForDisplay): '*';
        $statement = $db->select()->from(new Zend_Db_Expr('(' . $this->_source .')'), $columns);
        foreach($this->getFilters() as $filter) {
            $statement->where($filter);
        }
        if(count($this->_columnSort) > 0){
            foreach ($this->_columnSort as $columnName => $orderType) {
                $order = $columnName . ' ' . $orderType;
                $statement->order($order);
            }
        }
        $statement->limitPage($this->getLowLimit(), $this->getHighLimit());
        $rows = $db->fetchAll($statement);
        return $rows;
    }
    
    public function getRowsCount($refresh = false){
        $db = Zend_Db_Table::getDefaultAdapter();
        if($this->_rowsCount === null || $refresh == true){
            $statement = $db->select()->from(new Zend_Db_Expr('(' . $this->_source . ')'), new Zend_Db_Expr('COUNT(*)'));
            foreach ($this->getFilters() as $condition) {
                $statement->where($condition);
            }
            $this->_rowsCount = $db->fetchOne($statement);
        }
        return $this->_rowsCount;
    }
}