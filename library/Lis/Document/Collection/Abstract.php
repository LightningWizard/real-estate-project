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
 * @see Zend_Db_Table_Abstract
 */
require_once 'Zend/Db/Table/Abstract.php';
/**
 * @see Lis_Document_Collection_Exception
 */
require_once 'Lis/Document/Collection/Exception.php';
/**
 * @see Lis_Document_Collection_Factory
 */
require_once 'Lis/Document/Collection/Factory.php';

/**
 * Абстрактный класс коллекции документов некотрого типа
 *
 * @category   Lis
 * @package    Lis_Document
 * @subpackage Lis_Document_List
 * @copyright  Copyright (c) 2009, ООО "Лаборатория информационных систем"
 * @license    Проприетарная лицензия
 * @version    $Id$
 */
abstract class Lis_Document_Collection_Abstract extends Zend_Db_Table_Abstract
{
    protected $_documentClass = null;
    /**
     * Конструктор
     *
     * ВНИМАНИЕ! Рекомендуется всегда использовать конструкцию Lis_Document_Collection_Factory::factory
     * Проверяет задан ли тип документов, если нет - самостоятельно генерирует имя класса документов
     * Если $autoregister имеет значение ИСТИНА, то проводит регистрацию экземпляра в фабрике коллекций.
     *
     * @param bool $autoregister
     */
    public function  __construct($autoregister = true)
    {
        $this->_configure();

        if (null === $this->_documentClass) {
            $this->_documentClass = str_replace('Document_Collection_', 'Document_', get_class($this));
        }

        $this->_rowClass = $this->_documentClass;
        //коллекции документов всегда используют адаптер БД по умолчанию
        parent::__construct();

        if (true === $autoregister) {
            //необходимо зарегистрировать экземпляр коллекции документов в фабрике коллекций документов
            $this->register(true);
        }
    }
    /**
     * Конфигурирование коллекции
     *
     * Вызывается на первом шаге конструктора имеено здесь следует инициализировать все параметры коллеции
     */
    abstract protected function _configure();
    /**
     * Получить тип поддерживаемого документа
     * @return string
     */
    public function getDocumentType()
    {
        return parent::getRowClass();
    }
    /**
     * Регистрация в фабрике
     * Провордит регистрацию в фабрике коллекций.
     * Если $overvrite ИСТИНА, то, если зарегистрирован другой экземпляр этого класса он будет замещён
     *
     * @param bool $overvrite
     */
    public function register($overvrite = true)
    {
        Lis_Document_Collection_Factory::register($this, $overvrite);
    }
    /**
     * Установить тип строки (документа)
     *
     * Следует запретить выполнение этой операции
     *
     * @param string $docClass
     */
    public function setRowClass($docClass)
    {
        throw new Lis_Document_Collection_Exception('Document collection can not change type of document');
    }
    /**
     * Удалить документі из коллекции
     *
     * Удалает документы из коллекции (в т.ч. и из БД)
     *
     * @param array $items массив идентификаторов документов, которые следует удалить
     * @return Lis_Document_Collection_Abstract
     * @throws Lis_Document_Collection_Exception, если коллекция документов имеет сложный идентификатор
     */
    public function deleteItems(array $items)
    {
        if (1 !== count($this->_primary)) {
            throw new Lis_Document_Collection_Exception('Programming error. Method deleteItems could be used with collections with single primary key.');
        }
        $items = (array) $items;
        if (count($items)) {
            $key = array_keys($this->_primary);
            $id = $this->_primary[$key[0]];
            $db = $this->getAdapter();
            $where = $db->quoteInto($db->quoteIdentifier($id) . ' in (?)', $items);
            $this->delete($where);
        }
        return $this;
    }
}