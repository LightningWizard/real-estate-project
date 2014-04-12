<?php

class Lis_Grid_Filter {
    
    const TYPE_EQUAL = 2;
    const TYPE_LESS = 4;
    const TYPE_LIKE = 8;
    const TYPE_GREATER = 16;
    const TYPE_NOTEQUAL = 32;
    const TYPE_NOTLIKE = 64;
    const TYPE_CONTAIN = 128;
    const TYPE_NOTCONTAIN = 256;
    
    
    protected $_filterColumn;
    protected $_filterType;
    protected $_filterValue;
    
    public function __construct($filterColumn, $filterType, $filterValue) {
        $this->_filterColumn = $filterColumn;
        $this->_filterType = $filterType;
        $this->_filterValue = $filterValue;
    }
    
    public function getFilterType() {
        return $this->_filterType;
    }
    
    public function getFilterColumn(){
        return $this->_filterColumn;
    }
    
    public function getFilterValue(){
        return $this->_filterValue;
    }
}

?>
