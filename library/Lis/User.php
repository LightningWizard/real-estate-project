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
 * @copyright  Copyright (c) 2009, ООО "Лаборатория информационных систем"
 * @license    Проприетарная лицензия
 * @version    $Id$
 */

/**
 * Класс пользователя системы.
 *
 * Предназначен для инкапсуляции данных и поведения пользователя, обюращающегося к системе
 * Для управления пользователями и т.п. следует использовать документы карточек профиля пользователя и бланки их редактирования
 *
 * Класс является помесью реализации паттерна "Одиночка" и "Абстрактная фабрика"
 * У всех пользователей есть идентификатор. Статические методы класса обеспечивают создание единственного экземпляра объекта пользователя с идентификатором
 * Также при помощи статических методов класс конфигурируется объектом обмена данными ("загрузчик параметров")
 *
 * @category   Lis
 * @package    Lis_User
 * @copyright  Copyright (c) 2009, ООО "Лаборатория информационных систем"
 * @license    Проприетарная лицензия
 * @version    $Id$
 */
class Lis_User
{
    /**
     * Массив пользователей
     * @var array
     */
    static protected $_users = array();
    /**
     * Объект, предоставляющий возможность загрузки параметров пользователя
     * @var Lis_User_Dto_Interface
     */
    static protected $_transferer;
    /**
     * Инстанциировать объект пользователя
     *
     * Фабричный метод создания объекта пользователя с идентификатором $uid, если
     * установлен флаг $loadData, то при также будут загружены данные обекта пользователя
     *
     * @param mixed $uid
     * @param bool $loadData
     * @return Lis_User
     */
    static public function getUserInstance($uid, $loadData = true)
    {
        if (!isset(self::$_users[$uid]) || null === self::$_users[$uid]) {
            $user = new self($uid);
            if ($loadData) {
                $user->loadData();
            }
            self::$_users[$uid] = $user;
        }
        return self::$_users[$uid];
    }
    /**
     * Установить объект обмена данными с хранилищем
     *
     * Инкапсулирует стратегию загрузки данных пользователя
     *
     * @param Lis_User_Dto_Interface $transferer
     */
    static public function setTransferer(Lis_User_Dto_Interface $transferer)
    {
        if (null === $transferer)
            throw new Exception('Try to set null as user data transfer object');
        self::$_transferer = $transferer;
    }
    /**
     * Получить объект обмена данными
     * @return Lis_User_Dto_Interface
     */
    static public function getTransferer()
    {
        return self::$_transferer;
    }

    /**
     * Идентификатор пользователя
     * @var mixed
     */
    protected $_uid;
    /**
     * Имя пользователя
     * @var string
     */
    protected $_firstName;
    /**
     * Отчество пользователя
     * @var string
     */
    protected $_secondName;
    /**
     * Фамилия пользователя
     * @var string
     */
    protected $_lastName;
    /**
     * Предпочтительный язык GUI
     *
     * Указание кода языка либо 'auto', либо 'browser'
     * @var string
     */
    protected $_language = 'auto';
    protected $_roles = array();

    /**
     * Конструктор
     * @param mixed $uid
     */
    protected function __construct($uid)
    {
        $this->_uid = $uid;
    }
    /**
     * Установить имя пользователя
     * @param string $firstName
     * @return Lis_User
     */
    public function setFirstName($firstName)
    {
        $this->_firstName = $firstName;
        return $this;
    }
    /**
     * Установить отчество пользователя
     * @param string $secondName
     * @return Lis_User
     */
    public function setSecondName($secondName)
    {
        $this->_secondName = $secondName;
        return $this;
    }
    /**
     * Установить фамилию пользователя
     * @param string $lastName
     * @return Lis_User
     */
    public function setLastName($lastName)
    {
        $this->_lastName = $lastName;
        return $this;
    }
    /**
     * Устанвоить предпочтительный язык GUI для пользователя
     *
     * @param string $language
     * @return Lis_User
     */
    public function setLanguage($language)
    {
        $this->_language = $language;
        return $this;
    }
    /**
     * Получить идентификатор пользователя
     * @return mixed
     */
    public function getId()
    {
        return $this->_uid;
    }
    /**
     * Получить имя пользователя
     * @return string
     */
    public function getFirstName()
    {
        return $this->_firstName;
    }
    /**
     * Получить отчество пользователя
     * @return string
     */
    public function getSecondName()
    {
        return $this->_secondName;
    }
    /**
     * Получить фамилию пользователя
     * @return string
     */
    public function getLastName()
    {
        return $this->_lastName;
    }
    /**
     * Получить полное имя пользователя в отформатированном виде
     *
     * Формат задаётся символами при этом
     * F - Имя пользователя
     * f - Первая буква имени и символ "." (точка)
     * S - Отчестов пользователя
     * s - Первая буква отчества пользователя и символ "."
     * L - Фамилия пользователя
     * l - Первая буква фамилии ползователя
     *
     * Пример: 'LFS' => Иванов Павел Сидорович
     *         'fsL' => П. С. Иванов
     *
     * @param string $format
     * @return string
     */
    public function getFormatedName($format = 'LFS')
    {
        $result = array();
        if (preg_match('/^([fls])?([fls])?([fls])?$/is', $format, $format)) {
            for ($i = 1; $i < count($format); $i++) {
                switch ($format[$i]) {
                    case 'F':
                        $result[] = $this->_firstName;
                        break;
                    case 'S':
                        $result[] = $this->_secondName;
                        break;
                    case 'L':
                        $result[] = $this->_lastName;
                        break;
                    case 'f':
                        $result[] = $this->_getShort($this->_firstName);
                        break;
                    case 's':
                        $result[] = $this->_getShort($this->_secondName);
                        break;
                    case 'l':
                        $result[] = $this->_getShort($this->_lastName);
                        break;
                }
            }
        } else {
            throw new Exception(sprintf('Unknown format %s', $format));
        }
        return join(' ', $result);
    }
    /**
     * Получить код предпочтительного языка
     * @return string
     */
    public function getLanguage()
    {
        return $this->_language;
    }
    /**
     * Получить сокращённую часть имени
     * @param string $partName
     * @return string
     */
    private function _getShort($partName)
    {
        return mb_substr($partName, 0, 1, 'utf8') . '.';
    }
    public function setRoles(array $roles)
    {
        $this->_roles = $roles;
        return $this;
    }
    public function getRoles()
    {
        return $this->_roles;
    }
    /**
     * Загрузить данные пользователя
     *
     * Выполенние делегируется объекту обмена данными
     *
     * @return Lis_User
     */
    public function loadData()
    {
        self::$_transferer->load($this);
        return $this;
    }
}