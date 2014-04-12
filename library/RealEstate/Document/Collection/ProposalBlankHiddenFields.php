<?php

class RealEstate_Document_Collection_ProposalBlankHiddenFields extends Lis_Document_Collection_Abstract {
    protected $_documentClass = 'Zend_Db_Table_Row';
    
    protected function _configure() {
        $this->_name = 'TBL_SYS_MAINBLANK_HIDDENFIELDS';
        $this->_primary = array('ID');
        $this->_sequence = 'GEN_RELATION_ID';
    }
}
