<?php
include 'AbstractController.php';

class Administration_UsersController extends Administration_AbstractController {

    protected $_resource = 'user';
    protected $_blankClass = 'RealEstate_Blank_Account';
    protected $_documentAlias = 'Account';
    protected $_documentPrefix = 'USER';
    protected $_mainTitle = 'Users';
    protected $_gridDataSource;

    
    public function itemAction() {
        if (!$this->_helper->acl->assert($this->_resource, 'edit')) {
            $this->_forward('permission-denied', 'error', 'system');
            return;
        }
        $blank = new RealEstate_Blank_Account();
        $document = $blank->getDocument('Account');
        $request = $this->getRequest();

        if (null !== ($id = $request->getParam('id', null))) {
            $document->load(array('USER_ID' => $id));
        } else {
            $blank->removeElement('changePassword');
        }

        if ($request->getPost('__command__') == md5('save' . Zend_Session::getId())) {
            if (null !== $id && !$request->getPost('changePassword')) {
                $_POST['password'] = $document->USER_PASSWORD;
            }
            if ($blank->isValid($_POST)) {
                try {
                    $blank->update(false);
                } catch (Lis_Blank_Exception $e) {
                    $this->_forward('runtime-error', 'error', 'system', array('errorMsg' => $e->getMessage()));
                    return;
                }

                $departments = array_keys($request->getParam('department', array()));
                $specializations = array_keys($request->getParam('specialization', array()));
                if (count($departments)) {
                    $document->setDepartmentsForSave($departments);
                }
                if (count($specializations)) {
                    $document->setSpecializationsForSave($specializations);
                }

                $blank->commit();
                $this->_helper->ActionStack('data-save-notifire', 'main', 'system', array('dataIsSaved' => true));
                if ($id != $document->USER_ID) {
                    $this->_redirect('/administration/users/item/id/' . $document->USER_ID . '/__notifires__/data-save-notifire');
                }
            } else {
                $this->_helper->ActionStack('data-save-notifire', 'main', 'system', array('dataIsSaved' => false));
            }
            $blank->getElement('changePassword')->setValue(null);
        } else {
            $blank->update(true);
        }

        $blank->getElement('password')->setValue('');
        $this->view->id = $document->USER_ID;
        $this->view->blank = $blank;
        $this->view->layout()->setLayout('application');

        $this->_helper->ActionStack('item-scripts');

        if ($document->USER_ID) {
            $title = $this->view->translate('EditAccountAction')
                    . ' '
                    . $document->USER_ACCOUNT;
        } else {
            $title = $this->view->translate('CreateAccountAction');
        }
        $this->view->headTitle($title, 'SET');
    }

    public function editAction() {
        if (!$this->_helper->acl->assert($this->_resource, 'edit')) {
            $this->_forward('permission-denied', 'error', 'system');
            return;
        }
        $request = $this->getRequest();
        if (!$request->isPost()) {
            $this->_forward('error', 'error', 'system');
            return;
        }

        $command = $request->getPost('__command__');
        if ($command == md5('lockaccount' . Zend_Session::getId())) {
            $this->lockAccounts((array) $request->getPost('items'));
        } else if ($command == md5('unlockaccount' . Zend_Session::getId())) {
            $this->unlockAccounts((array) $request->getPost('items'));
        } else {
            $this->_forward('error', 'error', 'system');
            return;
        }
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        echo Zend_Json::encode(array(
            'code' => 0,
        ));
    }

    public function lockAccounts(array $accounts) {
        if (!$this->_helper->acl->assert($this->_resource, 'edit')) {
            $this->_forward('permission-denied', 'error', 'system');
            return;
        }
        $collection = $this->_getCollection();
        $collection->update(array('USER_ISACTIVE' => 0), $collection->getAdapter()->quoteInto('USER_ID IN (?)', $accounts));
    }

    public function unlockAccounts(array $accounts) {
        if (!$this->_helper->acl->assert($this->_resource, 'edit')) {
            $this->_forward('permission-denied', 'error', 'system');
            return;
        }
        $collection = $this->_getCollection();
        $collection->update(array('USER_ISACTIVE' => 1), $collection->getAdapter()->quoteInto('USER_ID IN (?)', $accounts));
    }

    public function itemsAction() {
        if (!$this->_helper->acl->assert($this->_resource, 'read')) {
            $this->_forward('permission-denied', 'error', 'system');
            return;
        }
        $list = new Lis_Grid_Meta();
        $columnModel = $this->_getColumnModel();
        $columnModel->removeColumn('AccountIsActive');

        $request = $this->getRequest();
        $department = $request->getParam('department');
        $dataUrl = $this->_helper->url('list');
        if ($department !== null) {
            $dataUrl = $dataUrl . '?filter[departmentIdFilter]=' . $department;
        }
        $list->attachColumnModel($columnModel)
             ->setGridParam('url', $dataUrl);

        $this->view->list = $list;
        $filterForm = $this->_configItemsGridFilterForm($columnModel);
        if($filterForm instanceof Lis_Grid_FilterForm){
            $this->view->filter = $filterForm;
        }
        $this->view->acl = $this->_helper->acl;
        $this->view->aclResource = $this->_resource;
        $this->view->layout()->setLayout('application');

        $this->_helper->ActionStack('items-scripts');
        $this->view->headTitle($this->view->translate('Users'), 'SET');
        $this->render('main');
    }

