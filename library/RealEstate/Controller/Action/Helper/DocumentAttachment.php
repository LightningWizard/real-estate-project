<?php
class RealEstate_Controller_Action_Helper_DocumentAttachment extends Zend_Controller_Action_Helper_Abstract
{
    public function save(Lis_Document_Abstract $document, Zend_Form_Element_File $attachment)
    {
        if ($attachment->isUploaded() && $attachment->receive()) {
            $file = new TestCenter_Document_File();
            $file->FILE_TITLE   = basename($attachment->getFileName());
            $file->FILE_MIME    = $attachment->getMimeType();
            $file->FILE_CONTENT = file_get_contents($attachment->getFileName());
            $file->FILE_SIZE    = filesize($attachment->getFileName());
            $file->save();
            $document->FILE_ID = $file->FILE_ID;
            unlink($attachment->getFileName());
        }
    }
    public function direct(Lis_Document_Abstract $document, Zend_Form_Element_File $attachment)
    {
        $this->save($document, $attachment);
    }
}