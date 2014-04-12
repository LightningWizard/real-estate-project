<?php

class RealEstate_Service_Coupling_DataManager {

    private static $_instance;

    private $_collection;
    private $_dbAdapter;
    private $_mainTablePhones;

    public static function getInstance(){
        if(self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function setDbAdapter(Zend_Db_Adapter_Abstract $adapter){
        $this->_dbAdapter = $adapter;
        return $this;
    }

    public function getDbAdapter() {
        if($this->_dbAdapter !== null) {
            return $this->_dbAdapter;
        }
        $adapter = Zend_Db_Table::getDefaultAdapter();
        if($adapter === null) {
            throw new Exception('No default adapter was found');
        }
        $this->_dbAdapter = $adapter;
        return $this->_dbAdapter;
    }

    private function __clone(){}

    private function __construct() {
        $this->_collection = Lis_Document_Collection_Factory::factory('RealEstate_Document_Collection_CouplingUnit');
        $adapter = $this->_collection->getAdapter();
        $this->setDbAdapter($adapter);
    }

    public function save(RealEstate_Coupling_Collection $collection, $source) {
        $insertedRows = 0;
        $this->_loadPhonesFromMainTable();
        foreach ($collection as $annoucement) {
            if($annoucement->hasPhonesNumbers()){
                $this->_loadDataForAnnoucement($annoucement);
                $couplingUnit = new RealEstate_Document_CouplingUnit();
                $this->_buildingCouplingUnit($annoucement, $couplingUnit, $source);
                $couplingUnit->save();
            }
            $insertedRows++;
        }
        return $insertedRows;
    }

    private function _loadDataForAnnoucement(RealEstate_Coupling_Annoucement $annoucement) {
        $db = $this->getDbAdapter();
        $phones = $annoucement->getPhonesNumbers();
        $sql = $db->select()->from(array('PL' => 'TBL_DIR_CONTRAGENT_PHONE_REL'), array('ID'))
                  ->where('CONTRAGENT_PHONE IN (?)', $phones)
                  ->limit(1);
        $row = $db->fetchRow($sql);
        if($row === false) {
            $findedRows = array();
            foreach($phones as $phone) {
                if(array_key_exists($phone, $this->_mainTablePhones)){
                    $findedRows[] = $this->_mainTablePhones[$phone];
                }
            }
            if(count($findedRows) > 0) {
                $lastProposalId = max($findedRows);
                $proposal = new RealEstate_Document_RealEstateProposal();
                $proposal->load($lastProposalId);
                $description = $this->_getDescriptionFromProposal($proposal);
                $annoucement->setDescription($description)
                            ->setStatus(RealEstate_Coupling_Annoucement::PHONE_EXIST_MT);
            } else {
                $sql = $db->select()->from(array('SCP' => 'TBL_SRV_COUPLINGUNIT_PHONES'), array('PHONE_NUMBER'))
                                    ->joinInner(array('SCL' => 'TBL_SRV_COUPLINGUNIT_LIST'),
                                                'SCP.COUPLINGUNIT_ID = SCL.COUPLINGUNIT_ID',
                                                array('COUPLINGUNIT_TEXT')
                                                )
                                    ->where('SCP.PHONE_NUMBER IN (?)', $phones)
                                    ->order('SCL.COUPLINGUNIT_ID DESC')
                                    ->limit(1);
                $row = $db->fetchRow($sql);
                if($row !== false) {
                    $annoucement->setStatus(RealEstate_Coupling_Annoucement::PHONE_EXIST_CT);
                    $annoucement->setDescription($row['COUPLINGUNIT_TEXT']);
                }
            }
        } else {
            $annoucement->setStatus(RealEstate_Coupling_Annoucement::FROM_AGENCY);
        }
    }


    private function _buildingCouplingUnit(RealEstate_Coupling_Annoucement $annoucement, RealEstate_Document_CouplingUnit $couplingUnit, $source) {
        $couplingUnit->COUPLINGUNIT_TEXT = trim($annoucement->getMessage());
        $couplingUnit->COUPLINGUNIT_DATE = date('Y-m-d');
        $couplingUnit->COUPLINGUNIT_STATUS = $annoucement->getStatus();
        $couplingUnit->COUPLINGUNIT_DESCRIPTION = $annoucement->getDescription();
        $couplingUnit->SOURCE_CODE = $source;
        $phones = $annoucement->getPhonesNumbers();
        $phonesAttachment = array();
        foreach($phones as $phone) {
            $phonesAttachment['new_' . uniqid()] = $phone;
        }
        $couplingUnit->attachPhones($phonesAttachment);
    }

    private function _loadPhonesFromMainTable() {
        $db = $this->getDbAdapter();
        $sql = $db->select()->from('TBL_DOC_PROPOSAL_LIST', array('PROPOSAL_ID', 'OWNER_PHONES'));
        $phonesData = $db->fetchPairs($sql);
        foreach ($phonesData as $proposalId => $ownerPhones) {
            $sepOwnerPhones = explode(',', $ownerPhones);
            foreach ($sepOwnerPhones as $phone) {
                if(is_numeric($phone)) {
                    $phone = trim($phone);
                    $this->_mainTablePhones[$phone] = $proposalId;
                }
            }
        }
    }

    private function _getDescriptionFromProposal(RealEstate_Document_RealEstateProposal $proposal){
        $description = '';
        if($proposal->PROPOSAL_ID !== null) {
            $parts = array();
            if($proposal->REALESTATETYPE_ID !== null) {
                $realEstateType = new RealEstate_Document_RealEstateType();
                $realEstateType->load($proposal->REALESTATETYPE_ID);
                $parts[] = $realEstateType->REALESTATETYPE_TITLE;
                unset($realEstateType);
            }
            if($proposal->GEODISTRICT_ID !== null) {
                $geoDistrict = new RealEstate_Document_GeographicalDistrict();
                $geoDistrict->load($proposal->GEODISTRICT_ID);
                $parts[] = $geoDistrict->DISTRICT_TITLE;
                unset($geoDistrict);
            }
            if($proposal->PROPOSAL_TYPE !== null) {
                $mapper = new RealEstate_Grid_Reflector_ProposalType();
                $parts[] = $mapper->execute($proposal->PROPOSAL_TYPE);
                unset($mapper);
            }
            if($proposal->PURPOSE_ID !== null) {
                $purpose = new RealEstate_Document_RealEstatePurpose();
                $purpose->load($proposal->PURPOSE_ID);
                $parts[] = $purpose->PURPOSE_TITLE;
                unset($purpose);
            }
            $description = implode(';', $parts);
        }
        return $description;
    }

}