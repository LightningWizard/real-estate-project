<?php

class RealEstate_Document_Collection_PlanningType extends Lis_Document_Collection_Abstract{
    //put your code here
    protected function _configure() {
        $this->_name = 'TBL_DIR_PLANNINGTYPE_LIST';
        $this->_primary = array('PLANNINGTYPE_ID');
        $this->_sequence = 'GEN_SUPPLY_ID';
    }
}