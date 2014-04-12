<?php

class RealEstate_Document_Collection_CouplingUnit extends Lis_Document_Collection_Abstract {

    protected function _configure() {
        $this->_name = 'TBL_SRV_COUPLINGUNIT_LIST';
        $this->_primary = array('COUPLINGUNIT_ID');
        $this->_sequence = 'GEN_PROPOSALUNIT_ID';
    }
}