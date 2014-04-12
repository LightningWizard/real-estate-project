<?php
class Administration_SpecializationsController extends Zend_Controller_Action
{
    public function mainAction()
    {
        $this->_listInterfaceAction('main-scripts');
    }
    public function mainScriptsAction()
    {
        $this->render('main-scripts', 'scripts');
    }
    public function itemsAction()
    {
        $this->_listInterfaceAction('main-scripts');
    }
    public function itemsScriptsAction()
    {
        $this->render('items-scripts', 'scripts');
    }
    protected function _listInterfaceAction($scriptsAction = null)
    {
        if (!$this->_helper->acl->assert('specialization', 'read')) {
            $this->_forward('permission-denied', 'error', 'system');
            return;
        }

        $request = $this->getRequest();

        if (null !== $scriptsAction) {
            $this->_helper->ActionStack($scriptsAction);
        }

        $this->view->source   = '/administration/specializations/list';
        if (null !== ($root = $request->getParam('childof', null))) {
            $this->view->source .= '/childof/' . $root;
        }
        if (null !== ($ignore = $request->getParam('ignore', null))) {
            $this->view->source .= '/ignore/' . $ignore;
        }

        $this->view->colNames = Zend_Json::encode(array(
            'id',
            $this->view->translate('Title'),
            $this->view->translate('ShortTitle'),
            $this->view->translate('Notice'),
        ));
        $this->view->colModel = Zend_Json::encode(array(
            array(
                'name'   => 'id',
                'index'  => 'id',
                'width'  => 1,
                'hidden' => true,
                'sortable' => false,
                'align'  => 'left',
                'key'    => true,
            ),
            array(
                'name'   => 'title',
                'index'  => 'title',
                'width'  => 300,
                'hidden' => false,
                'sortable' => false,
                'align'  => 'left',
                'key'    => false,
            ),
            array(
                'name'   => 'titleShort',
                'index'  => 'titleShort',
                'width'  => 200,
                'hidden' => false,
                'sortable' => false,
                'align'  => 'left',
                'key'    => false,
            ),
            array(
                'name'   => 'notice',
                'index'  => 'notice',
                'width'  => 300,
                'hidden' => false,
                'sortable' => false,
                'align'  => 'left',
                'key'    => false,
            ),
        ));
        $this->view->acl = $this->_helper->acl;
        $this->view->layout()->setLayout('application');
        $this->view->headTitle($this->view->translate('UsersSpecializations'), 'SET');
        $this->render('main');
    }

    public function listAction()
    {
        if (!$this->_helper->acl->assert('specialization', 'read')) {
            $this->_forward('permission-denied', 'error', 'system');
            return;
        }

        $request = $this->getRequest();
        $db = Zend_Db_Table::getDefaultAdapter();

        $root = (int)$request->getParam('childof', null);
        $ignore = (int)$request->getParam('ignore', null);
        $specializations = $db->fetchAll('select * from proc_getspecialization('. $db->quoteInto('?', $root) . ', ' . $db->quoteInto('?', $ignore) . ')');

        $rows = array();
        foreach ($specializations as $specialization) {
            $rows[] = array(
                'id'   => (int) $specialization['SPECIALIZATION_ID'],
                'cell' => array(
                    (int)    $specialization['SPECIALIZATION_ID'],
                    (string) $specialization['SPECIALIZATION_TITLE'],
                    (string) $specialization['SPECIALIZATION_TITLE_SHORT'],
                    (string) $specialization['SPECIALIZATION_NOTICE'],

                    (int)    $specialization['SPECIALIZATION_DEAP'],
                    (int)    $specialization['SPECIALIZATION_ID_PARENT'],
                             !$specialization['SPECIALIZATION_HASCHILDREN'],
                             true,
                ),
            );
        }

        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();

        echo zend_Json::encode(array(
            'page'    =>1,
            'total'   => 1,
            'records' => count($rows),
            'rows'    => $rows,
        ));
        return;
    }

    public function itemAction()
    {
        $blankEditable = false;
        if ($this->_helper->acl->assert('specialization', 'edit')) {
            $blankEditable = true;
        } else if ($this->_helper->acl->assert('specialization', 'read')) {
            $blankEditable = false;
        } else {
            $this->_forward('permission-denied', 'error', 'system');
            return;
        }

        $blank = new RealEstate_Blank_Specialization();
        $document = $blank->getDocument('Specialization');

        $request = $this->getRequest();

        if (null !== ($id = $request->getParam('id', null))) {
            $document->load($id);
        }

        if ($request->getPost('__command__') == md5('save' . Zend_Session::getId()) && $blank->isValid($_POST)) {
            if (!$this->_helper->acl->assert('specialization', 'edit')) {
                $this->_forward('permission-denied', 'error', 'system');
                return;
            }

            try {
                $blank->update(false);
            } catch (Lis_Blank_Exception $e) {
                $this->_forward('runtime-error', 'error', 'system', array('errorMsg' => $e->getMessage()));
                return;
            }
            $blank->commit();
            $this->_helper->ActionStack('data-save-notifire', 'main', 'system', array('dataIsSaved'=>true));
            if ($id != $document->SPECIALIZATION_ID) {
                $this->_redirect('/administration/specializations/item/id/' . $document->SPECIALIZATION_ID . '/__notifires__/data-save-notifire');
            }
        }
        if (!$blank->isErrors()) {
            $blank->update(true);
        } else {
            $this->_helper->ActionStack('data-save-notifire', 'main', 'system', array('dataIsSaved'=>false));
        }

        $this->view->id = $document->SPECIALIZATION_ID;
        $this->view->blank = $blank;
        $this->view->blankEditable = $blankEditable;
        $this->view->layout()->setLayout('application');

        $this->_helper->ActionStack('item-scripts');

        if ($document->SPECIALIZATION_ID) {
            $title = $this->view->translate('Specialization') . ' "' . ($document->SPECIALIZATION_TITLE_SHORT ? $document->SPECIALIZATION_TITLE_SHORT : $document->SPECIALIZATION_TITLE) . '"';
        } else {
            $title = $this->view->translate('NewSpecialization');
        }
        $this->view->headTitle($title, 'SET');
    }
    public function itemScriptsAction()
    {
        $this->render('item-scripts', 'scripts');
    }

    public function deleteAction()
    {
        if (!$this->_helper->acl->assert('specialization', 'remove')) {
            $this->_forward('permission-denied', 'error', 'system');
            return;
        }
        $items = (array) $this->getRequest()->getPost('items');
        if (count($items)) {
            $collection = Lis_Document_Collection_Factory::factory('RealEstate_Document_Collection_Specialization');
            $collection->deleteItems($items);
        }
        exit();
    }
}