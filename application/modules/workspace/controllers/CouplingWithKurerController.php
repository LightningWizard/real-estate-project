<?php

include 'AbstractCouplingController.php';

class Workspace_CouplingWithKurerController extends Workspace_AbstractCouplingController {

    protected $_resource = 'workspace::coupling::with-site-kurer';
    protected $_gridDataSource = null;
    protected $_headTitle = 'CouplingWithSiteKurer';

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
                $linkCode = $request->getParam('coupling-link');
                $service = new RealEstate_Service_Coupling_SiteKurer();
                $couplingLink = RealEstate_Service_Coupling_SiteKurer::getCouplingLink($linkCode);
                $rowsCount = $service->execute($couplingLink);
                $response = array(
                    'error' => false,
                    'rowCount' => $rowsCount,
                    'message' => $this->view->translate('ImportedRowsCountInContext')
                );
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

    protected function _configGridFilterForm(Lis_Grid_ColumnModel $columnModel) {
        $filterForm = parent::_configGridFilterForm($columnModel);
        $reflector = new RealEstate_Grid_Reflector_CouplingSource();
        $dataSourceFilter = new Zend_Form_Element_Select('dataSourceFilter');
        $sourceCodes = array(
            RealEstate_Document_CouplingUnit::KURER_BUY,
            RealEstate_Document_CouplingUnit::KURER_SELL,
            RealEstate_Document_CouplingUnit::KURER_EXCHANGE
        );
        $dataSourceFilter->setLabel('DataSource')
                         ->addMultiOption('', '');
        foreach($sourceCodes as $code) {
            $dataSourceFilter->addMultiOption($code, $reflector->execute($code));
        }
        unset($reflector);

        $filterForm->addElement($dataSourceFilter)
                   ->attachFilterToElement($dataSourceFilter->getName(), 'DataSource', Lis_Grid_Filter::TYPE_LIKE);
        return $filterForm;
    }

    protected function _setFiltersForGridData(Lis_Grid_Data_Abstract $gridData) {
        parent::_setFiltersForGridData($gridData);
        $sourceCodes = array(
            RealEstate_Document_CouplingUnit::KURER_BUY,
            RealEstate_Document_CouplingUnit::KURER_SELL,
            RealEstate_Document_CouplingUnit::KURER_EXCHANGE
        );
        $gridData->addFilter("SOURCE_CODE", Lis_Grid_Filter::TYPE_CONTAIN, $sourceCodes);
    }

}