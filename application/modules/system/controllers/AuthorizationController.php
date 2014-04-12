<?php
/**
 * Испытательный центр УГЦР
 *
 * ЛИЦЕНЗИЯ
 *
 * Все права на данный программный продукт принадлежат ООО "Лаборатория информационных систем"
 * Ни одна часть данного програмного продукта не может использоватся без разрешения правообладателя
 *
 * @category   Испытательный центр УГЦР
 * @package    Системный модуль
 * @subpackage Авторизация
 * @copyright  Copyright (c) 2009, ООО "Лаборатория информационных систем"
 * @license    Проприетарная лицензия
 * @version    $Id$
 */

/**
 * Реализует класс контролллера действий
 *
 * Создаёт все объекты необходимые для обработки запроса пользователя
 *
 * @uses       Zend_Controller_Action
 * @category   Испытательный центр УГЦР
 * @package    Системный модуль
 * @subpackage Авторизация
 * @copyright  Copyright (c) 2009, ООО "Лаборатория информационных систем"
 * @license    Проприетарная лицензия
 */
class System_AuthorizationController extends Zend_Controller_Action
{
    /**
     * Геттер формы авторизации
     *
     * Создание объекта формы авторизации
     *
     * @return Zend_Form
     */
    protected function _getAuthorizationForm()
    {
        $form = new Zend_Form(array('disableLoadDefaultDecorators' => true));
        $form->setMethod('post');

        $username = $form->createElement('text', 'username', array('disableLoadDefaultDecorators' => true));
        $username->setRequired(true);
        $username->setLabel('User');

        $password = $form->createElement('password', 'password', array('disableLoadDefaultDecorators' => true));
        $password->setRequired(true);
        $password->setLabel('Password');

        $submit = $form->createElement('submit', 'login', array('disableLoadDefaultDecorators' => true));
        $submit->setLabel('Login');
        $form->addElement($username)
             ->addelement($password)
             ->addElement($submit);

        $form->clearDecorators()
             ->addDecorator('FormElements')
             ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'form-elements'))
             ->addDecorator('Form')
             ->addElementPrefixPath('Lis_Form_Decorator', 'Lis/Form/Decorator', 'decorator')
             ->setElementDecorators(array('CompositeElement'));
        return $form;
    }
    /**
     * Авторизация
     * Обработчик действия авторизации
     */
    public function loginAction()
    {
        $this->view->layout()->setLayout('abstract');
        $request = $this->getRequest();
        if (!$request->isPost()) {
            //это не запрос с попыткой авторизации, выводим форму
            $this->view->form = $this->_getAuthorizationForm();
            $this->_helper->ActionStack('desktop', 'main');
            $this->render('login');
            return;
        }
        $form = $this->_getAuthorizationForm();
        if (!$form->isValid($_POST)) {
            //проверка на корректность заполнения (синтаксическая) не пройдена, выводим форму снова
            $this->view->form = $form;
            return $this->render('login');
        }
        $values = $form->getValues();
        
        $authorizationNamespace = new Zend_Session_Namespace('Authorization');
        $authorizationNamespace->userid = null;
        $authorizationNamespace->username = $values['username'];
        $authorizationNamespace->password = $values['password'];
        $authorizationNamespace->legal = false;
        $authorizationNamespace->isOwner = false;
        $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
        $authAdapter->setTableName('TBL_SEC_USER_LIST')
                    ->setIdentityColumn('USER_ACCOUNT')
                    ->setCredentialColumn('USER_PASSWORD')
                    ->setCredentialTreatment('? and USER_ISACTIVE=1')
                    ->setIdentity($values['username'])
                    ->setCredential($values['password']);
        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($authAdapter);
        if ($result->isValid()) {
            //пользователь авторизован, переадрисовываем на главную страницу
            $authorizationNamespace->userid = $authAdapter->getResultRowObject('USER_ID')->USER_ID;
            $authorizationNamespace->legal = true;
            $authorizationNamespace->isOwner = false;
            $this->_redirect('/');
            return;
        } else {
            //учётная запись не найдена в БД
            $owner = $this->getFrontController()->getParam('bootstrap')->getResource('SystemConfig')->owner;
            if (!empty($owner->login) && !empty($owner->password) && $owner->login == $values['username'] && $owner->password == $values['password']) {
                //сеанс работы с приложением под учётной записью "демона" системы, переадресация на главную страницу
                $authorizationNamespace->userid = -1;
                $authorizationNamespace->legal = true;
                $authorizationNamespace->isOwner = true;
                $this->_redirect('/');
                return;
            }
            //авторизация не пройдена, выясняем причину провала и снова выводим форму с указанием ошибки (семантической)
            if ($result->getCode() === Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND) {
                $usernameElement = $form->getElement('username');
                $usernameElement->addError('Unknown user name!');
            } else if ($result->getCode() === Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID) {
                $passwordElement = $form->getElement('password');
                $passwordElement->addError('Wrong password! Check Your language and CapsLok and try again.');
            }
            $this->view->form = $form;
            $this->render('login');
            return;
        }
    }
    /**
     * Выход
     * Обработчик действия завершения сианса работы с приложением
     */
    public function logoutAction()
    {
        //закрыть сессию
        Zend_Session::destroy();
        //переадресовать на главную страницу, т.е. на форму авторизации (сессия будет не авторизованаы)
        $this->_redirect('/');
    }
 }