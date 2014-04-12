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
 * @package    Lis_User
 * @subpackage Lis_User_Dto
 * @copyright  Copyright (c) 2009, ООО "Лаборатория информационных систем"
 * @license    Проприетарная лицензия
 * @version    $Id$
 */

/**
 * @see Lis_User_Dto_Interface
 */
require_once 'Lis/User/Dto/Interface.php';

/**
 * Абстрактный класс загрузки данных пользователя из БД
 *
 * @category   Lis
 * @package    Lis_User
 * @subpackage Lis_User_Dto
 * @copyright  Copyright (c) 2009, ООО "Лаборатория информационных систем"
 * @license    Проприетарная лицензия
 * @version    $Id$
 */
abstract class Lis_User_Dto_Db implements Lis_User_Dto_Interface
{
    /**
     * Адаптер базы данных
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;
    /**
     * Конструктор
     * @param Zend_Db_Adapter_Abstract $db
     */
    public function  __construct(Zend_Db_Adapter_Abstract $db = null)
    {
        if (null === $db)
            $db = Zend_Db_Table::getDefaultAdapter();
        $this->setDbAdapter(Zend_Db_Table::getDefaultAdapter());
    }
    /**
     * Установить новый адаптер БД
     * @param Zend_Db_Adapter_Abstract $db
     * @return Lis_User_Dto_Db
     */
    public function setDbAdapter(Zend_Db_Adapter_Abstract $db)
    {
        if (null === $db)
            throw new Exception('Try to set null as a DB adapter');
        $this->_db = $db;
        return $this;
    }
    /**
     * Получить адаптер БД
     * @return Zend_Db_Adapter_Abstract
     */
    public function getDbAdapter()
    {
        return $this->_db;
    }
}
