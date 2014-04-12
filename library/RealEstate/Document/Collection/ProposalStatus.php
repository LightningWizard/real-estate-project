<?php

class RealEstate_Document_Collection_ProposalStatus extends Lis_Document_Collection_Abstract{

    protected function _configure() {
        $this->_name = 'TBL_DIR_PROPOSALSTATUS_LIST';
        $this->_primary = array('PROPOSALSTATUS_ID');
        $this->_sequence = 'GEN_REALESTATE_ID';
    }
}