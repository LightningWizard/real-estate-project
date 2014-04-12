<?php

class RealEstate_Document_Contragent extends Lis_Document_Abstract {

    const CONTRAGENT_TYPE_SELLER  = 1;
    const CONTRAGENT_TYPE_AGENCY  = 2;
    const CONTRAGENT_TYPE_REALTOR = 3;
    const CONTRAGENT_TYPE_BUILDER = 4;
    
    public static function getContragentTypes(){
        return array(self::CONTRAGENT_TYPE_SELLER, self::CONTRAGENT_TYPE_AGENCY,
                    self::CONTRAGENT_TYPE_REALTOR, self::CONTRAGENT_TYPE_BUILDER);
    }

    protected $_attachedPhones = array();
    protected $_savedPhones = null;
    protected $_phonesIsAttached = false;

    public function instantiate() {
        parent::instantiate();
        $this->resetSpecificFields();
    }

    public function resetSpecificFields() {
        $this->_attachedPhones = array();
        $this->_savedPhones = null;
        $this->_attachedPhones = array();
    }

    public function load($primary, $refresh = false) {
        parent::load($primary, $refresh);
        $this->resetSpecificFields();
        return $this;
    }

    public function attachPhones(array $phones) {
        $this->_attachedPhones = $phones;
        $this->_phonesIsAttached = true;
        return $this;
    }

    public function save() {
        $db = $this->getCollection()->getAdapter();
        $db->beginTransaction();
        try {
            parent::save();
            $this->_saveAttachedPhones();
           $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
    
    protected function _saveAttachedPhones() {
        if($this->_phonesIsAttached) {
            $phonesCollection = Lis_Document_Collection_Factory::factory('RealEstate_Document_Collection_ContragentPhoneRel');
            $db = $this->getCollection()->getAdapter();
            $savedPhones = array();
            foreach($this->_attachedPhones as $id => $phone){
                if($this->_isTemporaryId($id)){
                    $relationId = $phonesCollection->insert(array(
                            'CONTRAGENT_ID' => $this->CONTRAGENT_ID,
                            'CONTRAGENT_PHONE' => $phone
                        )
                     );
                    $savedPhones[$relationId] = $phone;
                } else {
                    $phonesCollection->update(array('CONTRAGENT_PHONE' => $phone), $db->quoteInto('CONTRAGENT_ID = ?', $this->CONTRAGENT_ID) . ' AND '
                                             . $db->quoteInto('ID = ?', $id));
                    $savedPhones[$id] = $phone;
                }
            }
            if(count($savedPhones) > 0) {
                $phonesCollection->delete($db->quoteInto('CONTRAGENT_ID = ?', $this->CONTRAGENT_ID) 
                                          . ' AND '
                                          . $db->quoteInto('ID NOT IN(?)', array_keys($savedPhones))
                );
            } else {
                $phonesCollection->delete($db->quoteInto('CONTRAGENT_ID = ?', $this->CONTRAGENT_ID));
            }
            $this->_savedPhones = $savedPhones;
            $this->_attachedPhones = array();
            $this->_phonesIsAttached = false;
        }
    }
    
    protected function _isTemporaryId($id) {
        return (bool)(strpos($id, 'jqg') !== false);
    }
    
    public function getSavedPhones() {
        if($this->CONTRAGENT_ID && $this->_savedPhones === null) {
            $phonesCollection = Lis_Document_Collection_Factory::factory('RealEstate_Document_Collection_ContragentPhoneRel');
            $this->_savedPhones = $phonesCollection->fetchForContragent($this);
        }
        return (array) $this->_savedPhones;
    }
}