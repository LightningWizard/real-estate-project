<?php

class Lis_Grid_Column  {

    protected $_options = array();
    protected $_defaultOptions = array(
        'hidden' => false,
        'width' => 100,
        'sortable' => true
    );


    public function __construct(array $options) {
        if (!array_key_exists('name', $options)) {
            throw new Lis_Grid_Column_Exception('Empty input parametr name');
        }
        if (!array_key_exists('alias', $options)) {
            throw new Lis_Grid_Column_Exception('Empty input parametr alias');
        }
        $this->setOptions($options);
    }


    public function setOptions(array $options) {
        foreach ($options as $option => $value) {
            $this->setOption($option, $value);
        }
        return $this;
    }

    public function setOption($option, $value) {
        $this->_options[$option] = $value;
        return $this;
    }

    public function getOptions(){
        return array_merge($this->_defaultOptions, $this->_options);
    }

    public function getOption($option) {
        if (array_key_exists($option, $this->_options)) {
            return $this->_options[$option];
        } else if(array_key_exists($option, $this->_defaultOptions)){
            return $this->_defaultOptions[$option];
        }
        return null;
    }
}
