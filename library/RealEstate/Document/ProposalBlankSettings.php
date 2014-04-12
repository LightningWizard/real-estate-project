<?php

class RealEstate_Document_ProposalBlankSettings extends Lis_Document_Abstract {
    
    protected $_realEstateType = null;
    protected $_savedHiddenFields = null;
    protected $_attachedHiddenFields = array();
    protected $_hiddenFieldsAttached = false;


    public function __construct($config = null, $primary = null) {
        parent::__construct($config, $primary);
        if($this->_realEstateType instanceof RealEstate_Document_RealEstateType) {
            $this->_realEstateType = new RealEstate_Document_RealEstateType();
        }
    }
    
    public function instantiate() {
        parent::instantiate();
        if(!$this->_realEstateType instanceof RealEstate_Document_RealEstateType) {
            $this->_realEstateType = new RealEstate_Document_RealEstateType();
        } else {
            $this->_realEstateType->instantiate();
        }
        return $this;
    }

    public function __isset($key) {
        return parent::__isset($key) && isset($this->_realEstateType->{$key});
    }

    public function __unset($key) {
        try {
            parent::__unset($key);
        } catch (Exception $e) {
            unset($this->_realEstateType->{key});
        }
        return $this;
    }

    public function load($primary, $refresh = false) {
        parent::load($primary, $refresh);
        if($this->REALESTATETYPE_ID !== null){
            $this->_realEstateType->load($this->REALESTATETYPE_ID);
        }
        $this->resetSpecificFields();
        return $this;
    }

    public function __get($key) {
        try {
            return parent::__get($key);
        } catch (Exception $e) {
            return $this->_realEstateType->{$key};
        }
    }
    
    public function __set($key, $value) {
        switch ($key){
            case 'REALESTATETYPE_TITLE':
                break;
            default:
                parent::__set($key, $value);
        }
    }
    
    public function resetSpecificFields() {
        $this->_savedHiddenFields = null;
        $this->_attachedHiddenFields = array();
        $this->_hiddenFieldsAttached = false;
    }
    
    public function attachHiddenFields(array $hiddenFields) {
        $this->_attachedHiddenFields = $hiddenFields;
        $this->_hiddenFieldsAttached = true;
        return $this;
    }
    
    public function save() {
        $db = $this->getCollection()->getAdapter();
        $db->beginTransaction();
        try {
            parent::save();
            $this->_saveHiddenFields();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }   
    
    protected function _saveHiddenFields() {
        if($this->_hiddenFieldsAttached) {
            $savedHiddenFields = array();
            $db = $this->getCollection()->getAdapter();
            $collection = Lis_Document_Collection_Factory::factory(
                 'RealEstate_Document_Collection_ProposalBlankHiddenFields'
            );
            if(count($this->_attachedHiddenFields) > 0) {
                foreach($this->_attachedHiddenFields as $id => $fieldName) {
                    $id = $this->_saveHiddenField($collection, $id, $fieldName);
                    $savedHiddenFields[$id] = $fieldName;
                } 
            }
            $this->_savedHiddenFields = $savedHiddenFields;
            $this->_attachedHiddenFields = array();
            $this->_hiddenFieldsAttached = false;
            if(count($savedHiddenFields) > 0) {
                $collection->delete($db->quoteInto('SETTINGSGROUP_ID = ?', $this->SETTINGSGROUP_ID) 
                                    . ' AND '
                                    . $db->quoteInto('ID NOT IN(?)', array_keys($savedHiddenFields))
                );
            } else {
                $collection->delete($db->quoteInto('SETTINGSGROUP_ID = ?', $this->SETTINGSGROUP_ID));
            }
        }
    }
    
    protected function _saveHiddenField(Lis_Document_Collection_Abstract $collection, $id, $fieldName) {
        if($this->_isTemporaryRelationId($id)){
            $newId = $collection->insert(array(
                'SETTINGSGROUP_ID' => $this->SETTINGSGROUP_ID,
                'BLANKFIELD_NAME'  => $fieldName
            ));
            return $newId;
        } else {
            $db = $this->getCollection()->getAdapter();
            $collection->update(array('BLANKFIELD_NAME' => $fieldName),
                    $db->quoteInto('ID = ?', $id)
                    . ' AND '
                    . $db->quoteInto('SETTINGSGROUP_ID = ?', $this->SETTINGSGROUP_ID)
            );
            return $id;
        }
    }

    public function getHiddenFields() {
        if($this->_savedHiddenFields === null) {
            $this->_loadSavedHiddenFields();
        }
        return $this->_savedHiddenFields !== null ? $this->_savedHiddenFields: array();
    }
    
    protected function _loadSavedHiddenFields(){
        if($this->SETTINGSGROUP_ID !== null) {
            $db = $this->getCollection()->getAdapter();
            $select = $db->select()->from('TBL_SYS_MAINBLANK_HIDDENFIELDS', array('ID', 'BLANKFIELD_NAME'))
                                   ->where('SETTINGSGROUP_ID = ?', $this->SETTINGSGROUP_ID);
            $this->_savedHiddenFields = $db->fetchPairs($select);
        }
    }
    
    protected function _isTemporaryRelationId($id) {
        return (bool)(strpos($id, 'tmp_') !== false);
    }
}