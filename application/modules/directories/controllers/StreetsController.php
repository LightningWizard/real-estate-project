<?php

include 'AbstractController.php';

class Directories_StreetsController extends Directories_AbstractController {
    protected $_resource = 'directories::streets';
    protected $_blankClass = 'RealEstate_Blank_Street';
    protected $_documentAlias = 'Street';
    protected $_documentPrefix = 'STREET';

    protected $_gridDataSource = null;

    protected function _getColumnModel() {
        $columnModel = new Lis_Grid_ColumnModel();
        $columnModel->addColumn(array(
                        'alias' => 'Title',
                        'name' => 'STREET_TITLE',
                        'width' => 350,
                    ))
                    ->addColumn(array(
                        'alias' => 'StreetTypeId',
                        'name' => 'STREETTYPE_ID',
                        'hidden' => true,
                    ))
                    ->addColumn(array(
                        'alias' => 'StreetType',
                        'name' => 'STREETTYPE_TITLE',
                        'width' => 150,
                    ))
                    ->addColumn(array(
                        'alias' => 'SettlementId',
                        'name' => 'SETTLEMENT_ID',
                        'hidden' => true,
                    ))
                    ->addColumn(array(
                        'alias' => 'Settlement',
                        'name' => 'SETTLEMENT_TITLE',
                        'width' => 200,
                    )
        );
        return $columnModel;
    }

    protected function _getPageTitle($document) {
        $title = '';
        if($document->{$this->_documentPrefix . '_ID'} !== null){
            $title = $this->view->translate($this->_documentAlias) . ' "' . $document->STREET_TITLE . '"';
        } else {
            $title = $this->view->translate('New' . $this->_documentAlias);
        }
        return $title;
    }

    protected function  _getGridDataSource() {
        if($this->_gridDataSource === null) {
            $this->_gridDataSource = new Lis_Db_Table(array(
                Lis_Db_Table::NAME => 'VW_DIR_STREET_LIST',
                Lis_Db_Table::PRIMARY => array('STREET_ID'),
            ));
        }
        return $this->_gridDataSource;
    }

    protected function _getCollection() {
        return Lis_Document_Collection_Factory::factory('RealEstate_Document_Collection_Street');
    }

    protected function _configGridFilterForm(Lis_Grid_ColumnModel $columnModel) {
        $filterForm = new Lis_Grid_FilterForm();
        $titleFilter = new Zend_Form_Element_Text('titleFilter');
        $titleFilter->setLabel('Title');

        $streetTypeIdFilter = new Zend_Form_Element_Hidden('streetTypeIdFilter');

        $streetTypeFilter = new Zend_Form_Element_Text('streetTypeFilter');
        $streetTypeFilter->setLabel('StreetType');

        $settlementIdFilter = new Zend_Form_Element_Hidden('settlementIdFilter');

        $settlementFilter = new Zend_Form_Element_Text('settlementFilter');
        $settlementFilter->setLabel('Settlement');


        $filterForm->addElement($titleFilter)
                   ->addElement($streetTypeIdFilter)
                   ->addElement($streetTypeFilter)
                   ->addElement($settlementIdFilter)
                   ->addElement($settlementFilter);

        $filterForm->attachToColumnModel($columnModel);
        $filterForm->attachFilterToElement('titleFilter', 'Title', Lis_Grid_Filter::TYPE_LIKE)
                   ->attachFilterToElement('streetTypeIdFilter', 'StreetTypeId', Lis_Grid_Filter::TYPE_EQUAL)
                   ->attachFilterToElement('settlementIdFilter', 'SettlementId', Lis_Grid_Filter::TYPE_EQUAL);
        return $filterForm;
    }
}