<?php

class RealEstate_Document_Collection_RealEstateProposal extends Lis_Document_Collection_Abstract {
    protected function _configure() {
        $this->_name = 'TBL_DOC_PROPOSAL_LIST';
        $this->_primary = array('PROPOSAL_ID');
        $this->_sequence = 'GEN_PROPOSAL_ID';
    }
}