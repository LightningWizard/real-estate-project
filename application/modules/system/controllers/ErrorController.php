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
 * @subpackage Ошибки
 * @copyright  Copyright (c) 2009, ООО "Лаборатория информационных систем"
 * @license    Проприетарная лицензия
 * @version    $Id$
 */

/**
 * Реализует класс контролллера действий
 *
 * Создаёт все объекты необходимые для обработки не корректного запроса пользователя
 *
 * @uses       Zend_Controller_Action
 * @category   Испытательный центр УГЦР
 * @package    Системный модуль
 * @subpackage Ошибки
 * @copyright  Copyright (c) 2009, ООО "Лаборатория информационных систем"
 * @license    Проприетарная лицензия
 */
class System_ErrorController extends Zend_Controller_Action
{
    /**
     * Ошибка 404
     * Обработчик запроса не существующей страницы
     */
    public function errorAction()
    {
       $this->getResponse()->setHttpResponseCode(404);
    }

    /**
     * Ошибка 403
     * Обработчик запроса к не доступной странице
     */
    public function permissionDeniedAction()
    {
       $this->getResponse()->setHttpResponseCode(403);
    }
    public function runtimeErrorAction()
    {
        $this->view->msg = $this->getRequest()->getParam('errorMsg');
        $this->getResponse()->setHttpResponseCode(501);
    }
}