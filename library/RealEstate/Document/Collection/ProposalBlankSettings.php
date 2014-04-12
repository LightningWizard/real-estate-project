<?php

class RealEstate_Document_Collection_ProposalBlankSettings extends Lis_Document_Collection_Abstract{

    protected function _configure() {
        $this->_name = 'TBL_SYS_MAINBLANK_SETTINGS';
        $this->_primary = array('SETTINGSGROUP_ID');
        $this->_sequence = 'GEN_SETTING_ID';
    }
    
    public function fetchForRealEstateType($realEstateType){
        $realEstateType = (int) $realEstateType;
        $select = $this->select()->where('REALESTATETYPE_ID = ?', $realEstateType);
        return $this->fetchRow($select);
    }
}