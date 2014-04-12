<?php

class RealEstate_Document_Collection_RealEstateType extends Lis_Document_Collection_Abstract{

    protected function _configure() {
        $this->_name = 'TBL_DIR_REALESTATETYPE_LIST';
        $this->_primary = array('REALESTATETYPE_ID');
        $this->_sequence = 'GEN_REALESTATE_ID';
    }
}