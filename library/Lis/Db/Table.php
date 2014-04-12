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
 * @package    Lis_Db
 * @subpackage Lis_Db_Table
 * @subpackage Plugins
 * @copyright  Copyright (c) 2009, ООО "Лаборатория информационных систем"
 * @license    Проприетарная лицензия
 * @version    $Id$
 */

/**
 * @see Zend_Db_Table
 */
require_once 'Zend/Db/Table.php';

/**
 * Класс таблицы БД
 *
 * Необходим, поскольку на момент разработки Zend_Db_Table был объявлен как абстрактный,
 * хотя API допускает его конфигурирование при инстанциировании и дальнейшую его нормальную работу
 *
 * @category   Lis
 * @package    Lis_Db
 * @subpackage Lis_Db_Table
 * @subpackage Plugins
 * @copyright  Copyright (c) 2009, ООО "Лаборатория информационных систем"
 * @license    Проприетарная лицензия
 * @version    $Id$
 */
class Lis_Db_Table extends Zend_Db_Table
{
    
}