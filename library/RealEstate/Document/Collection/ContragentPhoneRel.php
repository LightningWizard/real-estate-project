<?php

class RealEstate_Document_Collection_ContragentPhoneRel extends Lis_Document_Collection_Abstract {

    protected function _configure() {
        $this->_name = 'TBL_DIR_CONTRAGENT_PHONE_REL';
        $this->_primary = array('ID');
        $this->_sequence = 'GEN_RELATION_ID';
    }
    
    public function fetchForContragent(RealEstate_Document_Contragent $contragent) {
        $db = $this->getAdapter();
        $select = $db->select()->from($this->_name, array('ID', 'CONTRAGENT_PHONE'))->where('CONTRAGENT_ID = ?', $contragent->CONTRAGENT_ID);
        return $db->fetchPairs($select);
    }
}