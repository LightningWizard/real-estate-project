<?php

class RealEstate_AddressManager {

    static private $_instance;
    protected $_db;

    public static function getInstance() {
        if(self::$_instance === null){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct() {
        $this->_db = Zend_Db_Table::getDefaultAdapter();
    }

    public function getSettlements() {
        $select = $this->_db->select()->from('TBL_DIR_SETTLEMENT_LIST',
                      array('id' => 'SETTLEMENT_ID',
                            'title' => 'SETTLEMENT_TITLE')
                  )
                  ->order('SETTLEMENT_TITLE ASC');
        return $this->_db->fetchAll($select);
    }

    public function getStreetsForSettlement($settlement) {
        $select = $this->_db->select()->from('VW_DIR_STREET_LIST',
                           array('id' => 'STREET_ID',
                                'title' => 'STREET_TITLE',
                                'type' => 'STREETTYPE_ID')
                       )
                       ->where('SETTLEMENT_ID = ?', $settlement)
                       ->order('STREET_TITLE ASC');
        return $this->_db->fetchAll($select);
    }

    public function getStreetTypes() {
        $select = $this->_db->select()->from('TBL_DIR_STREETTYPE_LIST',
                           array('id'        => 'STREETTYPE_ID',
                                'title'      => 'STREETTYPE_TITLE',
                                'titleShort' => 'STREETTYPE_SHORTTITLE')
                       )
                       ->order('STREETTYPE_SHORTTITLE ASC');
        return $this->_db->fetchAll($select);
    }

    public function  getGeographicalDistricts() {
        $select = $this->_db->select()->from('TBL_DIR_DISTRICT_LIST',
                                array('id' => 'DISTRICT_ID', 'title' => 'DISTRICT_TITLE'))
                        ->where('DISTRICT_TYPE = ?', RealEstate_Document_GeographicalDistrict::getDistrictType())
                        ->order('DISTRICT_TITLE ASC');
        return $this->_db->fetchAll($select);
    }
}