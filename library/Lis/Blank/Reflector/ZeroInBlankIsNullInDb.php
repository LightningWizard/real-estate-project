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
 * @see Lis_Blank_Reflector_Abstract
 */
require_once 'Lis/Blank/Reflector/Abstract.php';

/**
 * Класс отражателя данных "NULL значение БД"
 * Данные в обоих направлениях передаются без изменений
 *
 * Из БД данные передаются без изменений
 * При записи в БД, если значение отправленное клиентом можна интерпретировать как пустое, то в БД устанавливается NULL
 *
 * @category   Lis
 * @package    Lis_Blank
 * @subpackage Lis_Blank_Reflector
 * @copyright  Copyright (c) 2009, ООО "Лаборатория информационных систем"
 * @license    Проприетарная лицензия
 * @version    $Id$
 */
class Lis_Blank_Reflector_ZeroInBlankIsNullInDb extends Lis_Blank_Reflector_Abstract
{
    /**
     * Преобразовать данные поля формуляра в данные поля документа
     *
     * Значение передаётся без изменения
     *
     * @param mixed $value Данные поля формуляра
     * @return mixed Данные поля документа
     */
    protected function _d2b($value)
    {
        return $value;
    }
    /**
     * Преобразовать данные поля документа в данные поля формуляра
     *
     * Если значение можно интерпретировать как пустое (справедливо $value == false), то значение заменяется на NULL
     *
     * @param mixed $value Данные поля документа
     * @return mixed Данные поля формуляра
     */
    protected function _b2d($value)
    {
        return !$value ? null : $value;
    }

    /**
     * Выполнить преобразование данных
     *
     * Если параметр $fromDocumentToBlank имеет значение
     *      ИСТИНА - преобразовать данные документа в данные формы @see _export()
     *      ЛОЖЬ   - преобразовать данные формы в данные документа @see _import()
     * В документе может не существовать нужного поля.
     * Когда перевожу из документа в бланк, то проверяю существование в документе запрашиваемого поля
     * Когда перевожу из бланка в документ, то проверяю существование в документе поля, куда заношу значение
     * Поле бланка не проверяю, потому что его можно настроить в бланке, в ->update();
     * @param bool $fromDocumentToBlank
     */
    public function transform($fromDocumentToBlank = true)
    {
        if ($fromDocumentToBlank) {
            if(isset($this->_document->{$this->_documentField})){
                $value = trim($this->_d2b($this->_document->{$this->_documentField}));
            } else {
                $value = null;
            }
            $this->_blank->getElement($this->_blankField)->setValue($value);
        } else {
            $cols = $this->_document->getCollection()->info('cols');
            if(in_array($this->_documentField, $cols)){
                $this->_document->{$this->_documentField} = $this->_b2d($this->_blank->getElement($this->_blankField)->getValue());
            } else {
                // ничего не делаю, поля для сохранения данных в таблице нету. Введенные в форму ДАННЫЕ ТЕРЯЮТЬСЯ!
            }
        }
    }
}