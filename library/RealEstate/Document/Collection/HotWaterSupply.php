<?php

class RealEstate_Document_Collection_HotWaterSupply extends Lis_Document_Collection_Abstract {
    protected function _configure() {
        $this->_name = 'TBL_DIR_HOTWATERSUPPLY_LIST';
        $this->_primary = array('HOTWATERSUPPLY_ID');
        $this->_sequence = 'GEN_SUPPLY_ID';
    }
}