<?php

include 'AbstractController.php';

class Administration_RolesController extends Administration_AbstractController {

    protected $_resource = 'role';
    protected $_blankClass = 'RealEstate_Blank_Role';
    protected $_documentAlias = 'Role';
    protected $_documentPrefix = 'ROLE';
    protected $_gridDataSource;

    protected function _getCollection() {
        return Lis_Document_Collection_Factory::factory('RealEstate_Document_Collection_Role');
    }

    protected function _getGridDataSource() {
        if ($this->_gridDataSource == null) {
            $this->_gridDataSource = new Lis_Db_Table(array(
                        Lis_Db_Table::NAME => 'VW_SEC_ROLE_LIST',
                        Lis_Db_Table::PRIMARY => array('ROLE_ID')
                    ));
        }
        return $this->_gridDataSource;
    }

    protected function _getPermissions(RealEstate_Document_Role $role) {
        $rows = array();
        $availableResource = Zend_Registry::get('availableResource');
        $iterator = new RecursiveIteratorIterator($availableResource, RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $res) {
            if (0 == $iterator->getDepth()) {
                unset($block);
                $block = array(
                    'title' => $res->getTitle(),
                    'items' => array(),
                );
                $acceptor = &$block['items'];
                $rows[] = &$block;
            }

            $resId = $res->getResourceId();
            $resIdHash = md5($res->getResourceId());
            $parent = $res->getParent();
            if ($parent !== null) {
                $parent = md5($parent->getResourceId());
            }
            $hasChildren = $res->hasChildren();
            $acceptor[] = array(
                'Resource'    => $this->view->translate($res->getTitle()),
                'PermRead'    => $res->hasReadMode() ? '<input type="checkbox" name="permissions[' . $resIdHash . '][read]" ' . ($role->canRead($resIdHash) ? 'checked="checked" ' : '') . '/>' : null,
                'PermEdit'    => $res->hasEditMode() ? '<input type="checkbox" name="permissions[' . $resIdHash . '][edit]" ' . ($role->canEdit($resIdHash) ? 'checked="checked" ' : '') . '/>' : null,
                'PermRemove'  => $res->hasRemoveMode() ? '<input type="checkbox" name="permissions[' . $resIdHash . '][remove]" ' . ($role->canRemove($resIdHash) ? 'checked="checked" ' : '') . '/>' : null,
                'PermExecute' => $res->hasExecuteMode() ? '<input type="checkbox" name="permissions[' . $resIdHash . '][execute]" ' . ($role->canExecute($resIdHash) ? 'checked="checked" ' : '') . '/>' : null,
                'PermExtrim'  => $res->hasExtrimMode() ? '<input type="checkbox" name="permissions[' . $resIdHash . '][extrim]" ' . ($role->canExtrimalEdit($resIdHash) ? 'checked="checked" ' : '') . '/>' : null,
                'level'       => $iterator->getDepth(),
                'parent'      => $parent,
                'isLeaf'      => !$hasChildren,
                'expanded'    => $hasChildren
            );
            unset($resId, $resIdHash, $parent, $hasChildren);
        }
        return $rows;
    }

    protected function _getColumnModel() {
        $columnModel = new Lis_Grid_ColumnModel();
        $columnModel->addColumn(array(
                    'name' => 'ROLE_TITLE',
                    'alias' => 'Title',
                    'width' => 300
                ))
                ->addColumn(array(
                    'alias' => 'DepartmentId',
                    'name' => 'DEPARTMENT_ID',
                    'hidden' => true,
                ))
                ->addColumn(array(
                    'name' => 'DEPARTMENT_TITLE',
                    'alias' => 'Department',
                    'width' => 300
                ))
                ->addColumn(array(
                    'alias' => 'SpecializationId',
                    'name' => 'SPECIALIZATION_ID',
                    'hidden' => true,
                ))
                ->addColumn(array(
                    'name' => 'SPECIALIZATION_TITLE',
                    'alias' => 'Specialization',
                    'width' => 300
                ))
                ->addColumn(array(
                    'name' => 'ROLE_NOTICE',
                    'alias' => 'Notice',
                    'width' => 300
                ));
        return $columnModel;
    }

    protected function _configGridFilterForm(Lis_Grid_ColumnModel $columnModel) {
        $filterForm = new Lis_Grid_FilterForm();
        $filterForm->attachToColumnModel($columnModel);

        $filterElement = new Zend_Form_Element_Text('titleFilter');
        $filterElement->setLabel('Title');
        $filterForm->addElement($filterElement)
                ->attachFilterToElement($filterElement->getName(), 'Title', Lis_Grid_Filter::TYPE_LIKE);

        $filterElement = new Zend_Form_Element_Hidden('departmentIdFilter');
        $filterForm->addElement($filterElement)
                ->attachFilterToElement($filterElement->getName(), 'DepartmentId', Lis_Grid_Filter::TYPE_EQUAL);
        $filterElement = new Zend_Form_Element_Text('departmentTitleFilter');
        $filterElement->setLabel('Department');
        $filterForm->addElement($filterElement);

        $filterElement = new Zend_Form_Element_Hidden('specializationIdFilter');
        $filterForm->addElement($filterElement)
                ->attachFilterToElement($filterElement->getName(), 'SpecializationId', Lis_Grid_Filter::TYPE_EQUAL);
        $filterElement = new Zend_Form_Element_Text('specializationTitleFilter');
        $filterElement->setLabel('Specialization');
        $filterForm->addElement($filterElement);
        return $filterForm;
    }

    protected function _setSpecificFields() {
        $request = $this->getRequest();
        $perms = $request->getPost('permissions');
        $document = $this->_blank->getDocument($this->_documentAlias);
        if (is_array($perms)) {
            $document->resetPermissions();
            foreach ($perms as $permId => $perm) {
                $document->setPermission(
                        $permId, isset($perm['read']) ? (bool) $perm['read'] : false,
                        isset($perm['edit']) ? (bool) $perm['edit'] : false,
                        isset($perm['remove']) ? (bool) $perm['remove'] : false,
                        isset($perm['execute']) ? (bool) $perm['execute'] : false,
                        isset($perm['extrim']) ? (bool) $perm['extrim'] : false
                );
            }
        }
    }

    protected function _getPageTitle($document) {
        if ($document->DEPARTMENT_ID) {
            $title = $this->view->translate('Role') . ' "' . ($document->ROLE_TITLE) . '"';
        } else {
            $title = $this->view->translate('Role');
        }
        return $title;
    }

    protected function _setFiltersForGridData(Lis_Grid_Data_Abstract $gridData) {
        $request = $this->getRequest();
        $forUser = $request->getParam('for-user');
        if ($forUser) {
            if ($forUser != 'undefinid') {
                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                $gridData->addCondition($db->quoteInto('DEPARTMENT_ID IN (SELECT DEPARTMENT_ID FROM TBL_SEC_USERDEP_REL WHERE USER_ID=?)', $forUser));
                $gridData->addCondition($db->quoteInto('SPECIALIZATION_ID IN (SELECT SPECIALIZATION_ID FROM TBL_SEC_USERSPEC_REL WHERE USER_ID=?)', $forUser));
            } else {
                $gridData->addCondition('0=1');
            }
        }
    }

    protected function _setSpecificViewParams() {
       $document = $this->_blank->getDocument($this->_documentAlias);
       $this->view->permissionGroups = $this->_getPermissions($document);
    }

}