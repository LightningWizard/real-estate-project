<?php

class RealEstate_Document_Collection_Street extends Lis_Document_Collection_Abstract{

    protected function _configure() {
        $this->_name = 'TBL_DIR_STREET_LIST';
        $this->_primary = array('STREET_ID');
        $this->_sequence = 'GEN_STREET_ID';
    }
}