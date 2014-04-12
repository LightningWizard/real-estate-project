<?php

class RealEstate_Document_Collection_CouplingPhoneRel extends Lis_Document_Collection_Abstract{

    protected function _configure() {
        $this->_name = 'TBL_SRV_COUPLINGUNIT_PHONES';
        $this->_primary = array('ID');
        $this->_sequence = 'GEN_RELATION_ID';
    }

    public function fetchForCouplingUnit(RealEstate_Document_CouplingUnit $couplingUnit) {
        $db = $this->getAdapter();
        $select = $db->select()->from($this->_name, array('ID', 'PHONE_NUMBER'))->where('COUPLINGUNIT_ID = ?', $couplingUnit->COUPLINGUNIT_ID);
        return $db->fetchPairs($select);
    }
}
