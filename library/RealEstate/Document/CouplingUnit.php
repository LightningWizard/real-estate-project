<?php

class RealEstate_Document_CouplingUnit extends Lis_Document_Abstract {

    const KURER_BUY = 1;
    const KURER_SELL = 2;
    const KURER_EXCHANGE = 3;
    const TEXT_FILE = 4;

    private $_attachedPhones = array();
    private $_savedPhones = null;
    private $_phonesIsAttached = false;

    public function load($primary, $refresh = false) {
        $pks = $this->_getPrimaryKey();
        $neadRefresh = $refresh;
        if (!is_array($primary) && count($pks) == 1) {
            $pkNames = array_keys($pks);
            $primary = array($pkNames[0] => $primary);
        }
        foreach ($pks as $pk => $oldVal) {
            if (!isset($primary[$pk])) {
                throw new Lis_Document_Exception('Can not load document. Invalid document identifire. Missing ' . $pk);
            }
            if (null === $oldVal || $primary[$pk] != $oldVal) {
                $this->_data[$pk] = $primary[$pk];
                $neadRefresh = true;
            }
        }
        if (true === $neadRefresh) {
            foreach ($this->_data as $key => $value) {
                if (!array_key_exists($key, $pks)) {
                    $this->_data[$key] = null;
                }
            }
            parent::refresh();
            $this->resetSpecificFields();
        }
        return $this;
    }

    public function attachPhones(array $phones) {
        $this->_attachedPhones = $phones;
        $this->_phonesIsAttached = true;
        return $this;
    }

    public function resetSpecificFields() {
        $this->_attachedPhones = array();
        $this->_savedPhones = null;
        $this->_attachedPhones = array();
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

    private function _saveAttachedPhones() {
        if ($this->_phonesIsAttached) {
            $phonesCollection = Lis_Document_Collection_Factory::factory('RealEstate_Document_Collection_CouplingPhoneRel');
            $db = $this->getCollection()->getAdapter();
            $savedPhones = array();
            foreach ($this->_attachedPhones as $id => $phone) {
                if ($this->_isTemporaryId($id)) {
                    $relationId = $phonesCollection->insert(array(
                        'COUPLINGUNIT_ID' => $this->COUPLINGUNIT_ID,
                        'PHONE_NUMBER' => $phone
                      )
                    );
                    $savedPhones[$relationId] = $phone;
                } else {
                    $phonesCollection->update(array('PHONE_NUMBER' => $phone), $db->quoteInto('COUPLINGUNIT_ID = ?', $this->COUPLINGUNIT_ID) . ' AND '
                            . $db->quoteInto('ID = ?', $id));
                    $savedPhones[$id] = $phone;
                }
            }
            if (count($savedPhones) > 0) {
                $phonesCollection->delete($db->quoteInto('COUPLINGUNIT_ID = ?', $this->COUPLINGUNIT_ID)
                        . ' AND '
                        . $db->quoteInto('ID NOT IN(?)', array_keys($savedPhones))
                );
            } else {
                $phonesCollection->delete($db->quoteInto('COUPLINGUNIT_ID = ?', $this->COUPLINGUNIT_ID));
            }
            $this->_savedPhones = $savedPhones;
            $this->_attachedPhones = array();
            $this->_phonesIsAttached = false;
        }
    }

    protected function _isTemporaryId($id) {
        return (bool) (strpos($id, 'new_') !== false);
    }

    public function getSavedPhones() {
        if ($this->COUPLINGUNIT_ID && $this->_savedPhones === null) {
            $phonesCollection = Lis_Document_Collection_Factory::factory('RealEstate_Document_Collection_CouplingPhoneRel');
            $this->_savedPhones = $phonesCollection->fetchForCouplingUnit($this);
        }
        return (array) $this->_savedPhones;
    }

}