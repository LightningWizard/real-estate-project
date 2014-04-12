<?php

/**
 * Испытательный центр УГЦР
 *
 * ЛИЦЕНЗИЯ
 *
 * Все права на данный программный продукт принадлежат ООО "Лаборатория информационных систем"
 * Ни одна часть данного програмного продукта не может использоватся без разрешения правообладателя
 *
 * @category   Испытательный центр УГЦР
 * @package    Системный модуль
 * @subpackage Системный модуль
 * @copyright  Copyright (c) 2009, ООО "Лаборатория информационных систем"
 * @license    Проприетарная лицензия
 * @version    $Id$
 */

/**
 * Реализует класс контролллера действий
 *
 * Создаёт все объекты необходимые для обработки запроса пользователя
 *
 * @uses       Zend_Controller_Action
 * @category   Испытательный центр УГЦР
 * @package    Системный модуль
 * @subpackage Системный модуль
 * @copyright  Copyright (c) 2009, ООО "Лаборатория информационных систем"
 * @license    Проприетарная лицензия
 */
class System_MainController extends Zend_Controller_Action {

    /**
     * Основное окно системы
     * Обработчик действия открытия основного окна системы
     */
    public function mainAction() {
        $this->view->helloText = $this->view->translate('MainPageHello!HTML!');
        $this->view->mainWindow = true;
        $this->view->layout()->setLayout('application');
        $this->_helper->ActionStack('scripts');
        $this->_helper->ActionStack('toolbar');
        $this->_helper->ActionStack('navigation');
    }

    public function scriptsAction() {
        $this->render('scripts', 'scripts');
    }

    public function toolbarAction() {
        $this->render('toolbar', 'toolbar');
    }

    public function navigationAction() {
        $this->render('navigation', 'navigation');
    }

    public function closeIwindowAction() {
        //$this->render('close-iwindow');
    }

    public function desktopAction() {
        //create javascript for insert Google Gears Desktop Shortcut
    }

    public function dataSaveNotifireAction() {
        $this->view->success = $this->getRequest()->getParam('dataIsSaved');
        $this->render('data-save-notifire', 'scripts');
    }

    public function printAction() {
        header('Content-Type: text/html; charset=UTF-8');
        mb_internal_encoding('UTF-8');
        $request = $this->getRequest();
        $module = $request->getParam('moduleName', null);
        $controller = $request->getParam('controllerName', null);
        $action = $request->getParam('actionName', null);
        if (!empty($module) && !empty($controller) && !empty($action)) {
            $actionStack = $this->getHelper('ActionStack');
            $filter = (array) $request->getParam('filter');
            $filter = array_map("urldecode", $filter);
            $sidx = $request->getParam('sidx');
            $sord = $request->getParam('sord');
            $actionStack->actionToStack($action, $controller, $module, array(
                'sidx' => $sidx,
                'sord' => $sord,
                'filter' => $filter
            ));
        }
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

}