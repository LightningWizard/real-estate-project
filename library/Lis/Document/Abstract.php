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
 * @copyright  Copyright (c) 2009, ООО "Лаборатория информационных систем"
 * @license    Проприетарная лицензия
 * @version    $Id$
 */

/**
 * @see Zend_Db_Table_Row_Abstract
 */
require_once 'Zend/Db/Table/Row/Abstract.php';

/**
 * Абстрактный класс докумета, хранящегося в БД
 *
 * @category   Lis
 * @package    Lis_Document
 * @copyright  Copyright (c) 2009, ООО "Лаборатория информационных систем"
 * @license    Проприетарная лицензия
 * @version    $Id$
 */
class Lis_Document_Abstract extends Zend_Db_Table_Row_Abstract
{
    /**
     * Коллекция документов данного типа
     * @var Lis_Document_Collection_Abstract
     */
    protected $_collection;

    /**
     * Конструктор нового документа
     *
     * Параметр коллекции указывает с каким набором документов следует ассоциировать данный документ.
     * По умолчанию этот параметр используетследующее соглашение об именовании документов и коллекций докуметов:
     * Имена всех документов имеют вид Document_ТипДокумента
     * Имена всех коллекций документов имеют вид Document_Collection_ТипДокумента
     *
     * @param $config
     * @param $primary
     */
    public function __construct($config = null, $primary = null)
    {
        $this->_collection = &$this->_table;
        if (is_array($config)) {
            parent::__construct($config);
        } else {
            parent::__construct();
            $this->setCollection($config);
            if (null === $primary) {
                $this->instantiate();
            } else {
                $this->load($primary, true);
            }
        }
    }

    /**
     * Получить значение поля документа
     *
     * @param string $key Название поля.
     * @return mixed Значение поля.
     */

    public function __get($key)
    {
        //доступ к полю записи БД
        return parent::__get($key);
    }

    /**
     * Проверка заполненности полей документа
     *
     * @param string $key Имя поля.
     * @return boolean
     */
    public function __isset($key)
    {
        //поле существует и имеет установленное значение
        return parent::__isset($key) && null !== parent::__get($key);
    }

    /**
     * Устнаовить значение поля документа
     *
     * @param string $key Имя поля.
     * @param mixed $val Значение поля.
     * @return void
     */
    public function __set($key, $value)
    {
        parent::__set($key, $value);
    }

    /**
     * Уничтожение значения поля.
     *
     * идентично вызову __set($key, null)
     *
     * @param string $key
     * @return Lis_Document_Abstract
     */
    public function __unset($key) {
        if (parent::__isset($key)) {
            parent::__set($key, null);
        }
        return $this;
    }

    /**
     * Копировать документ
     *
     * Если копирование элемента запрещено, то следует возвращать null-значение
     * Это действие не приводит к изменнению хранилища.
     * Для записи копии следует вызвать метод save.
     *
     * @param mixed $id Первичный ключ для копии документа
     * @param string|array $callback Функция обратного вызова, выполняемая при копировании. Функции обратного вызова передаётся объект-оригинал и объект-копия
     * @return null|Lis_Document_Abstract
     */
    public function copy($id = null, $callback = null)
    {
        $data = $this->toArray();
        $pks = $this->_getPrimaryKey();
        foreach (array_keys($pks) as $pk) {
            if (null === $id) {
                $data[$pk] = null;
            } else {
                $data[$pk] = @$id[$pk];
            }
        }
        $copy = $this->getCollection()->createRow($data);
        if (null !== $callback && function_exists($callback)) {
            call_user_func($callback, $this, $copy);
        }
        return $copy;
    }

    /**
     * Уничтожить запись документ в хранилище
     *
     * @return Lis_Document_Abstract
     */
    public function delete()
    {
        parent::delete();
    }

    /**
     * Получить ассоциированный список документов
     *
     * @return Lis_Document_Collection_Abstract
     */
    public function getCollection()
    {
        if (null === $this->_collection) {
            $this->setCollection();
        }
        return $this->_collection;
    }

    /**
     *  Создать пустую структуру данных.
     */
    public function instantiate()
    {
        $cols = $this->_collection->info('cols');
        foreach ($cols as $col) {
            $this->_data[$col] = null;
        }
        return $this;
    }

    /**
     * Загрузить сущестувющий документ
     *
     * Параметр $refresh указывает следует ли повторно производить загрузку докумета
     *
     * @param mixed $primary;
     * @param bool $refresh
     * @return Lis_Document_Abstract
     */
    public function load($primary, $refresh = false)
    {
        $pks = $this->_getPrimaryKey();
        $neadRefresh = $refresh;
        if (!is_array($primary) && count($pks) == 1) {
            $pkNames = array_keys($pks);
            $primary = array($pkNames[0] => $primary);
        }
        foreach ($pks as $pk=>$oldVal) {
            if (!isset($primary[$pk])) {
                throw new Lis_Document_Exception('Can not load document. Invalid document identifire. Missing ' . $pk);
            }
            if (null === $oldVal || $primary[$pk] != $oldVal) {
                $this->_data[$pk] = $primary[$pk];
                $neadRefresh = true;
            }
        }
        if (true === $neadRefresh) {
            foreach ($this->_data as $key=>$value) {
                if (!array_key_exists($key, $pks)) {
                    $this->_data[$key] = null;
                }
            }
            parent::refresh();
        }
        return $this;
    }

    /**
     * Записать документ в хранилище
     * @return Lis_Document_Abstract
     */
    public function save()
    {
        //TODO: модифицировать так чтобы, объекты созданные вручную (не загруженные) с указаным идентификатором пытались обновиться а не вставлялись
        parent::save();
        return $this;
    }

    /**
     * Привязать документ к определённому списку документов
     *
     * @param string $collection Имя класса колллекци документов
     * @return Lis_Document_Abstract
     */
    public function setCollection($collection = null) {
        $docClass = get_class($this);
        if (null === $collection) {
            $collection = str_replace('Document_', 'Document_Collection_', $docClass);
        }
        if ($collection !== get_class($this->_collection)) {
            $this->_collection = Lis_Document_Collection_Factory::factory($collection);
            $info = $this->_collection->info();
            $this->_primary = (array) $info['primary'];
            if ($this->_collection->getDocumentType() !== $docClass) {
                throw new Lis_Document_Exception('Invalid programming logic. Document collection ' . $collection . ' does not support documents of type ' . $docClass . '.');
            }
        }
        return $this;
    }
    /**
     * Сериализация документа
     *
     * Документ преобразуется в хэш, после чего просиходит сериализация данных
     *
     * @return string
     */
    public function getSerialization()
    {
        $data = $this->toArray();
        array_walk_recursive($data, array($this, '_convertIntToString'));
        return serialize($data);
    }
    /**
     * Получить контрольную сумму документа
     * Вычисляется MD5 сериализации объекта документа
     * @return string
     */
    public function getChecksum()
    {
        return md5($this->getSerialization());
    }
    protected function _convertIntToString(&$val)
    {
        if (is_int($val)) {
            $val = (string)$val;
        }
    }
}