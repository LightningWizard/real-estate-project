<?php

class RealEstate_Service_ImageAttachment
{

    /**
     * @var string
     */
    private $targetDirectory;

    /**
     * @var string
     */
    private $suffixForSmallImage = '_small';
    /**
     * @var string
     */
    private $suffixForThumbnail = '_thumb';
    /**
     * @var RealEstate_Document_Collection_FileAttachment
     */
    private $attachmentCollection;

    public function __construct(RealEstate_Document_Collection_FileAttachment $collection)
    {
        $this->attachmentCollection = $collection;
    }

  /**
   *
   * @param string $targetDirectory
   * @return self
   * @throws InvalidArgumentException
   */
    public function setTargetDirectory($targetDirectory)
    {
        if(!is_dir($targetDirectory)) {
            throw new InvalidArgumentException('(  '. $targetDirectory.' ) does not exist or is not a directory');
        }
        if(!is_readable($targetDirectory)) {
            throw new InvalidArgumentException('(  '. $targetDirectory.' ) is not readable');
        }
        if(!is_writable($targetDirectory)) {
            throw new InvalidArgumentException('(  '. $targetDirectory.' ) is not writable');
        }
        $this->targetDirectory = (string) $targetDirectory;
        return $this;
    }

    /**
     * @param int $documentId
     * @param string $documentType
     * @throws RuntimeException
     */
    public function createRecordsFromFileTransfer(Zend_File_Transfer $fileTransfer, $documentId, $documentType, RealEstate_Thumbnail_GeneratorInterface $thumbNailGenerator = null)
    {
        $success = $fileTransfer->receive();
        if ($success) {
            $files = $fileTransfer->getFileInfo();
            foreach ($files as $key => $value) {
                $fileSize = $fileTransfer->getFileSize($key);
                $fileMime = $fileTransfer->getMimeType($key);
                $targetDirectory = $this->_getTargetDirectoryForDocumentType($documentType);
                $this->_createTargetDirectoryIfNotExists($targetDirectory);
                $attachmentRecord = $this->createFileAttachmentRecord($documentId, $documentType, $key, null, $fileMime, $fileSize);
                $db = $this->attachmentCollection->getAdapter();
                $db->beginTransaction();
                $filePath = $fileTransfer->getFileName($key);
                try {
                    $attachmentRecord->save();
                    $fileDestination = $this->getFileForAttachmentRecord($attachmentRecord);
                    if (rename($filePath, $fileDestination)) {
                        if($thumbNailGenerator !== null) {
                            $smallImagePath = $this->getSmallImageForAttachmentRecord($attachmentRecord);
                            $thumbNailGenerator->setOptions(array('width' => 500, 'height' => 500, 'method' => THUMBNAIL_METHOD_SCALE_MIN))
                                               ->createThumbnail($fileDestination, $smallImagePath);
                        }
                        if($thumbNailGenerator !== null) {
                            $thumbnailPath = $this->getThumbnailForAttachmentRecord($attachmentRecord);
                            $thumbNailGenerator->setOptions(array('width' => 100, 'height' => 100, 'method' => THUMBNAIL_METHOD_SCALE_MIN))
                                               ->createThumbnail($smallImagePath, $thumbnailPath);
                        }
                        $db->commit();
                    } else {
                        throw new RuntimeException('Can not move uploaded file (' . $filePath . ') to target directory (' . $fileDestination . ')');
                    }
                } catch (Exception $e) {
                    $db->rollBack();
                    unlink($filePath);
                    throw $e;
                }
            }
        } else {
            $messages = $fileTransfer->getMessages();
            throw new RuntimeException(implode(PHP_EOL, $messages));
        }
    }

    /**
     *
     * @param int $documentId
     * @param string $documentType
     * @param string $name
     * @param string $description
     * @param string $mimeType
     * @param int $size
     * @return RealEstate_Document_FileAttachment
     */
    public function createFileAttachmentRecord($documentId, $documentType, $name, $description, $mimeType, $size)
    {
        $attachmentRecord = new RealEstate_Document_FileAttachment();
        $attachmentRecord->FILE_NAME = $name;
        $attachmentRecord->FILE_DESCRIPTION = $description;
        $attachmentRecord->FILE_MIME = $mimeType;
        $attachmentRecord->FILE_SIZE = (int) $size;
        $attachmentRecord->DOCUMENT_ID = (int) $documentId;
        $attachmentRecord->DOCUMENT_TYPE = $documentType;
        return $attachmentRecord;
    }

    /**
     * @param string $documentType
     * @return string
     */
    private function _getTargetDirectoryForDocumentType($documentType)
    {
        $documentType = strrchr($documentType, '_');
        $targetDirectory = $this->targetDirectory . DIRECTORY_SEPARATOR . substr(strtolower($documentType), 1);
        return $targetDirectory;
    }

