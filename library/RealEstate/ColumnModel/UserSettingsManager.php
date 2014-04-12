<?php

final class RealEstate_ColumnModel_UserSettingsManager {

    private $_user;
    private $_colModelDoc;
    private $_dbAdapter;
    private $_savedSettings = null;
    private $_attachedSettings = array();
    private $_settingsIsAttached = false;

    public function __construct(Lis_User $user, RealEstate_Document_ColumnModel $colModelDoc) {
        $this->_user = $user;
        $this->_colModelDoc = $colModelDoc;
    }

    public function setDbAdapter(Zend_Db_Adapter_Abstract $adapter) {
        $this->_dbAdapter = $adapter;
        return $this;
    }

    public function getDbAdapter() {
        if ($this->_dbAdapter !== null) {
            return $this->_dbAdapter;
        }
        $adapter = Zend_Db_Table::getDefaultAdapter();
        if ($adapter === null) {
            throw new Exception('No default adapter was found');
        }
        $this->_dbAdapter = $adapter;
        return $this->_dbAdapter;
    }

    public function attachColumnsSettings(array $colsSettings) {
        foreach ($colsSettings as $columnName => $colSettings) {
            $this->_attachColumnSettings($columnName, $colSettings);
        }
        $this->_settingsIsAttached = true;
        return $this;
    }

    private function _attachColumnSettings($columnName, array $colSettings) {
        $settingId = array_key_exists('settingId', $colSettings) ? $colSettings['settingId'] : null;
        $columnId = array_key_exists('columnId', $colSettings) ? $colSettings['columnId'] : null;
        $columnWidth = array_key_exists('columnWidth', $colSettings) ? $colSettings['columnWidth'] : 90;
        $isVisible = array_key_exists('columnIsVisible', $colSettings) ? $colSettings['columnIsVisible'] : 0;
        $isPrintable = array_key_exists('columnIsPrintable', $colSettings) ? $colSettings['columnIsPrintable'] : 0;
        $columnOrder = array_key_exists('columnOrder', $colSettings) ? $colSettings['columnOrder'] : null;

        if (empty($columnName)) {
            throw new Exception('Invalid program logic. Empty column name');
        }
        if (empty($columnId)) {
            throw new Exception('Invalid program logic. Empty column identificator');
        }
        if (empty($settingId)) {
            throw new Exception('Invalid program logic. Empty setting identificator');
        }

        $this->_attachedSettings[$columnName] = array(
            'COLUMN_VISIBLE' => $isVisible,
            'COLUMN_PRINTABLE' => $isPrintable,
            'COLUMN_WIDTH' => $columnWidth,
            'COLUMN_ID' => $columnId,
            'SETTING_ID' => $settingId,
            'COLUMN_ORDER' => $columnOrder,
        );
    }

    public function getUserSettings() {
        if ($this->_savedSettings === null) {
            $this->_loadUserSettings();
        }
        return $this->_savedSettings;
    }

    public function save() {
        if ($this->_settingsIsAttached) {
            $db = $this->getDbAdapter();
            $db->beginTransaction();
            try {
                $this->_saveColumnsSettings();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            $this->_savedSettings = $this->_attachedSettings;
            $this->_attachedSettings = array();
            $this->_settingsIsAttached = false;
        }
    }

    private function _saveColumnsSettings() {
        $savedIds = array();
        $db = $this->getDbAdapter();
        $colletion = Lis_Document_Collection_Factory::factory('RealEstate_Document_Collection_ColumnSettingsUser');
        $attachedColumnsNames = array_keys($this->_attachedSettings);
        foreach ($attachedColumnsNames as $columnName) {
            $columnSettings = $this->_attachedSettings[$columnName];
            $settingId = $this->_saveColumnSettings($colletion, $columnName, $columnSettings);
            $savedIds[] = $settingId;
            $this->_attachedSettings[$columnName]['SETTING_ID'] = $settingId;
        }
        if (!empty($savedIds)) {
            $colletion->delete($db->quoteInto('COLMODEL_ID = ?', $this->_colModelDoc->COLMODEL_ID)
                    . ' AND ' . $db->quoteInto('USER_ID = ?', $this->_user->getId())
                    . ' AND ' . $db->quoteInto('SETTING_ID NOT IN (?)', $savedIds));
        } else {
            $colletion->delete(
                    $db->quoteInto('COLMODEL_ID = ?', $this->_colModelDoc->COLMODEL_ID)
                    . ' AND ' . $db->quoteInto('USER_ID = ?', $this->_user->getId())
            );
        }
    }

    private function _saveColumnSettings(RealEstate_Document_Collection_ColumnSettingsUser $collection, $columnName, array $columnSettings) {
        $settingId = $columnSettings['SETTING_ID'];
        $columnSettings['COLMODEL_ID'] = $this->_colModelDoc->COLMODEL_ID;
        $columnSettings['USER_ID'] = $this->_user->getId();
        unset($columnSettings['SETTING_ID']);
        if ($this->_isSettingsExist($settingId)) {
            $settingId = $collection->insert($columnSettings);
        } else {
            $db = $this->getDbAdapter();
            $collection->update($columnSettings, $db->quoteInto('SETTING_ID = ?', $settingId));
        }
        return $settingId;
    }

    protected function _loadUserSettings() {
        $userId = $this->_user->getId();
        $colModelId = $this->_colModelDoc->COLMODEL_ID;
        if (empty($userId)) {
            throw new Exception('Empty user identificator');
        }
        if (empty($colModelId)) {
            throw new Exception('Empty column model document identificator');
        }
        $db = $this->getDbAdapter();
        $select = $db->select()->from(array('COU' => 'TBL_SYS_COLOPTION_USER'))
                ->joinInner(array('COD' => 'TBL_SYS_COLOPTION_DEFAULT'), 'COU.COLUMN_ID = COD.COLUMN_ID', array('COLUMN_NAME', 'COLUMN_ALIAS')
                )
                ->where('COU.USER_ID = ?', $userId)
                ->where('COU.COLMODEL_ID = ?', $colModelId)
                ->where('COD.COLUMN_HOLDED = 0');

        $settingsRows = $db->fetchAll($select);
        $this->_savedSettings = array();
        foreach ($settingsRows as $row) {
            $columnName = $row['COLUMN_NAME'];
            $this->_savedSettings[$columnName] = $row;
            unset($this->_savedSettings[$columnName]['COLUMN_NAME']);
        }
    }

    private function _isSettingsExist($columnId) {
        return (bool) (strpos($columnId, 'new_') === 0);
    }

}