<?php
require_once 'Lis/Document/Collection/Abstract.php';
class RealEstate_Document_Collection_Specialization extends Lis_Document_Collection_Abstract
{
    protected function _configure()
    {
        $this->_name    = 'TBL_SEC_SPECIALIZATION_LIST';
        $this->_primary = array('SPECIALIZATION_ID');
        $this->_sequence = 'GEN_ROLE_ID';
    }
}