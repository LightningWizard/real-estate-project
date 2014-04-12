<?php

include 'AbstractController.php';

class Directories_ContragentsController extends Directories_AbstractController {

    protected $_resource = 'directories::contragents';
    protected $_blankClass = 'RealEstate_Blank_Contragent';
    protected $_documentAlias = 'Contragent';
    protected $_documentPrefix = 'CONTRAGENT';

    protected function _getColumnModel() {
        $columnModel = new Lis_Grid_ColumnModel();
        $columnModel->addColumn(array(
                    'alias' => 'Title',
                    'name' => 'CONTRAGENT_TITLE',
                    'width' => 350
                ))
                ->addColumn(array(
                    'alias' => 'ContragentType',
                    'name' => 'CONTRAGENT_TYPE',
                    'width' => 150,
                    'reflector' => 'RealEstate_Grid_Reflector_ContragentType'
                ))
                ->addColumn(array(
                    'alias' => 'Address',
                    'name' => 'CONTRAGENT_ADDRESS',
                    'width' => 250
                ));
        return $columnModel;
    }

    protected function _getCollection() {
        return Lis_Document_Collection_Factory::factory('RealEstate_Document_Collection_Contragent');
    }

    protected function _getPageTitle($document) {
        $title = '';
        if ($document->{$this->_documentPrefix . '_ID'} !== null) {
            $title = $this->view->translate($this->_documentAlias) . ' "' . $document->CONTRAGENT_TITLE . '"';
        } else {
            $title = $this->view->translate('New' . $this->_documentAlias);
        }
        return $title;
    }

    protected function _setSpecificFields() {
        $request = $this->getRequest();
        $phones = (array) $request->getParam('phones');
        $document = $this->_blank->getDocument('Contragent');
        $document->attachPhones($phones);
    }

    protected function _setSpecificViewParams() {
        $document = $this->_blank->getDocument('Contragent');
        $savedPhones = $document->getSavedPhones();
        $contragentPhones = array();
        foreach($savedPhones as $id => $phone) {
            $contragentPhones[] = array('id' => $id, 'Phone' => $phone);
        }
        $this->view->contragentPhones = $contragentPhones;
    }

    protected function _configGridFilterForm(Lis_Grid_ColumnModel $columnModel) {
        $filterForm = new Lis_Grid_FilterForm();
        $contragentTitleFilter = new Zend_Form_Element_Text('contragentTitleFilter');
        $contragentTitleFilter->setLabel('Title');

        $reflector = new RealEstate_Grid_Reflector_ContragentType();
        $types = RealEstate_Document_Contragent::getContragentTypes();
        $contragentTypeFilter = new RealEstate_Form_Element_Select('contragentTypeFilter');
        $contragentTypeFilter->setLabel('ContragentType')
                    ->setDecorators(array('CompositeElement'))
                    ->addMultiOption('','')
                    ->addReflectedOptions($types, $reflector);
        unset($reflector);

        $filterForm->addElement($contragentTitleFilter)
                   ->addElement($contragentTypeFilter);

        $filterForm->attachToColumnModel($columnModel);
        $filterForm->attachFilterToElement($contragentTitleFilter->getName(), 'Title', Lis_Grid_Filter::TYPE_LIKE)
                   ->attachFilterToElement($contragentTypeFilter->getName(), 'ContragentType', Lis_Grid_Filter::TYPE_EQUAL);
        return $filterForm;
    }

    public function searchByPhoneAction(){
        $request = $this->getRequest();
        $phoneNumber = $request->getParam('phone', null);
        $data = array(
            'id' => false,
            'title' => false,
        );
        if(!empty($phoneNumber)) {
            $collection = $this->_getCollection();
            $contragent = $collection->getContragentByPhone($phoneNumber);
            if($contragent !== null) {
                $data['id'] = $contragent->CONTRAGENT_ID;
                $data['title'] = $contragent->CONTRAGENT_TITLE;
            } else {
                $data['title'] = $this->view->translate('ContragentNotFound');
            }
        }
        echo Zend_Json::encode($data);
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

}