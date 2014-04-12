<?php

class RealEstate_Document_Collection_ColumnModel extends Lis_Document_Collection_Abstract {

    protected function _configure() {
        $this->_name = 'TBL_SYS_COLMODEL_LIST';
        $this->_primary = array('COLMODEL_ID');
        $this->_sequence = 'GEN_COLMODEL_ID';
    }
}
