<?php

require_once 'XML/Query2XML/Callback.php';
class Lis_Query2XML_Callback_GridReflector implements XML_Query2XML_Callback {

    protected $_reflector;
    protected $_columnName;

    public function __construct($columnName, Lis_Grid_Reflector_Interface $reflector) {
        $this->_columnName = $columnName;
        $this->_reflector = $reflector;
    }

    public function execute(array $record) {
        if(!array_key_exists($this->_columnName, $record)){
            throw new Zend_Exception('The column "' . $this->_columnName . '" was not found in the result set');
        }
        return $this->_reflector->execute($record[$this->_columnName]);
    }
}