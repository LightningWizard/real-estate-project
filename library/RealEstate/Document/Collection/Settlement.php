<?php

class RealEstate_Document_Collection_Settlement extends Lis_Document_Collection_Abstract{

    protected function _configure() {
        $this->_name = 'TBL_DIR_SETTLEMENT_LIST';
        $this->_primary = array('SETTLEMENT_ID');
        $this->_sequence = 'GEN_SETTLEMENT_ID';
    }
}