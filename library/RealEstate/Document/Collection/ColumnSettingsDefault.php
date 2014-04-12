<?php

class RealEstate_Document_Collection_ColumnSettingsDefault extends Lis_Document_Collection_Abstract{

    protected $_documentClass = 'Zend_Db_Table_Row';

    protected function _configure() {
        $this->_name = 'TBL_SYS_COLOPTION_DEFAULT';
        $this->_primary = array('COLUMN_ID');
        $this->_sequence = 'GEN_SETTING_ID';
    }
}