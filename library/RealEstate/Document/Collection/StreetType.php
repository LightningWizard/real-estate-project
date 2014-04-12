<?php

class RealEstate_Document_Collection_StreetType extends Lis_Document_Collection_Abstract{
    protected function _configure(){
        $this->_name = 'TBL_DIR_STREETTYPE_LIST';
        $this->_primary = array('STREETTYPE_ID');
        $this->_sequence = 'GEN_STREET_ID';
    }
}
