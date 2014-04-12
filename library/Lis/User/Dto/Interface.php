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
 * Интерфейс загрузки/сохранения данных пользователя
 *
 * @category   Lis
 * @package    Lis_User
 * @subpackage Lis_User_Dto
 * @copyright  Copyright (c) 2009, ООО "Лаборатория информационных систем"
 * @license    Проприетарная лицензия
 * @version    $Id$
 */
interface Lis_User_Dto_Interface
{
    /**
     * Загрузить данные пользователя из хранилища
     * @param Lis_User $user
     * @return void
     */
    public function load(Lis_User $user);
    /**
     * Сохранить данные пользователя в хранилище
     * @param Lis_User $user
     * @return void
     */
    public function save(Lis_User $user);
}