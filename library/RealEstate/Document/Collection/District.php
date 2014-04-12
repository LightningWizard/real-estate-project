<?php

abstract class RealEstate_Document_Collection_District extends Lis_Document_Collection_Abstract {

    protected function _configure() {
        $this->_name = 'TBL_DIR_DISTRICT_LIST';
        $this->_primary = array('DISTRICT_ID');
        $this->_sequence = 'GEN_DISTRICT_ID';
    }
}