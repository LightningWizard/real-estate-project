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
 * @package    Lis_Document
 * @subpackage Lis_Document_Collection
 * @copyright  Copyright (c) 2009, ООО "Лаборатория информационных систем"
 * @license    Проприетарная лицензия
 * @version    $Id$
 */

/**
 * @see Lis_Document_Collection_Exception
 */
require_once 'Lis/Document/Collection/Exception.php';

/**
 * Фабрика коллекций документов.
 * Основное назначение фабрики обеспечить наличие единственного экземпляра конкретной коллекции
 *
 * @category   Lis
 * @package    Lis_Document
 * @subpackage Lis_Document_List
 * @copyright  Copyright (c) 2009, ООО "Лаборатория информационных систем"
 * @license    Проприетарная лицензия
 * @version    $Id$
 */
 class Lis_Document_Collection_Factory
 {
     /**
      * Загруженные экземпляры классов документов
      *
      * Ассоциативный массив пар "Имя класса коллекции документов"=>"Экземпляр класса"
      *
      * @var array
      */
     private static $_register = array();

     /**
      * Получить коллекцию документов
      *
      * @param string $docClass Имя класса коллекции документов
      * @return Lis_Document_Collection_Abstract экземпля коллекции документов
      * @throws Lis_Document_Collection_Exception если не возможно создать экземпляр класса коллекции документов
      */
     public static function factory($docClass)
     {
         if (!isset(self::$_register[$docClass])) {
             try {
                 $pathToClass = str_replace('_', DIRECTORY_SEPARATOR, $docClass) . '.php';
                 require_once $pathToClass;
                 $obj = new $docClass(false);
             } catch (Exception $e) {
                 throw new Lis_Document_Collection_Exception('Can not instantiate document collection of type ' . $docClass . '. ' . $e->getMessage());
             }
             self::register($obj);
         }
         return self::$_register[$docClass];
     }

     /**
      * Зарегистрировать экземпляр коллекции документов
      * 
      * @param Lis_Document_Abstract $obj Экземпляр класса коллекции докуметов
      * @param bool $overvrite Заместить зарегистрированный экземпляр этого типа
      */
     public static function register($obj, $overvrite = true)
     {
         $docClass = get_class($obj);
         if (!($obj instanceof Lis_Document_Collection_Abstract)) {
             throw new Lis_Document_Collection_Exception('Invalid programming logic. ' . $docClass . ' is not a document collection.');
         } else {
             if (true === $overvrite || !isset(self::$_register[$docClass])) {
                self::$_register[$docClass] = $obj;
             }
         }
     }
 }