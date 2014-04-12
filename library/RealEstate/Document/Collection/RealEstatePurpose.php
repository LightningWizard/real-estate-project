<?php

class RealEstate_Document_Collection_RealEstatePurpose extends Lis_Document_Collection_Abstract{
    //put your code here
    protected function _configure() {
        $this->_name = 'TBL_DIR_PURPOSE_LIST';
        $this->_primary = array('PURPOSE_ID');
        $this->_sequence = 'GEN_REALESTATE_ID';
    }
}