    /**
     * @param string $targetDirectory
     * @throws RuntimeException
     */
    private function _createTargetDirectoryIfNotExists($targetDirectory)
    {
        if (!file_exists($targetDirectory) && mkdir($targetDirectory, 0777)) {
            throw new RuntimeException('Unable to create target directory (' . $targetDirectory . ')');
        }
    }

    /**
     * @param int $documentId
     * @param string $documentType
     * @return Zend_Db_Table_Rowset
     */
    public function getAttachmentsByDocumentData($documentId, $documentType)
    {
        return $this->attachmentCollection->getAttachmentByDocumentData($documentId, $documentType);
    }

    /**
     * @param int $id
     * @param string $fileName
     * @param string $fileDescription
     * @return boolean
     */
    public function editAttachment($id, $fileName, $fileDescription)
    {
        $record = $this->getAttachmentById($id);
        if ($record !== null) {
            try {
                $record->FILE_NAME = $fileName;
                $record->FILE_DESCRIPTION = $fileDescription;
                $record->save();
            } catch (Exception $e) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param int $id
     * @throws Exception
     * @throws RuntimeException
     */
    public function removeAttachment($id)
    {
        $record = $this->getAttachmentById($id);
        if ($record !== null) {
            $db = $this->attachmentCollection->getAdapter();
            try {
                $pathToFile = $this->getFileForAttachmentRecord($record);
                $pathToSmall = $this->getSmallImageForAttachmentRecord($record);
                $pathToThumb = $this->getThumbnailForAttachmentRecord($record);
                $db->beginTransaction();
                $this->attachmentCollection->deleteItems(array($id));
                if (file_exists($pathToFile) && !unlink($pathToFile)) {
                    throw new RuntimeException('Unsuccessful attempt to remove related file (' . $pathToFile . ')');
                }
                if (file_exists($pathToSmall) && !unlink($pathToSmall)) {
                    throw new RuntimeException('Unsuccessful attempt to remove related file (' . $pathToSmall . ')');
                }
                if (file_exists($pathToThumb) && !unlink($pathToThumb)) {
                    throw new RuntimeException('Unsuccessful attempt to remove related file (' . $pathToThumb . ')');
                }
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
    }

    /**
     * @param int $id
     * @return  \RealEstate_Document_FileAttachment
     */
    public function getAttachmentById($id)
    {
        $collection = $this->attachmentCollection;
        $record = $this->attachmentCollection->fetchRow($collection->select()->where('FILE_ID = ?', $id));
        return $record;
    }

    /**
     * @param RealEstate_Document_FileAttachment $fileAttachment
     * @return string Path to file related to record
     */
    public function getFileForAttachmentRecord(RealEstate_Document_FileAttachment $fileAttachment)
    {
        $documentType = $fileAttachment->DOCUMENT_TYPE;
        $fileId = $fileAttachment->FILE_ID;
        $mimeType = $fileAttachment->FILE_MIME;
        $targetDirectory = $this->_getTargetDirectoryForDocumentType($documentType);
        $fileDestination = $targetDirectory . DIRECTORY_SEPARATOR
                . $fileId . '.' . $this->getFileExtensionFromMimeType($mimeType);
        return $fileDestination;
    }

    public function getThumbnailForAttachmentRecord(RealEstate_Document_FileAttachment $fileAttachment)
    {
        $documentType = $fileAttachment->DOCUMENT_TYPE;
        $fileId = $fileAttachment->FILE_ID;
        $mimeType = $fileAttachment->FILE_MIME;
        $targetDirectory = $this->_getTargetDirectoryForDocumentType($documentType);
        $pathToThumbnail = $targetDirectory . DIRECTORY_SEPARATOR
                . $fileId . $this->suffixForThumbnail .  '.' . $this->getFileExtensionFromMimeType($mimeType);
        return $pathToThumbnail;
    }

    public function getSmallImageForAttachmentRecord(RealEstate_Document_FileAttachment $fileAttachment)
    {
        $documentType = $fileAttachment->DOCUMENT_TYPE;
        $fileId = $fileAttachment->FILE_ID;
        $mimeType = $fileAttachment->FILE_MIME;
        $targetDirectory = $this->_getTargetDirectoryForDocumentType($documentType);
        $pathToThumbnail = $targetDirectory . DIRECTORY_SEPARATOR
                . $fileId . $this->suffixForSmallImage .  '.' . $this->getFileExtensionFromMimeType($mimeType);
        return $pathToThumbnail;
    }


    /**
     * @param string $fileName
     * @return string
     */
    public function getFileExtensionFromMimeType($mimeType)
    {
        switch ($mimeType) {
            case 'image/jpeg':
                return 'jpeg';
            case 'image/png':
                return 'png';
            case 'image/gif':
                return 'gif';
        }
    }

}