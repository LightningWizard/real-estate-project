<?php

class Workspace_FileAttachmentsController extends Zend_Controller_Action {

    /**
     * @var \RealEstate_Service_ImageAttachment
     */
    protected $fileAttachmentService;

    public function init()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function uploadFileAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $documentId = $request->getParam('documentId');
            $documentType = $request->getParam('documentType');
            $service = $this->getFileAttachmentService();
            try {
                $fileTransfer = RealEstate_Service_ImageAttachmentFactory::createImageFileTransfer();
                $thumbNailGenerator = RealEstate_Service_ThumbnailFactory::createGenerator('AdapterForThumbnail');
                $service->createRecordsFromFileTransfer($fileTransfer, $documentId, $documentType, $thumbNailGenerator);
                echo Zend_Json::encode(array('success' => true));
            } catch (Exception $e) {
                echo Zend_Json::encode(array('success' => false, 'message' => $e->getMessage()));
            }
        }
    }

    public function listAttachmentAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $request = $this->getRequest();
        $documentId = $request->getParam('id');
        $documentType = $request->getParam('type');
        $attachments = array();
        if ($documentId && $documentType) {
            $service = $this->getFileAttachmentService();
            $rowset = $service->getAttachmentsByDocumentData($documentId, $documentType);
            $reflector = new RealEstate_Grid_Reflector_FileAttachment();
            foreach ($rowset as $record) {
                $pathToSmall = $service->getSmallImageForAttachmentRecord($record);
                $pathToThumb = $service->getThumbnailForAttachmentRecord($record);
                $linkToSmall = $this->_getLinkToFile($pathToSmall);
                $linkToThumb = $this->_getLinkToFile($pathToThumb);
                $attachments[] = array(
                    'id' => $record->FILE_ID,
                    'cell' => array(
                        '<a href="' . $linkToSmall . '" rel="lightbox[roadtrip]" title ="' . $record->FILE_DESCRIPTION . '"><img src="' . $linkToThumb . '"></a>',
                        $record->FILE_NAME,
                        $record->FILE_DESCRIPTION,
                        $record->FILE_SIZE,
                        $record->FILE_MIME,
                        $reflector->execute($record->FILE_ID),
                    )
                );
            }
            unset($reflector);
        }
        $response = array(
            'page' => 1,
            'total' => 1,
            'records' => count($attachments),
            'rows' => $attachments,
        );
        echo Zend_Json::encode($response);
    }

    public function itemAction()
    {
        $request = $this->getRequest();
        $operation = $request->getParam('oper');
        switch ($operation) {
            case 'edit' :
                $this->_forward('edit');
                return;
            case 'del':
                $this->_forward('delete');
                return;
            default:
                echo Zend_Json::encode(array('success' => false, 'message' => 'Undefined operation type'));
        }
    }

    public function editAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id');
        $name = $request->getParam('Title');
        $description = $request->getParam('Description');

        $service = $this->getFileAttachmentService();
        $result = $service->editAttachment($id, $name, $description);
        echo Zend_Json::encode(array('success' => $result));
    }

    public function deleteAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id');

        $service = $this->getFileAttachmentService();
        $service->removeAttachment($id);
    }

    /**
     * @return  \RealEstate_Service_ImageAttachment
     */
    public function getFileAttachmentService()
    {
        if ($this->fileAttachmentService === null) {
            $this->fileAttachmentService = RealEstate_Service_ImageAttachmentFactory::createService();
        }
        return $this->fileAttachmentService;
    }

    private function _getLinkToFile($pathToFile)
    {
        $linkToFile = str_replace(DIRECTORY_SEPARATOR, '/', $pathToFile);
        $serverDocumentRoot = $this->getRequest()->getServer('DOCUMENT_ROOT');
        return str_replace($serverDocumentRoot, '', $linkToFile);
    }

    public function downloadAttachmentAction()
    {
        $request = $this->getRequest();
        $fileId = (int) $request->getParam('id');
        if ($fileId) {
            $service = $this->getFileAttachmentService();
            $attachment = $service->getAttachmentById($fileId);
            if ($attachment !== null) {
                $pathToFile = $service->getFileForAttachmentRecord($attachment);
                header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
                header('Cache-Control: no-cache, must-revalidate');
                header('Pragma: no-cache');
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . 'GMT');
                header('Content-Disposition: attachment; filename="' . $attachment->FILE_NAME .'.'. $service->getFileExtensionFromMimeType($attachment->FILE_MIME) . '"');
                header('Content-Length: ' . $attachment->FILE_SIZE);
                echo file_get_contents($pathToFile);
                exit();
            } else {
                $this->_helper->layout()->setLayout('abstract');
                $this->_forward('error', 'error', 'system');
                return;
            }
        } else {
            $this->_helper->layout()->setLayout('abstract');
            $this->_forward('error', 'error', 'system');
            return;
        }
    }

}