    public function itemsScriptsAction() {
        $this->render('items-scripts', 'scripts');
    }

    protected function _getItemsGridDataSource() {
        if ($this->_gridDataSource === null) {
            $this->_gridDataSource = new Lis_Db_Table(array(
                        Lis_Db_Table::NAME => 'VW_SEC_USERS',
                        Lis_Db_Table::PRIMARY => array('USER_ID'),
                    ));
        }
        return $this->_gridDataSource;
    }

    public function departmentsAction() {
        if (!$this->_helper->acl->assert('department', 'read')) {
            $this->_forward('permission-denied', 'error', 'system');
            return;
        }
        $user = new RealEstate_Document_Account();
        if (null != ($userId = $this->getRequest()->getParam('id', null))) {
            $user->load($userId);
        }
        $departments = $user->getDepartments();

        $rows = array();
        foreach ($departments as $department) {
            $rows[] = array(
                'id' => (int) $department['DEPARTMENT_ID'],
                'cell' => array(
                    (int) $department['DEPARTMENT_ID'],
                    (int) $department['DEPARTMENT_ISENABLE'],
                    (string) $department['DEPARTMENT_TITLE'],
                    (int) $department['DEPARTMENT_DEAP'],
                    (int) $department['DEPARTMENT_ID_PARENT'],
                    !$department['DEPARTMENT_HASSUBDEPARTMENTS'],
                    true,
                ),
            );
        }

        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();

        echo Zend_Json::encode(array(
            'page' => 1,
            'total' => 1,
            'records' => count($rows),
            'rows' => $rows,
        ));
    }

    public function specializationsAction() {
        if (!$this->_helper->acl->assert('specialization', 'read')) {
            $this->_forward('permission-denied', 'error', 'system');
            return;
        }
        $user = new RealEstate_Document_Account();
        if (null != ($userId = $this->getRequest()->getParam('id', null))) {
            $user->load($userId);
        }
        $specializations = $user->getSpecializations();

        $rows = array();
        foreach ($specializations as $specialization) {
            $rows[] = array(
                'id' => (int) $specialization['SPECIALIZATION_ID'],
                'cell' => array(
                    (int) $specialization['SPECIALIZATION_ID'],
                    (int) $specialization['SPECIALIZATION_ISENABLE'],
                    (string) $specialization['SPECIALIZATION_TITLE'],
                    (int) $specialization['SPECIALIZATION_DEAP'],
                    (int) $specialization['SPECIALIZATION_ID_PARENT'],
                    !$specialization['SPECIALIZATION_HASCHILDREN'],
                    true,
                ),
            );
        }

        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();

        echo Zend_Json::encode(array(
            'page' => 1,
            'total' => 1,
            'records' => count($rows),
            'rows' => $rows,
        ));
    }

    protected function _getCollection() {
        return Lis_Document_Collection_Factory::factory('RealEstate_Document_Collection_Account');
    }

    protected function _getColumnModel() {
        $columnModel = new Lis_Grid_ColumnModel();
        $columnModel->addColumn(array(
                    'name' => 'USER_ACCOUNT',
                    'alias' => 'Account',
                    'width' => 200
                ))
                ->addColumn(array(
                    'name' => 'USER_LASTNAME',
                    'alias' => 'LastName',
                    'width' => 200
                ))
                ->addColumn(array(
                    'name' => 'USER_FIRSTNAME',
                    'alias' => 'FirstName',
                    'width' => 200
                ))
                ->addColumn(array(
                    'name' => 'USER_LASTNAME',
                    'alias' => 'LastName',
                    'width' => 200
                ))
                ->addColumn(array(
                    'name' => 'USER_SECONDNAME',
                    'alias' => 'SecondName',
                    'width' => 200
                ))
                ->addColumn(array(
                    'name' => 'USER_ISACTIVE',
                    'alias' => 'AccountIsActive',
                    'width' => 100,
                    'reflector' => 'RealEstate_Grid_Reflector_BoolToImg'
                ));
        return $columnModel;
    }

    protected function _getPageTitle($document) {
        if ($document->USER_ID) {
            $title = $this->view->translate('EditAccountAction')
                    . ' '
                    . $document->USER_ACCOUNT;
        } else {
            $title = $this->view->translate('CreateAccountAction');
        }
        return $title;
    }

