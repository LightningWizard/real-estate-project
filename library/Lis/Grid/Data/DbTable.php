<?php

class Lis_Grid_Data_DbTable extends Lis_Grid_Data_Database {

    protected $_defaultIndexColumns;

    public function attachSource($source) {
        if (!($source instanceof Zend_Db_Table_Abstract)) {
            throw new Lis_Grid_Data_Exception('Input parametr source, must be instanceof Zend_Db_Table_Abstract');
        }
        $this->resetMemory();
        $this->_source = $source;
        return $this;
    }

    public function getRows() {
        $statement = $this->getQuery();
        $rows = $this->_source->getAdapter()->fetchAll($statement);
        return $rows;
    }

    public function getQuery($forPage = true){
        $tableName = $this->_getTableName();
        $columnsForDisplay = $this->getColumnsForDisplay();
        $columns = count($columnsForDisplay) > 0 ? array_merge($this->getIndexColumns(), $columnsForDisplay): '*';
        $filterForm = $this->_columnModel->getFilterForm();
        if($filterForm !== null) {
            $filters = $filterForm->getFilters();
            foreach ($filters as $filter) {
                $this->_filters[] = $filter;
            }
        }
        $conditions = $this->getConditions();
        $statement = $this->_source->select()->from($tableName, $columns);
        foreach ($conditions as $condition) {
            $statement->where($condition);
        }
        if(count($this->_columnSort) > 0){
            foreach ($this->_columnSort as $columnName => $orderType) {
                $order = $columnName . ' ' . $orderType;
                $statement->order($order);
            }
        }
        if($forPage) {
            $statement->limitPage($this->getCurrentPage(), $this->getRowsPerPage());
        }
        return $statement;
    }

    public function getRowsCount($refresh = false) {
        if ($this->_rowsCount === null || $refresh == true) {
            $conditions = $this->getConditions();
            $tableName = $this->_getTableName();
            $statement = $this->_source->select()->from($tableName, new Zend_Db_Expr('COUNT(*)'));
            $filterForm = $this->_columnModel->getFilterForm();
            if ($filterForm !== null) {
                $filterObjects = $filterForm->getFilters();
                foreach ($filterObjects as $filter) {
                    $this->_filters[] = $filter;
                }
            }
            foreach ($conditions as $condition) {
                $statement->where($condition);
            }
            $this->_rowsCount = $this->_source->getAdapter()->fetchOne($statement);
        }
        return $this->_rowsCount;
    }

    public function getIndexColumns() {
        $indexColumns = parent::getIndexColumns();
        if(empty($indexColumns)) {
            return $this->_getDefaultIndexColumns();
        }
        return $indexColumns;
    }

    protected function _getTableName() {
        return $this->_source->info('name');
    }

    protected function _getDefaultIndexColumns() {
        if($this->_defaultIndexColumns === null) {
           $this->_defaultIndexColumns = $this->_source->info('primary');
        }
        return $this->_defaultIndexColumns;
    }

}
