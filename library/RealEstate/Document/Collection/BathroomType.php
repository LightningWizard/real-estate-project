<?php

class RealEstate_Document_Collection_BathroomType extends Lis_Document_Collection_Abstract{

    protected function _configure() {
        $this->_name = 'TBL_DIR_BATHROOMTYPE_LIST';
        $this->_primary = array('BATHROOMTYPE_ID');
        $this->_sequence = 'GEN_BATHROOMTYPE_ID';
    }
}