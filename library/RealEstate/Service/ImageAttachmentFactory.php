<?php

class RealEstate_Service_ImageAttachmentFactory
{

    /**
     * @return \RealEstate_Service_ImageAttachment
     */
    public static function createService()
    {
        $collection = Lis_Document_Collection_Factory::factory('RealEstate_Document_Collection_FileAttachment');
        $targetDirectory = realpath(APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'public') . DIRECTORY_SEPARATOR . 'uploads';
        $attachmentService = new RealEstate_Service_ImageAttachment($collection);
        $attachmentService->setTargetDirectory($targetDirectory);
        return $attachmentService;
    }

    /**
     * @return \Zend_File_Transfer
     */
    public static function createImageFileTransfer(){
        $fileTransfer = new Zend_File_Transfer('Zend_File_Transfer_Adapter_Http', false, array('useByteString' => false));
        $isImageValidator = new Zend_Validate_File_IsImage();
        $fileTransfer->addValidator($isImageValidator);
        return $fileTransfer;
    }

}