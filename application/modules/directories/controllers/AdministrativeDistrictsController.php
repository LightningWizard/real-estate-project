<?php

include 'AbstractController.php';

class Directories_AdministrativeDistrictsController extends Directories_AbstractController {

    protected $_resource = 'directories::districts::administrative';
    protected $_blankClass = 'RealEstate_Blank_AdministrativeDistrict';
    protected $_documentAlias = 'AdministrativeDistrict';
    protected $_documentPrefix = 'DISTRICT';
    
    protected function _getPageTitle($document) {
        $title = '';
        if ($document->{$this->_documentPrefix . '_ID'} !== null) {
            $title = $this->view->translate($this->_documentAlias) . ' ' . $document->DISTRICT_TITLE;
        } else {
            $title = $this->view->translate('New' . $this->_documentAlias);
        }
        return $title;
    }
    
    protected function _getColumnModel() {
        $columnModel = new Lis_Grid_ColumnModel();
        $columnModel->addColumn(array(
                        'alias' => 'Title',
                        'name' => 'DISTRICT_TITLE',
                        'width' => 350))
                    ->addColumn(array(
                        'alias' => 'ShortTitle',
                        'name' => 'DISTRICT_SHORTTITLE',
                        'width' => 150));
        return $columnModel;
    }
    
    protected function _getCollection(){
        return Lis_Document_Collection_Factory::factory('RealEstate_Document_Collection_GeographicalDistrict');
    }
    
    protected function _setFiltersForGridData(Lis_Grid_Data_Abstract $gridData) {
        $gridData->addFilter('DISTRICT_TYPE', Lis_Grid_Filter::TYPE_EQUAL, RealEstate_Document_AdministrativeDistrict::getDistrictType());
    }

    protected function _configGridFilterForm(Lis_Grid_ColumnModel $columnModel) {
        $filterForm = new Lis_Grid_FilterForm();
        $titleFilter = new Zend_Form_Element_Text('titleFilter');
        $titleFilter->setLabel('Title');
        
        $shortTitleFilter = new Zend_Form_Element_Text('shortTitleFilter');
        $shortTitleFilter->setLabel('ShortTitle');
        
        $filterForm->addElement($titleFilter)
                   ->addElement($shortTitleFilter);
        
        $filterForm->attachToColumnModel($columnModel);
        $filterForm->attachFilterToElement('titleFilter', 'Title', Lis_Grid_Filter::TYPE_LIKE)
                   ->attachFilterToElement('shortTitleFilter', 'ShortTitle', Lis_Grid_Filter::TYPE_LIKE);
        return $filterForm;
    }
}