    protected function _setSpecificFields() {
        $request = $this->getRequest();
        $document = $this->_blank->getDocument($this->_documentAlias);
        $id = $request->getParam('id', null);
        if ($id !== null && !$request->getPost('changePassword')) {
            $_POST['password'] = $document->USER_PASSWORD;
        }
        $departments = array_keys($request->getParam('department', array()));
        $specializations = array_keys($request->getParam('specialization', array()));
        if (count($departments)) {
            $document->setDepartmentsForSave($departments);
        }
        if (count($specializations)) {
            $document->setSpecializationsForSave($specializations);
        }
    }

    protected function _afterBlankUpdate() {
        $this->_blank->getElement('changePassword')->setValue(null);
        $this->_blank->getElement('password')->setValue('');
    }

    protected function _setFiltersForGridData(Lis_Grid_Data_Abstract $gridData) {
        $request = $this->getRequest();
        $filter = $request->getParam('filter');
        if (is_array($filter)) {
            $db = Zend_Db_Table::getDefaultAdapter();
            if (!empty($filter['departmentIdFilter'])) {
                $department = $filter['departmentIdFilter'];
                $gridData->addCondition($db->quoteInto('USER_ID in (select USER_ID from TBL_SEC_USERDEP_REL where DEPARTMENT_ID=?)', $department));
            }
            if (!empty($filter['specializationIdFilter'])) {
                $specialization = $filter['specializationIdFilter'];
                $gridData->addCondition($db->quoteInto('USER_ID in (select USER_ID from TBL_SEC_USERSPEC_REL where SPECIALIZATION_ID=?)', $specialization));
            }
        }
    }

    protected function _configGridFilterForm(Lis_Grid_ColumnModel $columnModel) {
        $filterForm = new Lis_Grid_FilterForm();
        $filterForm->attachToColumnModel($columnModel);

        $likeFilters = array(
            'Account',
            'LastName',
            'FirstName',
            'SecondName',
        );
        foreach ($likeFilters as $filterPart) {
            $element = new Zend_Form_Element_Text($filterPart . 'Filter');
            $element->setLabel($filterPart);
            $filterForm->addElement($element)
                    ->attachFilterToElement($element->getName(), $filterPart, Lis_Grid_Filter::TYPE_LIKE);
        }

        $element = new Zend_Form_Element_Hidden('departmentIdFilter');
        $filterForm->addElement($element);
        $element = new Zend_Form_Element_Text('departmentTitleFilter');
        $element->setLabel('DepartmentSection');
        $filterForm->addElement($element);

        $element = new Zend_Form_Element_Hidden('specializationIdFilter');
        $filterForm->addElement($element);
        $element = new Zend_Form_Element_Text('specializationTitleFilter');
        $element->setLabel('Credentials');
        $filterForm->addElement($element);

        $element = new Zend_Form_Element_Select('AccountIsActive' . 'Filter');
        $element->setLabel('AccountIsActive')
                ->addMultiOption('', '')
                ->addMultiOption('1', $this->view->translate('Yes'))
                ->addMultiOption('0', $this->view->translate('No'));
        $filterForm->addElement($element)
                ->attachFilterToElement($element->getName(), 'AccountIsActive', Lis_Grid_Filter::TYPE_EQUAL);

        return $filterForm;
    }


    public function itemsListAction() {
        $request = $this->getRequest();
        $gridData = new Lis_Grid_Data_DbTable();
        $listDataSource = $this->_getItemsGridDataSource();
        $columnModel = $this->_getColumnModel();
        $columnModel->removeColumn('AccountIsActiverk');
        $this->_configGridFilterForm($columnModel);
        $gridData->attachSource($listDataSource)
                ->attachColumnModel($columnModel)
                ->extractFromRequest($request);
        $this->_configItemsGridFilterForm($columnModel);
        $this->view->list = $gridData;
        $this->_helper->layout()->setLayout('json');
        $this->render('main');
    }

    protected function _configItemsGridFilterForm(Lis_Grid_ColumnModel $columnModel){
        $filterForm = new Lis_Grid_FilterForm();
        $filterForm->attachToColumnModel($columnModel);

        $likeFilters = array(
            'Account',
            'LastName',
            'FirstName',
            'SecondName',
        );
        foreach ($likeFilters as $filterPart) {
            $element = new Zend_Form_Element_Text($filterPart . 'Filter');
            $element->setLabel($filterPart);
            $filterForm->addElement($element)
                    ->attachFilterToElement($element->getName(), $filterPart, Lis_Grid_Filter::TYPE_LIKE);
        }

        $element = new Zend_Form_Element_Hidden('departmentIdFilter');
        $filterForm->addElement($element);
        $element = new Zend_Form_Element_Text('departmentTitleFilter');
        $element->setLabel('DepartmentSection');
        $filterForm->addElement($element);

        $element = new Zend_Form_Element_Hidden('specializationIdFilter');
        $filterForm->addElement($element);
        $element = new Zend_Form_Element_Text('specializationTitleFilter');
        $element->setLabel('Credentials');
        $filterForm->addElement($element);
        return $filterForm;
    }
}