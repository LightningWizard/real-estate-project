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

/**
 * Отладка приложения
 *
 * Данный плагин используется только в режиме разаработки приложение
 * Плагин обеспечивает вывод отладочной информации для расширения Firefox FirePHP
 * Выводятся длительность различных этапов обработки запроса пользоваетля
 *
 * @uses       Zend_Controller_Plugin_Abstract
 * @category   Lis
 * @package    Lis_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2009, ООО "Лаборатория информационных систем"
 * @license    Проприетарная лицензия
 * @version    $Id$
 */
class Lis_Controller_Plugin_Debug extends Zend_Controller_Plugin_Abstract
{
    /**
     * Вывод времени начала маршрутизации
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $this->_debug('routeStartup');
    }
    /**
     * Вывод времени конца маршрутизации
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        $this->_debug('routeShutdown');
    }
    /**
     * Вывод времени начала цикла диспетчиризации
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $this->_debug('dispatchLoopStartup');
    }
    /**
     * Вывод времени начала диспетчиризации конкретного действия
     * Выодится также имя модуля, контролллеа и собственно действия
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $this->_debug(
            'preDispatch'
          . '::'
          . $request->getModuleName()
          . '::'
          . $request->getControllerName()
          . '::'
          . $request->getActionName()
        );
    }
    /**
     * Вывод времени завершения диспетчиризации конкретного действия
     * Выодится также имя модуля, контролллеа и собственно действия
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        $this->_debug(
            'postDispatch'
          . '::'
          . $request->getModuleName()
          . '::'
          . $request->getControllerName()
          . '::'
          . $request->getActionName()
        );
    }
    /**
     * Вывод времени окончания цикла дисптчиризации
     */
    public function dispatchLoopShutdown()
    {
        $this->_debug('dispatchLoopShutdown');
    }
    /**
     * Вывод времени наступления какого-то события
     * @param string $subject
     */
    protected function _debug($subject)
    {
        FirePHP_Debug::getGenerateTime($subject);
    }
}