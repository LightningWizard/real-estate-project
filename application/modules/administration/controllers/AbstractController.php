<?php
abstract class Administration_AbstractController extends Zend_Controller_Action {
    protected $_resource;
    protected $_blankClass = 'Lis_Blank_Abstract';
    protected $_documentAlias = 'Document';
    protected $_documentPrefix = 'DOCUMENT';
    protected $_blank = null;
    protected $_mainTitle = null;

    abstract protected function _getPageTitle($document);
    abstract protected function _getColumnModel();
    abstract protected function _getCollection();

    public function mainAction() {
        if (!$this->_helper->acl->assert($this->_resource, 'read')) {
            $this->_forward('permission-denied', 'error', 'system');
            return;
        }
        $this->_listInterfaceAction('main-scripts');
    }

    public function mainScriptsAction() {
        $this->render('main-scripts', 'scripts');
    }

    protected function _listInterfaceAction($clientScripts = '') {
        if ($clientScripts !== '') {
            $this->_helper->ActionStack('main-scripts');
        }
        $list = $this->_buildGridMeta();
        $this->view->list = $list;
        $filterForm = $this->_configGridFilterForm($this->_getColumnModel());
        if($filterForm instanceof Lis_Grid_FilterForm){
            $this->view->filter = $filterForm;
        }
        $this->view->acl = $this->_helper->acl;
        $this->view->aclResource = $this->_resource;
        $this->_helper->layout()->setLayout('application');
        $title = $this->_mainTitle !== null ? $this->_mainTitle : $this->_documentAlias;
        if($this->_mainTitle === null){
            if(preg_match("/y$/", $title)) {
                $title = preg_replace("/y$/", "ies", $title);
            } else {
                $title .= "s";
            }
        }
        $this->view->headTitle($this->view->translate($title), 'SET');
    }

    protected function _buildGridMeta(){
        $list = new Lis_Grid_Meta();
        $list->attachColumnModel($this->_getColumnModel())
             ->setGridParam('url', $this->_helper->url('list'));
        return $list;
    }

    public function listAction() {
        $gridData = $this->_buildGridData();
        $this->_setFiltersForGridData($gridData);
        $this->view->list = $gridData;
        $this->_helper->layout()->setLayout('json');
        $this->render('main');
    }

    protected function _buildGridData() {
        $request = $this->getRequest();
        $gridData = new Lis_Grid_Data_DbTable();
        $listDataSource = $this->_getGridDataSource();
        $columnModel = $this->_getColumnModel();
        $this->_configGridFilterForm($columnModel);
        $gridData->attachSource($listDataSource)
                ->attachColumnModel($columnModel)
                ->extractFromRequest($request);
        return $gridData;
    }

    public function itemAction() {
        $blankEditable = false;
        $request = $this->getRequest();
        $command = $request->getParam('__command__', false);
        $editCommand = md5('edit' . Zend_Session::getId());
        $saveCommand = md5('save' . Zend_Session::getId());
        if($command && ($command == $editCommand || $command == $saveCommand)){
            $privilege = 'edit';
            $blankEditable = true;
        } else {
            $privilege = 'read';
        }
        if(!$this->_helper->acl->assert($this->_resource, $privilege)) {
            $this->_forward('permission-denied', 'error', 'system');
            return;
        }
        $blank = new $this->_blankClass();
        $this->_blank = $blank;
        $document = $blank->getDocument($this->_documentAlias);
        $id = $request->getParam('id', null);
        if($id !== null) {
            $document->load($id);
        }
        $this->_setDocumentDefaults($document);
        if ($request->getPost('__command__') == md5('save' . Zend_Session::getId()) && $blank->isValid($request->getPost())) {
            try {
                $blank->update(false);
            } catch (Lis_Blank_Exception $e) {
                $this->_forward('runtime-error', 'error', 'system', array('errorMsg' => $e->getMessage()));
                return;
            }
            $this->_setSpecificFields();
            $blank->commit();
            $this->_helper->ActionStack('data-save-notifire', 'main', 'system', array('dataIsSaved'=>true));
            if ($id != $document->{$this->_documentPrefix . '_ID'}) {
                $this->_redirect($this->_helper->url('item', null, null, array(
                        'id'            => $document->{$this->_documentPrefix . '_ID'},
                        '__command__'   => md5('edit' . Zend_Session::getId()),
                    )
                  )
                );
            }
        }
        if (!$blank->isErrors()) {
            $blank->update(true);
        } else {
            $this->_helper->ActionStack('data-save-notifire', 'main', 'system', array('dataIsSaved'=>false));
        }
        $this->_afterBlankUpdate();
        $this->view->id = $document->{$this->_documentPrefix . '_ID'};
        $this->view->blank = $blank;
        $this->view->blankEditable = $blankEditable;
        $this->view->acl = $this->_helper->acl;
        $this->_setSpecificViewParams();

        $this->_helper->ActionStack('item-scripts');
        $this->view->layout()->setLayout('application');
        $this->view->headTitle($this->_getPageTitle($document), 'SET');

    }

    public function itemScriptsAction() {
        $this->render('item-scripts', 'scripts');
    }

     public function deleteAction() {
        if (!$this->_helper->acl->assert($this->_resource, 'remove')) {
            $this->_forward('permission-denied', 'error', 'system');
            return;
        }
        $items = (array) $this->getRequest()->getPost('items');
        if (count($items)) {
            $this->_getCollection()->deleteItems($items);
        }
        return;
    }

    protected function  _getGridDataSource() {
        return $this->_getCollection();
    }

    protected function _setDocumentDefaults(Lis_Document_Abstract $document){}
    protected function _setSpecificFields() {}
    protected function _setSpecificViewParams() {}
    protected function _setFiltersForGridData(Lis_Grid_Data_Abstract $gridData) {}
    protected function _configGridFilterForm(Lis_Grid_ColumnModel $columnModel) {}
    protected  function _afterBlankUpdate() {}
}