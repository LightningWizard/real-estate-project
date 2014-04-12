<?php

class RealEstate_Document_Collection_Contragent extends Lis_Document_Collection_Abstract {
    protected function _configure() {
        $this->_name = 'TBL_DIR_CONTRAGENT_LIST';
        $this->_primary = array('CONTRAGENT_ID');
        $this->_sequence = 'GEN_CONTRAGENT_ID';
    }

    public function getContragentByPhone($phoneNumber){
        $db = $this->getAdapter();
        $select = $this->select()->where(
                'CONTRAGENT_ID = (?)',
                new Zend_Db_Expr(
                   $db->select()->from('TBL_DIR_CONTRAGENT_PHONE_REL', array('CONTRAGENT_ID'))
                                ->where('CONTRAGENT_PHONE = ?', $phoneNumber)
                )
         );
         return $this->fetchRow($select);
    }
}