<?php

class RealEstate_Document_Collection_HeatingType extends Lis_Document_Collection_Abstract{

    protected function _configure() {
        $this->_name = 'TBL_DIR_HEATINGTYPE_LIST';
        $this->_primary = array('HEATINGTYPE_ID');
        $this->_sequence = 'GEN_SUPPLY_ID';
    }
}