<?php

class RealEstate_Validate_NoDbRecordExists extends Zend_Validate_Abstract {

    protected $_document;
    protected $_fieldName;
    protected $_db;

    const RECORD_EXISTS = 'dbRecordExists';

    protected $_messageTemplates = array(
        self::RECORD_EXISTS => 'Record with value %value% already exists in table'
    );

    public function __construct(Lis_Document_Abstract $document,
                                $field,
                                Zend_Db_Adapter_Abstract $adapter = null){
        $this->_document = $document;
        if(!isset($field, $this->_document)){
            // Если в документе нету поля $field выбрасываем исключение
            throw new Exception('Property "' . $field . '" not exists in class "' . get_class($this->_document) .  '"');
        }
        $this->_fieldName = $field;
        if ($adapter == null) {
            $adapter = Zend_Db_Table::getDefaultAdapter();
            // Если адаптер по умолчанию не задан выбрасываем исключение
            if ($adapter == null) {
                throw new Exception('No default adapter was found');
            }
        }
        $this->_db = $adapter;
    }

    public function isValid($value) {
        $collection = $this->_document->getCollection();
        $tableName = $collection->info('name');
        $primaries = $collection->info('primary');
        $select = $this->_db->select()->from($tableName, array($this->_fieldName))
                            ->where($this->_db->quoteIdentifier($this->_fieldName) . '=?', $value);
        foreach($primaries as $primary) {
            if($this->_document->{$primary} !== null) {
               $select->where($this->_db->quoteIdentifier($primary) . '!=?', $this->_document->{$primary});
            }
        }
        $recordExists = $this->_db->fetchOne($select);
        if($recordExists !== false) {
            $this->_error(self::RECORD_EXISTS, $value);
            return false;
        }
        return true;
    }
}