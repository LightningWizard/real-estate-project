<?php
require_once 'Lis/Document/Collection/Abstract.php';
class RealEstate_Document_Collection_Account extends Lis_Document_Collection_Abstract
{
    protected function _configure()
    {
        $this->_name    = 'TBL_SEC_USER_LIST';
        $this->_primary = array('USER_ID');
        $this->_sequence = 'GEN_USER_ID';
    }
}