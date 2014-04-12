<?php

include 'AbstractCouplingController.php';

class Workspace_CouplingWithJournalController extends Workspace_AbstractCouplingController {

    protected $_resource = 'workspace::coupling::with-call-journal';
    protected $_gridDataSource = null;
    protected $_headTitle = 'CouplingWithCallJournal';

    public function executeAction() {
        $request = $this->getRequest();
        if (!$request->isXmlHttpRequest()) {
            $this->_forward('error', 'error', 'system');
            return;
        }
        if (!$this->_helper->acl->assert($this->_resource, 'execute')) {
            $response = array(
                'error' => true,
                'message' => $this->view->translate('PermissionDenied')
            );
        } else {
            try {
                $fileValidator = new Zend_Validate_File_Upload();
                $fileValidator->setFiles();
                if ($fileValidator->isValid("fileToUpload")) {
                    $targetFile = $_FILES["fileToUpload"]["tmp_name"];
                    $fileMimeValidator = new Zend_Validate_File_MimeType("text/plain");
                    if ($fileMimeValidator->isValid($targetFile)) {
                        $service = new RealEstate_Service_Coupling_JournalCall();
                        $rowsCount = $service->execute($targetFile);
                        $response = array(
                            'error' => false,
                            'rowCount' => $rowsCount,
                            'message' => $this->view->translate('ImportedRowsCountInContext')
                        );
                    } else {
                        $response = array(
                            'error' => true,
                            'message' => implode(', ', $fileMimeValidator->getMessages())
                        );
                    }
                } else {
                    $response = array(
                        'error' => true,
                        'message' => implode(', ', $fileValidator->getMessages())
                    );
                }
            } catch (Exception $e) {
                $response = array(
                    'error' => true,
                    'message' => $this->view->translate($e->getMessage())
                );
            }
        }

        $this->view->jsonData = Zend_Json::encode($response);
        $this->_helper->layout()->setLayout('json');
    }

    protected function _setFiltersForGridData(Lis_Grid_Data_Abstract $gridData) {
        parent::_setFiltersForGridData($gridData);
        $gridData->addFilter("SOURCE_CODE", Lis_Grid_Filter::TYPE_EQUAL, RealEstate_Document_CouplingUnit::TEXT_FILE);
    }
}