<?php

include 'AbstractController.php';

class Directories_PlanningTypesController extends Directories_AbstractController {

    protected $_resource = 'directories::real-estate::planning-types';
    protected $_blankClass = 'RealEstate_Blank_PlanningType';
    protected $_documentAlias = 'PlanningType';
    protected $_documentPrefix = 'PLANNINGTYPE';

    protected function _getCollection() {
        return Lis_Document_Collection_Factory::factory('RealEstate_Document_Collection_PlanningType');
    }

    protected function _getColumnModel() {
        $columnModel = new Lis_Grid_ColumnModel();
        $columnModel->addColumn(array(
                    'name' => 'PLANNINGTYPE_TITLE',
                    'alias' => 'Title',
                    'width' => 200
                ));
        return $columnModel;
    }

    protected function _getPageTitle($document) {
        $title = '';
        if ($document->{$this->_documentPrefix . '_ID'} !== null) {
            $title = $this->view->translate($this->_documentAlias) . ' "' . $document->PLANNINGTYPE_TITLE . '"';
        } else {
            $title = $this->view->translate('New' . $this->_documentAlias);
        }
        return $title;
    }

    protected function _configGridFilterForm(Lis_Grid_ColumnModel $columnModel) {
        $filterForm = new Lis_Grid_FilterForm();
        $titleFilter = new Zend_Form_Element_Text('planningType');
        $titleFilter->setLabel('Title');

        $filterForm->addElement($titleFilter);

        $filterForm->attachToColumnModel($columnModel);
        $filterForm->attachFilterToElement('planningType', 'Title', Lis_Grid_Filter::TYPE_LIKE);
        return $filterForm;
    }
}