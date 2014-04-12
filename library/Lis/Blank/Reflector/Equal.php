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
 * Класс ортажателя данных "Эквивалентность"
 * Данные в обоих направлениях передются без изменений
 *
 * @category   Lis
 * @package    Lis_Blank
 * @subpackage Lis_Blank_Reflector
 * @copyright  Copyright (c) 2009, ООО "Лаборатория информационных систем"
 * @license    Проприетарная лицензия
 * @version    $Id$
 */
class Lis_Blank_Reflector_Equal extends Lis_Blank_Reflector_Abstract
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
     * Значение передаётся без изменения
     *
     * @param mixed $value Данные поля документа
     * @return mixed Данные поля формуляра
     */
    protected function _b2d($value)
    {
        return $value;
    }
}
