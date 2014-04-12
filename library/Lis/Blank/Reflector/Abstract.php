<?php
/* ЛИЦЕНЗИЯ
 *
 * Все права на данный программный продукт принадлежат ООО "Лаборатория информационных систем"
 * Ни одна часть данного програмного продукта не может использоватся без разрешения правообладателя
 *
 * @category   Lis
 * @package    Lis_Blank
 * @subpackage Lis_Blank_Reflector
 * @copyright  Copyright (c) 2009, ООО "Лаборатория информационных систем"
 * @license    Проприетарная лицензия
 * @version    $Id$
 */

/**
 * @see Lis_Blank_Reflector_Exception
 */
require_once 'Lis/Blank/Reflector/Exception.php';

/**
 * Абстрактный класс ортажателя данных между формулярами и документоами
 *
 * @category   Lis
 * @package    Lis_Blank
 * @subpackage Lis_Blank_Reflector
 * @copyright  Copyright (c) 2009, ООО "Лаборатория информационных систем"
 * @license    Проприетарная лицензия
 * @version    $Id$
 */
abstract class Lis_Blank_Reflector_Abstract
{
    /**
     *
     * @var Lis_Blank_Abstract
     */
    protected $_blank;
    /**
     *
     * @var string
     */
    protected $_blankField = null;
    /**
     *
     * @var  Lis_Document_Abstract
     */
    protected $_document;
    /**
     *
     * @var string
     */
    protected $_documentField = null;
    /**
     * Конструктор
     *
     * @param Lis_Blank_Abstract $blank Вызвавшиий формуляр
     * @param string $blankFieldName Поле вызвавшего формуляра
     * @param Lis_Document_Abstract $document Связанный с формуляром документ
     * @param string $documentFieldName Поле докумета связанное с полем формуляра
     */
    public function __construct(Lis_Blank_Abstract $blank, $blankFieldName, Lis_Document_Abstract $document, $documentFieldName)
    {
        $this->setBlank($blank, $blankFieldName);
        $this->setDocument($document, $documentFieldName);
    }
    /**
     * Установить формуляр и его поле
     *
     * @param Lis_Blank_Abstract $blank Вызвавшиий формуляр
     * @param string $field Поле вызвавшего формуляра
     */
    public function setBlank($blank, $field)
    {
        $this->_blank = $blank;
        $this->_blankField = $field;
    }
    /**
     * Установить документ и его поле
     *
     * @param Lis_Document_Abstract $document Связанный с формуляром документ
     * @param string $field Поле докумета связанное с полем формуляра
     */
    public function setDocument($document, $field)
    {
        $this->_document = $document;
        $this->_documentField = $field;
    }
    /**
     * Преобразовать данные поля формуляра в данные поля документа
     *
     * @param mixed $value Данные поля формуляра
     * @return mixed Данные поля документа
     */
    abstract protected function _b2d($value);
    /**
     * Преобразовать данные поля документа в данные поля формуляра
     *
     * @param mixed $value Данные поля документа
     * @return mixed Данные поля формуляра
     */
    abstract protected function _d2b($value);
    /**
     * Выполнить преобразование данных
     *
     * Если параметр $fromDocumentToBlank имеет значение
     *      ИСИТНА - преобразовать данные документа в данные формы @see _export()
     *      ЛОЖЬ   - преобразовать данные формы в данные документа @see _import()
     * @param bool $fromDocumentToBlank
     */
    public function transform($fromDocumentToBlank = true)
    {
        if ($fromDocumentToBlank) {
            $this->_blank->getElement($this->_blankField)->setValue(trim($this->_d2b($this->_document->{$this->_documentField})));
        } else {
            $this->_document->{$this->_documentField} = $this->_b2d($this->_blank->getElement($this->_blankField)->getValue());
        }
    }
}