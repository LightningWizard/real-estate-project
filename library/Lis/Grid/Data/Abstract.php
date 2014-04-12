<?php

abstract class Lis_Grid_Data_Abstract extends Lis_Grid_Abstract {

    protected $_page = 1;
    protected $_rows = array();
    protected $_rowsPerPage = 25;
    protected $_source;
    protected $_filters = array();
    protected $_rowsCount = null;
    protected $_columnSort = array();
    protected $_userData = array();
    protected $_indexColumns = array();
    protected $_columnsForDisplay = array();
    protected $_colsReflectors = array();
    protected $_viewHelper = 'gridData';
    protected $_columnModel = null;

    const ORDER_TYPE_ASC = 'asc';
    const ORDER_TYPE_DESC = 'desc';

    abstract public function getRows();

    abstract public function attachSource($source);

    abstract protected function getRowsCount($refresh = false);

    public function __construct($source = null) {
        if ($source !== null) {
            $this->attachSource($source);
        }
    }

    public function setCurrentPage($page) {
        if ($page > 0) {
            $this->_page = (int) $page;
        }
        return $this;
    }

    public function getCurrentPage() {
        return $this->_page;
    }

    public function setRowsPerPage($rowsPerPage) {
        if ($rowsPerPage > 0) {
            $this->_rowsPerPage = (int) $rowsPerPage;
        }
        return $this;
    }

    public function getRowsPerPage() {
        return $this->_rowsPerPage;
    }

    public function getTotalPages() {
        $totalPages = ceil($this->getRowsCount() / $this->_rowsPerPage);
        return $totalPages > 0 ? $totalPages : 1;
    }

    public function getLowLimit() {
        return ($this->_page == 1) ? 0 : $this->_rowsPerPage * ($this->_page - 1);
    }

    public function getHighLimit() {
        return ($this->_page == 1) ? $this->_rowsPerPage : $this->_rowsPerPage + ($this->_page - 1) * $this->_rowsPerPage;
    }

    public function resetMemory() {
        $this->_indexColumns = array();
        $this->_columnsForDisplay = array();
        $this->_columnSort = array();
        $this->_colsReflectors = array();
        $this->_rows = array();
        $this->_filters = array();
        $this->_rowsCount = null;
        $this->_userData = array();
    }

    
    public function addFilter($filterColumn, $filterType, $filterValue) {
        $this->_filters[] = new Lis_Grid_Filter($filterColumn, $filterType, $filterValue);
        return $this;
    }

    public function setColumnSort($columnName, $orderType = null) {
        $this->_columnSort[$columnName] = ($orderType == self::ORDER_TYPE_DESC) ? self::ORDER_TYPE_DESC : self::ORDER_TYPE_ASC;
    }

    public function attachColumnModel(Lis_Grid_ColumnModel $columnModel) {
        if($this->_columnModel !== null) {
            $this->detachColumnModel();
        }
        if (!$columnModel->hasColumns()) {
            throw new Lis_Grid_Data_Exception('Invalid program logic. Try to set empty columnModel');
        }
        $this->_columnModel = $columnModel;
        $this->resetMemory();
        $columnsForDisplay = array();
        foreach ($columnModel->getColumns() as $column) {
            $columnName = $column->getOption('name');
            $reflector  = $column->getOption('reflector');
            $columnsForDisplay[] = $columnName;
            if($reflector !== null) {
                $this->attachColumnReflector($columnName, $reflector);
            }
        }
        $this->setColumnsForDisplay($columnsForDisplay);
        return $this;
    }
    
    public function detachColumnModel() {
        $this->_columnModel = null;
        return $this;
    }
    
    public function getColumnModel() {
        return $this->_columnModel;
    }

    public function setIndexColumns(array $indexColumns) {
        $this->_indexColumns = $indexColumns;
        return $this;
    }

    public function getIndexColumns() {
        return $this->_indexColumns;
    }

    public function setColumnsForDisplay(array $columnsForDisplay) {
        $this->_columnsForDisplay = $columnsForDisplay;
        return $this;
    }

    public function getColumnsForDisplay() {
        return $this->_columnsForDisplay;
    }

    public function setUserData(array $userData) {
        $this->_userData = $userData;
    }

    public function getUserData() {
        return $this->_userData;
    }

    public function attachColumnReflector($column, $reflector) {
        if (!is_string($reflector) && !$reflector instanceof Lis_Grid_Reflector_Interface) {
            throw new Lis_Grid_Data_Exception('Programming error. Invalid column reflector atachment.');
        }
        $this->_colsReflectors[$column] = $reflector;
        return $this;
    }

    public function getReflectedColumns() {
        return array_keys($this->_colsReflectors);
    }

    public function columnHasReflector($column) {
        return array_key_exists($column, $this->_colsReflectors);
    }

    public function reflectColumn($column, $value) {
        if ($this->columnHasReflector($column)) {
            $reflector = $this->_colsReflectors[$column];
            if (is_string($reflector)) {
                try {
                    include_once str_replace('_', DIRECTORY_SEPARATOR, $reflector) . '.php';
                    $reflector = new $reflector();
                    if (!$reflector instanceof Lis_Grid_Reflector_Interface) {
                        throw new Lis_Grid_Data_Exception('Programming error. Invalid column reflector class specified.');
                    }
                } catch (Exception $e) {
                    throw new Lis_Grid_Data_Exception('Programming error. Invalid column reflector atachment. ' . $e->getMessage());
                }
            }
            return $reflector->execute($value);
        } else {
            return $value;
        }
    }
    
    public function extractFromRequest(Zend_Controller_Request_Http $request) {
        if (($rowsPerPage = $request->getQuery('rows')) && $rowsPerPage != -1) {
            $this->setRowsPerPage($rowsPerPage);
        }
        if (($currentPage = $request->getQuery('page'))) {
            $this->setCurrentPage($currentPage);
        }
        if (($sortBy = $request->getQuery('sidx'))) {
            $columnModel = $this->getColumnModel();
            $column = $columnModel->getColumnByIndex($sortBy);
            $this->setColumnSort($column->getOption('name'), $request->getQuery('sord') == 'desc' ? Lis_Grid_Data_DbTable::ORDER_TYPE_DESC : Lis_Grid_Data_DbTable::ORDER_TYPE_ASC);
        }
        return $this;
    }
    
}