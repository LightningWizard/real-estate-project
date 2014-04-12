<?php

class Lis_Grid_Meta extends Lis_Grid_Abstract{

    protected $_gridId = 'list';
    protected $_pagerId = 'pager';

    protected $_viewHelper = 'gridMeta';

    protected $_gridParams = array();
    protected $_defaultGridParams = array(
        'datatype' => 'json',
        'caption' => false,
        'viewrecords' => true,
        'rowList' => array('25', '35', '50', '75', '100'),
        'autowidth' => true,
        'height' => 'auto',
        'multiselect' => true,
        'shrinkToFit' => false,
    );

    protected $_columnModel;

    protected $_translator;
    protected static $_translatorDefault;
    protected $_translatorDisabled = false;

    public function __construct(array $gridOptions = array(), $gridId = null, $pagerId = null) {
        $this->setGridParams($gridOptions);
        if($gridId !== null) {
            $this->_gridId = $gridId;
        }
        if($pagerId !== null) {
            $this->_pagerId = $pagerId;
        }
        $this->setGridParam('pager', '#' . $this->_pagerId)
             ->setGridParam('rowNum', 50);
    }

    public function setGridParams(array $params){
        foreach($params as $param => $value) {
            $this->setGridParam($param, $value);
        }
        return $this;
    }

    public function setGridParam($param, $value) {
        $this->_gridParams[$param] = $value;
        return $this;
    }

    public function attachColumnModel(Lis_Grid_ColumnModel $columnModel){
        $this->_columnModel = $columnModel;
        return $this;
    }

    public function getGridId(){
        return $this->_gridId;
    }

    public function getPagerId(){
        return $this->_pagerId;
    }

    public function getGridParams() {
        $gridParams = array_merge($this->_defaultGridParams, $this->_gridParams);
        if($this->_columnModel !== null) {
            $gridParams['colNames'] = $this->_getColumnsNames($this->_columnModel);
            $gridParams['colModel'] = $this->_getColumnsModel($this->_columnModel);
        }
        return $gridParams;
    }

    protected function _getColumnsNames(Lis_Grid_ColumnModel $columnModel){
        $columnNames = array();
        $translator = $this->getTranslator();
        if($translator!== null) {
            foreach ($columnModel->getColumns() as $column) {
               $columnNames[] = $translator->translate($column->getOption('alias'));
            }
        } else {
            foreach ($columnModel->getColumns() as $column) {
               $columnNames[] = $column->getOption('alias');
            }
        }
        return $columnNames;
    }

    protected function _getColumnsModel(Lis_Grid_ColumnModel $columnModel){
        $colModel = array();
        foreach($columnModel->getColumns() as $column){
            $columnOptions = $column->getOptions();
            if(array_key_exists('reflector', $columnOptions)){
                unset($columnOptions['reflector']);
            }
            $columnOptions['name'] = $columnOptions['alias'];
            unset($columnOptions['alias']);
            $colModel[] = $columnOptions;
        }
        return $colModel;
    }

    public function setTranslator($translator = null) {
        if (null === $translator) {
            $this->_translator = null;
        } elseif ($translator instanceof Zend_Translate_Adapter) {
            $this->_translator = $translator;
        } elseif ($translator instanceof Zend_Translate) {
            $this->_translator = $translator->getAdapter();
        } else {
            require_once 'Zend/Form/Exception.php';
            throw new Lis_Grid_Exception('Invalid translator specified');
        }
        return $this;
    }

    public function getTranslator() {
        if ($this->translatorIsDisabled()) {
            return null;
        }

        if (null === $this->_translator) {
            return self::getDefaultTranslator();
        }

        return $this->_translator;
    }

    public function translatorIsDisabled() {
        return $this->_translatorDisabled;
    }

    public function setDisableTranslator($flag) {
        $this->_translatorDisabled = (bool) $flag;
        return $this;
    }

    public static function getDefaultTranslator() {
        if (null === self::$_translatorDefault) {
            require_once 'Zend/Registry.php';
            if (Zend_Registry::isRegistered('Zend_Translate')) {
                $translator = Zend_Registry::get('Zend_Translate');
                if ($translator instanceof Zend_Translate_Adapter) {
                    return $translator;
                } elseif ($translator instanceof Zend_Translate) {
                    return $translator->getAdapter();
                }
            }
        }
        return self::$_translatorDefault;
    }
}