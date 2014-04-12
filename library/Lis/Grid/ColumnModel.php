<?php

class Lis_Grid_ColumnModel {
    
    protected $_columns = array();
    protected $_filterForm = null;

    public function addColumn($column) {
        if(is_array($column)){
            $columnOptions = $column;
            $column = new Lis_Grid_Column($columnOptions);
            unset($columnOptions);
        }
        $index = $column->getOption('index');
        if($index === null) {
            $index = $column->getOption('alias');
            $column->setOption('index', $index);
        }
        if(array_key_exists($index, $this->_columns)) {
            unset($this->_columns[$index]);
        }
        $this->_columns[$index] = $column;
        return $this;
    }
    
    public function removeColumn($column) {
        if(is_string($column)) {
            if(array_key_exists($column, $this->_columns)){
                unset($this->_columns[$column]);
            }
        } elseif($column instanceof Lis_Grid_Column) {
            
        }
        return $this;
    }


    public function getOptionByIndex($index, $option){
        $index = (string) $index;
        if(array_key_exists($index, $this->_columns)){
            return $this->_columns[$index]->getOption($option);
        }
        return null;
    }
    
    public function getColumns() {
        return $this->_columns;
    }
    
    public function hasColumns() {
        return (bool) count($this->_columns);
    }
    
    public function getColumnByIndex($index) {
        $index = (string) $index;
        if(array_key_exists($index, $this->_columns)) {
            return $this->_columns[$index];
        }
        return null;
    }
    
   public function attachFilterForm(Lis_Grid_FilterForm $filterForm) {
        if ($this->_filterForm === $filterForm) {
            return $this;
        }
        if ($this->_filterForm !== null) {
            $this->detachFilterForm();
        }
        $this->_filterForm = $filterForm;
        if ($filterForm->getColumnModel() !== $this) {
            $filterForm->attachToColumnModel($this);
        }
        return $this;
    }

    public function detachFilterForm() {
        if($this->_filterForm === null) {
            return $this;
        }
        $filterForm = $this->_filterForm;
        $this->_filterForm = null;
        if($filterForm->getColumnModel() === $this){
            $filterForm->detachFromColumnModel();
        }
        return $this;
    }

    public function getFilterForm() {
        return $this->_filterForm;
    }
}