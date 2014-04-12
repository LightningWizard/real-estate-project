<?php
/**
 * Расширение Zend Framework от LISSoft
 *
 * ЛИЦЕНЗИЯ
 *
 * Все права на данный программный продукт принадлежат ООО "Лаборатория информационных систем"
 * Ни одна часть данного програмного продукта не может использоватся без разрешения правообладателя
 *
 * @category   Lis
 * @package    Lis_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2009, ООО "Лаборатория информационных систем"
 * @license    Проприетарная лицензия
 * @version    $Id$
 */

/** Zend_Controller_Plugin_Abstract */
require_once 'Zend/Controller/Plugin/Abstract.php';
/** Zend_Session_Namespace */
require_once 'Zend/Session/Namespace.php';

/**
 * Отслеживание авторизации пользователя
 *
 * @uses       Zend_Controller_Plugin_Abstract
 * @category   Lis
 * @package    Lis_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2009, ООО "Лаборатория информационных систем"
 * @license    Проприетарная лицензия
 * @version    $Id$
 */
class Lis_Controller_Plugin_Authorization extends Zend_Controller_Plugin_Abstract
{
    /**
     * dispatchLoopStartup() plugin hook -- проверка аторизации
     *
     * Выполнется перед вхождением в цикл диспетчиризации
     * Проверка текущей сессии была ли пройдена авторизация пользователя
     * в отрицательном случае происходит перенаправление на авторизацию
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $authorizationNamespace = new Zend_Session_Namespace('Authorization');
        if ($authorizationNamespace->legal !== true) {
            $mvc = Zend_Layout::getMvcInstance();
            $mvc->setLayout('authorization');
            $request->setModuleName(Zend_Controller_Front::getInstance()->getDispatcher()->getDefaultModule())
                    ->setControllerName('authorization')
                    ->setActionName('login');
        }
    }
}