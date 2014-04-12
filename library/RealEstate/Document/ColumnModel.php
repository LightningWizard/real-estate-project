<?php

class RealEstate_Document_ColumnModel extends Lis_Document_Abstract {

    private $_savedColsSettings = null;
    private $_attachedColsSettings = array();
    private $_colsSettingsIsAttached = false;

    public function load($primary, $refresh = false) {
        $pks = $this->_getPrimaryKey();
        $neadRefresh = $refresh;
        if (!is_array($primary) && count($pks) == 1) {
            $pkNames = array_keys($pks);
            $primary = array($pkNames[0] => $primary);
        }
        foreach ($pks as $pk=>$oldVal) {
            if (!isset($primary[$pk])) {
                throw new Lis_Document_Exception('Can not load document. Invalid document identifire. Missing ' . $pk);
            }
            if (null === $oldVal || $primary[$pk] != $oldVal) {
                $this->_data[$pk] = $primary[$pk];
                $neadRefresh = true;
            }
        }
        if (true === $neadRefresh) {
            foreach ($this->_data as $key=>$value) {
                if (!array_key_exists($key, $pks)) {
                    $this->_data[$key] = null;
                }
            }
            parent::refresh();
            $this->_resetSpecificFields();
        }
        return $this;
    }

    public function getColumnsSettings() {
        if($this->_savedColsSettings === null) {
            $this->_loadColumnsSettings();
        }
        return $this->_savedColsSettings;
    }

    public function attachColumnsSettings(array $colsSettings) {
        foreach($colsSettings as $columnName => $colSettings) {
            $this->_attachColumnSettings($columnName, $colSettings);
        }
        $this->_colsSettingsIsAttached = true;
    }

    private function _attachColumnSettings($columnName, array $colSettings){
        $columnAlias = $colSettings['columnAlias'];
        $columnId = $colSettings['columnId'];
        $columnWidth = array_key_exists('columnWidth', $colSettings) ? $colSettings['columnWidth'] : 90;
        $isVisible = array_key_exists('columnIsVisible', $colSettings) ? $colSettings['columnIsVisible'] : 0;
        $isHolded = array_key_exists('columnIsHolded', $colSettings) ? $colSettings['columnIsHolded'] : 0;
        $isPrintable = array_key_exists('columnIsPrintable', $colSettings) ? $colSettings['columnIsPrintable'] : 0;
        $columnOrder = array_key_exists('columnOrder', $colSettings) ? $colSettings['columnOrder'] : null;
        $columnReflector = array_key_exists('columnReflector', $colSettings) ? $colSettings['columnReflector'] : null;

        if(empty($columnName)) {
            throw new Exception('Invalid program logic. Empty column name');
        }

        if(empty($columnId)) {
            throw new Exception('Invalid program logic. Empty column identificator');
        }

        $this->_attachedColsSettings[$columnName] = array(
            'COLUMN_ALIAS' => $columnAlias,
            'COLUMN_VISIBLE' => $isVisible,
            'COLUMN_PRINTABLE' => $isPrintable,
            'COLUMN_WIDTH' => $columnWidth,
            'COLUMN_HOLDED' => $isHolded,
            'COLUMN_ID' => $columnId,
            'COLUMN_ORDER' => $columnOrder,
            'COLUMN_REFLECTOR' => $columnReflector
        );
    }

    private function _resetSpecificFields() {
        $this->_savedColsSettings = null;
        $this->_attachedColsSettings = array();
        $this->_colsSettingsIsAttached = false;
    }

    public function save() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            parent::save();
            if($this->_colsSettingsIsAttached) {
                $this->_saveColumnsSettings();
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $this->_savedColsSettings = $this->_attachedColsSettings;
        $this->_attachedColsSettings = array();
        $this->_colsSettingsIsAttached = false;
    }

    private function _saveColumnsSettings(){
        $savedIds = array();
        $db = $this->getCollection()->getAdapter();
        $colletion = Lis_Document_Collection_Factory::factory('RealEstate_Document_Collection_ColumnSettingsDefault');
        $attachedColumnsNames = array_keys($this->_attachedColsSettings);
        foreach($attachedColumnsNames as $columnName){
            $columnSettings = $this->_attachedColsSettings[$columnName];
            $columnId = $this->_saveColumnSettings($colletion, $columnName, $columnSettings);
            $savedIds[] = $columnId;
            $this->_attachedColsSettings[$columnName]['COLUMN_ID'] = $columnId;
        }
        if(!empty($savedIds)) {
            $colletion->delete($db->quoteInto('COLMODEL_ID = ?', $this->COLMODEL_ID)
                               . ' AND '. $db->quoteInto('COLUMN_ID NOT IN (?)', $savedIds));
        } else {
            $colletion->delete($db->quoteInto('COLMODEL_ID = ?', $this->COLMODEL_ID));
        }
    }

    private function _saveColumnSettings(RealEstate_Document_Collection_ColumnSettingsDefault $collection, $columnName,  array $columnSettings) {
        $columnId = $columnSettings['COLUMN_ID'];
        $columnSettings['COLMODEL_ID'] = $this->COLMODEL_ID;
        $columnSettings['COLUMN_NAME'] = $columnName;
        unset($columnSettings['COLUMN_ID']);
        if($this->_isSettingsExist($columnId)) {
            $columnId = $collection->insert($columnSettings);
        } else {
            $db = $this->getCollection()->getAdapter();
            $collection->update($columnSettings, $db->quoteInto('COLUMN_ID = ?', $columnId));
        }
        return $columnId;
    }

    private function _isSettingsExist($columnId){
        return (bool)(strpos($columnId, 'new_') === 0);
    }

    private function _loadColumnsSettings() {
        $db = $this->getCollection()->getAdapter();
        $select = $db->select()->from('TBL_SYS_COLOPTION_DEFAULT')
                               ->where('COLMODEL_ID = ?', $this->COLMODEL_ID)
                               ->order('COLUMN_ORDER ASC');
        $savedColumnsSettings = $db->fetchAll($select);
        $this->_savedColsSettings = array();
        foreach($savedColumnsSettings as $row) {
            $this->_savedColsSettings[$row['COLUMN_NAME']] = $row;
            unset($this->_savedColsSettings[$row['COLUMN_NAME']]['COLUMN_NAME']);
            unset($this->_savedColsSettings[$row['COLUMN_NAME']]['COLMODEL_ID']);
        }
    }

}