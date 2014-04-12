<?php

class RealEstate_Document_Collection_FileAttachment extends Lis_Document_Collection_Abstract
{
    protected function _configure()
    {
        $this->_name = 'TBL_FILE_LIST';
        $this->_primary = 'FILE_ID';
        $this->_sequence = 'GEN_FILE_ID';
    }
    
    /**
     * @param int $documentId
     * @param string $documentType
     * @return Zend_Db_Table_Rowset
     */
    public function getAttachmentByDocumentData($documentId, $documentType) {
        $query = $this->select()->where('DOCUMENT_ID = ?', $documentId)
                                                ->where('DOCUMENT_TYPE = ?', $documentType);
        return $this->fetchAll($query);
    }
}