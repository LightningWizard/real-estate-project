<?php

include 'AbstractController.php';

class Administration_ColumnModelsController extends Administration_AbstractController {

    protected $_resource = 'column-model-setting';
    protected $_blankClass = 'RealEstate_Blank_ColumnModel';
    protected $_documentAlias = 'ColumnModel';
    protected $_documentPrefix = 'COLMODEL';
    protected $_mainTitle = 'ColumnModelSettings';
    protected $_gridDataSource;

    protected function _getCollection() {
        return Lis_Document_Collection_Factory::factory('RealEstate_Document_Collection_ColumnModel');
    }
    
    protected function _getColumnModel() {
        $columnModel = new Lis_Grid_ColumnModel();
        $columnModel->addColumn(array(
                    'name' => 'COLMODEL_ID',
                    'alias' => 'ColumnModelId',
                    'hidden' => true,
                    'key' => true
                ))->addColumn(array(
                    'name' => 'COLMODEL_ALIAS',
                    'alias' => 'Alias',
                    'width' => 150
                ))
                ->addColumn(array(
                    'name' => 'DATASOURCE_TYPE',
                    'alias' => 'DataSource',
                    'width' => 150
                ))
                ->addColumn(array(
                    'name' => 'COLMODEL_NOTICE',
                    'alias' => 'Notice',
                    'width' => 450
                ));
        return $columnModel;
    }

    protected function _setSpecificFields() {
        $request = $this->getRequest();
        $columnsSettings = (array) $request->getParam('columnsSettings');
        $document = $this->_blank->getDocument($this->_documentAlias);
        $document->attachColumnsSettings($columnsSettings);
    }

    protected function _getPageTitle($document) {

    }

    public function columnsSettingsAction() {
        $request = $this->getRequest();
        $colModelId = $request->getParam('colModelId', null);
        $dataSource = $request->getParam('dataSource');
        $dataSourceType = $request->getParam('dataSourceType');
        if (empty($dataSource) || empty($dataSourceType)) {
            throw new RuntimeException('Invalid program logic');
        }
        if ($dataSourceType == 1) {
            $savedSettings = array();
            $columnsListHelper = new RealEstate_ColumnModel_Helper_DbTableColumns();
            $columnsOptions = RealEstate_ColumnModel_Facade::getSettingsByDefault($columnsListHelper, 'VW_DOC_PROPOSAL_LIST', $savedSettings);
            $this->view->colSettings = $columns;
        }
        $this->_helper->layout()->disableLayout();
    }

    protected function _setSpecificViewParams() {
        $document = $this->_blank->getDocument($this->_documentAlias);
        $savedSettings = $document->getColumnsSettings();
        $columnsListHelper = new RealEstate_ColumnModel_Helper_DbTableColumns();
        $columnsOptions = RealEstate_ColumnModel_Facade::getSettingsByDefault($columnsListHelper, 'VW_DOC_PROPOSAL_LIST', $savedSettings);
        $settingsGridData = array();
        foreach ($columnsOptions as $colName => $colOptions) {
            $columnId = $colOptions['COLUMN_ID'];
            $columnAlias = $colOptions['COLUMN_ALIAS'];
            $columnWidth = !empty($colOptions['COLUMN_WIDTH']) ? $colOptions['COLUMN_WIDTH'] : 100;
            $isVisible = (int) $colOptions['COLUMN_VISIBLE'];
            $isPrintable = (int) $colOptions['COLUMN_PRINTABLE'];
            $isHolded = (int) $colOptions['COLUMN_HOLDED'];
            $columnRedlector = (string) $colOptions['COLUMN_REFLECTOR'];
            $settingsGridData[] = array(
                'id' => $columnId,
                'ColumnName' => $colName,
                'ColumnAlias' => $columnAlias,
                'ColumnWidth' => $columnWidth,
                'ColumnIsVisible' => $isVisible,
                'ColumnIsPrintable' => $isPrintable,
                'ColumnIsHolded' => $isHolded,
                'ColumnReflector' => $columnRedlector
            );
        }
        $this->view->columnsSettings = $settingsGridData;
    }

    public function userAction() {
        $request = $this->getRequest();
        $colModelId = $request->getParam('colModelId', null);
        if (empty($colModelId)) {
            $this->_forward('error', 'error', 'system');
            return;
        }
        $columnModelDoc = new RealEstate_Document_ColumnModel();
        $columnModelDoc->load($colModelId);

        $currentUser = Zend_Registry::get('currentUser');
        $userSettingsManager = new RealEstate_ColumnModel_UserSettingsManager($currentUser, $columnModelDoc);
        if ($request->getPost('__command__') == md5('save' . Zend_Session::getId())) {
            try {
                $attachedSettings = (array) $request->getParam('columnsSettings');
                $userSettingsManager->attachColumnsSettings($attachedSettings);
                $userSettingsManager->save();
            } catch (Exception $e) {
                $this->_forward('runtime-error', 'error', 'system', array('errorMsg' => $e->getMessage()));
                return;
            }
        }

        $defaults = $columnModelDoc->getColumnsSettings();
        $userSettings = $userSettingsManager->getUserSettings();
        $settingsForUser = RealEstate_ColumnModel_Facade::getSettingsForUser($defaults, $userSettings);
        $settingsGridData = array();
        foreach ($settingsForUser as $colName => $colOptions) {
            $settingId = $colOptions['SETTING_ID'];
            $columnId = $colOptions['COLUMN_ID'];
            $columnAlias = $colOptions['COLUMN_ALIAS'];
            $columnWidth = !empty($colOptions['COLUMN_WIDTH']) ? $colOptions['COLUMN_WIDTH'] : 100;
            $isVisible = (int) $colOptions['COLUMN_VISIBLE'];
            $isPrintable = (int) $colOptions['COLUMN_PRINTABLE'];
            $isHolded = (int) $colOptions['COLUMN_HOLDED'];
            if (!$isHolded) {
                $settingsGridData[] = array(
                    'id' => $settingId,
                    'ColumnId' => $columnId,
                    'ColumnName' => $colName,
                    'ColumnAlias' => $this->view->translate($columnAlias),
                    'ColumnWidth' => $columnWidth,
                    'ColumnIsVisible' => $isVisible,
                    'ColumnIsPrintable' => $isPrintable,
                );
            }
        }
        $this->view->columnsSettings = $settingsGridData;
        $this->view->headTitle($this->view->translate('UserColumnsModelSettings'), 'SET');
        $this->_helper->ActionStack('user-scripts');
        $this->_helper->layout()->setLayout('application');
    }

    public function userScriptsAction() {
        $this->render('user-scripts', 'scripts');
    }
}
