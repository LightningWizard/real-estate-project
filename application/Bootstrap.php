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
 * @package    Испытательный центр УГЦР
 * @subpackage Загрузчик
 * @copyright  Copyright (c) 2009, ООО "Лаборатория информационных систем"
 * @license    Проприетарная лицензия
 * @version    $Id$
 */

/**
 * Реализует класс загручика прложения АСУ "Испытательный центр УГЦР"
 *
 * Создаёт все объекты необходимые для обработки запроса пользователя
 *
 * @uses       Bootstrap
 * @category   Испытательный центр УГЦР
 * @package    Испытательный центр УГЦР
 * @subpackage Загрузчик
 * @copyright  Copyright (c) 2009, ООО "Лаборатория информационных систем"
 * @license    Проприетарная лицензия
 */

class Bootstrap extends Zend_Application_Bootstrap_BootstrapAbstract
{
    /**
     * Конструктор
     *
     * Вызывает стандартный коснтруктор загрузчика Zend_Application_Bootstrap_Bootstrap
     * Обявляет необхождимы для работы приложения константы
     *
     * @param  Zend_Application|Zend_Application_Bootstrap_Bootstrapper $application
     * @return void
     */
    public function __construct($application)
    {
        parent::__construct($application);

        //путь к каталогу приложения
        defined('APPLICATION_PATH')
            || define('APPLICATION_PATH',
                      dirname(__FILE__));
        //путь к пользовательским настройкам приложения
        defined('SETTINGS_PATH')
            || define('SETTINGS_PATH',
                       realpath(dirname(__FILE__) . '/../settings/'));
        //путь к каталогам языковых пактов
        defined('LANGUAGES_PATH')
            || define('LANGUAGES_PATH',
                realpath(dirname(__FILE__) . '/../languages/'));
        //путь к кактлогам утилит (бинарные исполняемые файлы, специфические для ОС сервера)
        defined('UTILITES_PATH')
            || define('UTILITES_PATH',
                realpath(dirname(__FILE__) . '/../utilites/' . (PATH_SEPARATOR == ';' ? 'windows' : 'unix') . '/'));
        //тип сборки (production, stage, test, development)
        defined('APPLICATION_ENV')
            || define('APPLICATION_ENV',
                      (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

        if ('development' == $this->getEnvironment()) {
//            FirePHP_Debug::setEnabled(true);
//            FirePHP_Debug::getGenerateTime('Bootstrap');
        }

        $this->bootstrap('Session');
    }
    /**
     * Загрузка ресурса списика прав доступа
     *
     * return Zend_Acl|null
     */
    protected function _initAcl()
    {
        $acl = new Zend_Acl();

        $this->bootstrap('User');
        $user = $this->getResource('User');
        if (null !== $user) {
            $acl->addRole(new Zend_Acl_Role('user', array($user->getRoles())));

            $this->bootstrap('AvailableResource');
            $resources = $this->getResource('AvailableResource');
            $iterator = new RecursiveIteratorIterator($resources, RecursiveIteratorIterator::SELF_FIRST);
            foreach ($iterator as $res) {
                $acl->add($res);
                foreach ($user->getRoles() as $role) {
                    if ($role->canRead(md5($res->getResourceId()))) {
                        $acl->allow('user', $res, 'read');
                    }
                    if ($role->canEdit(md5($res->getResourceId()))) {
                        $acl->allow('user', $res, 'edit');
                    }
                    if ($role->canRemove(md5($res->getResourceId()))) {
                        $acl->allow('user', $res, 'remove');
                    }
                    if ($role->canExecute(md5($res->getResourceId()))) {
                        $acl->allow('user', $res, 'execute');
                    }
                    if ($role->canExtrimalEdit(md5($res->getResourceId()))) {
                        $acl->allow('user', $res, 'extrim');
                    }
                }
            }

            if ($user->getId() === -1) {
                //set permissions for system owner
                try {
                    $acl->allow('user', 'administration', array('read'));
                } catch (Exception $e) {}
                try {
                    $acl->allow('user', 'user', array('read', 'edit'));
                } catch (Exception $e) {}
                try {
                    $acl->allow('user', 'department', array('read', 'edit', 'remove'));
                } catch (Exception $e) {}
                try {
                    $acl->allow('user', 'specialization', array('read', 'edit', 'remove'));
                } catch (Exception $e) {}
                try {
                    $acl->allow('user', 'role', array('read', 'edit', 'remove'));
                } catch (Exception $e) {}
            }

            Zend_View_Helper_Navigation_HelperAbstract::setDefaultAcl($acl);
            Zend_View_Helper_Navigation_HelperAbstract::setDefaultRole('user');
            Lis_Controller_Action_Helper_Acl::setDefaultAcl($acl);
            Lis_Controller_Action_Helper_Acl::setDefaultRole('user');
        }

        Zend_Registry::set('Zend_Acl', $acl);
        return $acl;
    }
    /**
     * Загрузка глобального доступного системе ресурса
     *
     * return RealEstate_Acl_Resource | null
     */
    protected function _initAvailableResource()
    {
        $this->bootstrap('Navigation');
        $navigation = $this->getResource('Navigation');

        $availableResource = RealEstate_Acl_Resource_Util::extractFromNavigation($navigation);
        Zend_Registry::set('availableResource', $availableResource);
        return $availableResource;
    }
    /**
     * Загрузка ресурса доступных языков
     *
     * @return array
     */
    protected function _initAvailableLanguages()
    {
        $langs = array();
        $langDirs = glob(LANGUAGES_PATH . '/*', GLOB_ONLYDIR);
        foreach ($langDirs as $dir) {
            if (count(glob($dir . '/*.mo'))) {
                $langs[] = mb_convert_case(basename($dir), MB_CASE_LOWER);
            }
        }
        return $langs;
    }
    /**
     * Загрузка ресурса БД
     *
     * @return Zend_Db_Adapter_Abstract|null
     */
    protected function _initDb()
    {
        ini_set('ibase.timestampformat', '%d.%m.%Y %H:%M:%S');
        ini_set('ibase.dateformat', '%d.%m.%Y');
        ini_set('ibase.timeformat', '%H:%M:%S');

        $this->bootstrap('SystemConfig');
        $config = $this->getResource('SystemConfig');
        $dbParams = $config->database->toArray();
        $dbParams['options'] = array(Zend_Db::CASE_FOLDING => Zend_Db::CASE_UPPER);
        $db = new ZendX_Db_Adapter_Firebird($dbParams);
        Zend_Db_Table::setDefaultAdapter($db);
        if ('development' == $this->getEnvironment()) {
//            $profiler = new FirePHP_Db_Profiler('Profiler');
//            $profiler->setEnabled(true);
//            $db->setProfiler($profiler);
        }
        return $db;
    }
    /**
     * Загрузка ресурса фронт-контроллера
     *
     * @return Zend_Controller_Front
     */
    protected function _initFrontController()
    {
        $front = Zend_Controller_Front::getInstance();
        $front->addModuleDirectory(realpath(APPLICATION_PATH . '/modules/'))
              ->setModuleControllerDirectoryName('controllers')
              ->setDefaultModule('system')
              ->setDefaultControllerName('main')
              ->setDefaultAction('main')
              ->setParams(array(
                  'prefixDefaultModule' => true,
                  'env' => $this->getEnvironment(),
              ))
              ->registerPlugin(new Lis_Controller_Plugin_Authorization())
              ->registerPlugin(new Lis_Controller_Plugin_Notifire());
        if ('development' == $this->getEnvironment()) {
            $front->throwExceptions(true)
                  ->registerPlugin(new Lis_Controller_Plugin_Debug());
        } else {
            $front->throwExceptions(false);
        }

        Zend_Controller_Action_HelperBroker::addPrefix('Lis_Controller_Action_Helper');
        return $front;
    }
    /**
     * Загрузка ресурса макета
     *
     * @return Zend_Layout
     */
    protected function _initLayout()
    {
        $this->bootstrap('FrontController');
        $layout = Zend_Layout::startMvc(array(
            'layoutPath' => APPLICATION_PATH . '/layouts/scripts',
            'layout' => 'abstract'
        ));
        return $layout;
    }
    /**
     * Загрузка ресурса локали приложения
     *
     * @return Zend_Locale
     */
    protected function _initLocale()
    {
        $this->bootstrap('AvailableLanguages');
        $langs = $this->getResource('AvailableLanguages');
        $this->bootstrap('User');
        $user = $this->getResource('User');

        $tryLangs = array();
        if (null !== $user) {
            $tryLangs[] = $user->getLanguage();
        }
        $locale = new Zend_Locale('browser');
        $tryLangs[] = $locale->getLanguage();
        $tryLangs[] = 'uk';
        $tryLangs[] = $langs[0];
        do {
            $langCode = array_shift($tryLangs);
        } while (!in_array($langCode, $langs) && count($tryLangs));
        $locale->setLocale($langCode);
        Zend_Registry::set('Zend_Locale', $locale);
        return $locale;
    }
    /**
     * Загрузка ресурса карты сайта
     *
     * @return Zend_Navigation|null
     */
    protected function _initNavigation()
    {
        //подключаем файл /config/navigation.php в котором содержиться массив $naviagtion c описанием навигационной структуры
        require_once APPLICATION_PATH . '/config/navigation.php';
        $navigation = new Zend_Navigation($navigation);
        Zend_Registry::set('Zend_Navigation', $navigation);
        return $navigation;
    }
    /**
     * Загрузка ресурса сессии и её запуск
     *
     * Загрузка ресурса хранилища данных сессии и старт сесии
     * Выполняется сразу после создания объекта загрузки
     *
     * @return void
     */
    protected function _initSession()
    {
        if ('development' == $this->getEnvironment()) {
            $sessoinsDir = APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'datatmp' . DIRECTORY_SEPARATOR . 'sessions';
            if (is_dir($sessoinsDir) || mkdir($sessoinsDir, 0777, true)) {
                Zend_Session::setOptions(array('save_path' => realpath($sessoinsDir)));
            }
        }
        Zend_Session::start();
    }
    /**
     * Загрузка пользовательской конфигурации системы
     *
     * @return Zend_Config_Ini
     * @throws Zend_Application_Bootstrap_Exception  если файл конфигурации не существует или повреждён или указаны не все параметры
     */
    protected function _initSystemConfig()
    {
        $config = new Zend_Config_Ini(SETTINGS_PATH . '/system.ini');
        //если не сконфигурировано подключение к бзаза данных - сгенерировать исключение
        if (
            null == $config->database->host ||
            null == $config->database->dbname ||
            null == $config->database->username ||
            null == $config->database->password
        ) {
            throw new Zend_Application_Bootstrap_Exception('Invalid configuration file. No database configuration detected.');
        }
        //если некоторые опции не указаны - выставлям значения по умолчанию
        if (null == $config->owner->login) {
            $config->owner->login = 'root';
        }
        if (null == $config->owner->password) {
            $config->owner->password = 'CeghtLtvjy';
        }
        if (null == $config->owner->language) {
            $config->owner->language = 'ru';
        }
        if (null == $config->backup->storage) {
            $config->backup->storage = realpath(APPLICATION_PATH . '/../database/backups');
        }
        if ('development' == $this->getEnvironment()) {
            FirePHP_Debug::debug($config);
        }
        return $config;
    }
    /**
     * Загрузка ресурса переводчика
     *
     * @return Zend_Translate
     */
    protected function _initTranslate()
    {
        $this->bootstrap('Locale');
        $locale = $this->getResource('Locale');
        $translate = new Zend_Translate(
            'Zend_Translate_Adapter_Gettext',
            LANGUAGES_PATH . '/'. $locale->getLanguage() . '/',
            $locale->getLanguage(),
            array(
                'scan' => Zend_Translate::LOCALE_DIRECTORY,
                'logUntranslated' => false
            )
        );
        Zend_Registry::set('Zend_Translate', $translate);
        return $translate;
    }
    /**
     * Загрузка ресурса текущего пользователя
     *
     * @return Lis_User|null
     */
    protected function _initUser()
    {
        $this->bootstrap('Session');
        $this->bootstrap('Db');
        $this->bootstrap('SystemConfig');

        $authorizationNamespace = new Zend_Session_Namespace('Authorization');
        if ($authorizationNamespace->legal) {
            Lis_User::setTransferer(new RealEstate_User_Dto_Db(Zend_Db_Table::getDefaultAdapter()));
            $user = Lis_User::getUserInstance($authorizationNamespace->userid, !$authorizationNamespace->isOwner);
            if ($authorizationNamespace->isOwner) {
                $user->setLastName($authorizationNamespace->username);
                $user->setLanguage($this->getResource('SystemConfig')->owner->language);
            }
        } else {
            $user = null;
        }
        Zend_Registry::set('currentUser', $user);
        return $user;
    }
    /**
     * Загрузка ресурса представления
     *
     * @return Zend_View
     */
    protected function _initView()
    {
        //Убудится, что ресурс переводчика загружен
        $this->bootstrap('Translate');
        $locale    = $this->getResource('Locale');
        $translate = $this->getResource('Translate');
        //Убудится, что ресурс макета загружен, получить объект представления
        $this->bootstrap('Layout');
        $layout = $this->getResource('Layout');
        $view = $layout->getView()->addHelperPath('Lis/View/Helper/');

        //Настроить объект представления
        $view->setEncoding('utf-8')
             ->addHelperPath('Lis/View/Helper/', 'Lis_View_Helper')                    //помощники вида для компонент разработанныз ООО ЛИС
             ->addHelperPath('Reestr/View/Helper/', 'Reestr_View_Helper')                    //помощники вида для компонент разработанныз ООО ЛИС
             ->addHelperPath('ZendX/JQuery/View/Helper/', 'ZendX_JQuery_View_Helper');  //помощники вида для компонент пакета работы с jQuery
        $view->doctype('XHTML1_FRAMESET');
        $view->headMeta()
             ->appendHttpEquiv('expires', 'Wed, 26 Feb 1997 08:21:57 GMT')
             ->appendHttpEquiv('pragma', 'no-cache')
             ->appendHttpEquiv('Cache-Control', 'no-cache');
        $view->headTitle($translate->translate('ApplicationTitleLong'))
             ->headTitle()->setSeparator(' :: ');
        $view->headLink()
             ->headLink(
                array(
                    'rel' => 'favicon',
                    'href' => '/favicon.ico'
                ),
                'PREPEND'
             )
             ->appendStylesheet('/css/reset.css')
             ->appendStylesheet('/css/jquery-ui-1.8.16.custom.css')
             ->appendStylesheet('/css/jquery.ui.extended.css')
             ->appendStylesheet('/css/jquery.ui.jqgrid.css')
             ->appendStylesheet('/css/main.css');
        //$view->headStyle()
        $view->headScript()->appendFile('/js/jquery-1.7.2.min.js', 'text/javascript');                                             //основная библиотека jQuery (http://jquery.com)
        $view->headScript()->appendFile('/js/jquery-ui-1.8.21.custom.min.js', 'text/javascript');                                          //библиотека UI jQuery (http://jqueryui.com)
        if ('en' != $locale->getLanguage()) {
            $view->headScript()->appendFile('/js/datepicker/ui.datepicker.' . $locale->getLanguage() . '.js', 'text/javascript');
        }
        $view->headScript()->appendFile('/js/jquery.ui.extended.js', 'text/javascript');                                 //дополненния к библиотеке UI jQuery (LISSoft)
        $view->headScript()->appendFile('/js/jquery.checktree.js', 'text/javascript');                                //плагин-виджет jQuery дерева выбора (http://static.geewax.org/checktree/index.html)
        $view->headScript()->appendFile('/js/jqgrid/i18n/grid.locale-' . $locale->getLanguage() . '.js', 'text/javascript');  //языковая библиотека плагина jQuery jqGrid (http://www.trirand.com/blog/)
        //$view->headScript()->appendFile('/js/jqgrid/jqGrid.js', 'text/javascript');                                   //плагин jQuery jqGrid (http://www.trirand.com/blog/)
        $view->headScript()->appendFile('/js/jqgrid/jquery.jqGrid.src.js', 'text/javascript');
        $view->headScript()->appendScript('$.jgrid.defaults.rowNum = -1;');
        $view->headScript()->appendFile('/js/jquery.layout.js', 'text/javascript');                                      //плагин jQuery для работы с макетами (http://layout.jquery-dev.net/index.html)
        $view->headScript()->appendFile('/js/jquery.maskedinput-1.3.min.js', 'text/javascript');                                      //плагин jQuery для работы с макетами (http://layout.jquery-dev.net/index.html)
        $view->headScript()->appendFile('/js/jquery.form.js', 'text/javascript');                                        //плагин jQuery для работы с формами (http://malsup.com/jquery/form/)
        $view->headScript()->appendFile('/js/defaultPage.js', 'text/javascript');                                        //плагин jQuery для работы с формами (http://malsup.com/jquery/form/)
        //плагин jQuery для отображения сообщений в консоли встроенной в страницу
        if ('development' == $this->getEnvironment()) {
            $view->headScript()->appendFile('/js/jquery._trace(enable).js', 'text/javascript');
        } else {
            $view->headScript()->appendFile('/js/jquery._trace(disable).js', 'text/javascript');
        }
        $view->headScript()->appendFile('/js/jquery.bgiframe.js', 'text/javascript', array('conditional' => 'lte IE 7'));//плагин jQuery для корректной прорисовки слоёв (http://plugins.jquery.com/project/bgiframe)
        //$view->headScript()->appendFile('/js/jquery.ifixpng2.js', 'text/javascript', array('conditional' => 'lte IE 7'));//плагин jQuery для корректной прорисовки PNG (http://plugins.jquery.com/project/iFixPng2)
        //$view->inlineScript();
        /*
         * Внимание!
         * Помощник jQuery должен рендерить только обработку события загрузки страницу
         * Работу с остальными частями следует выполнять через стандартные помощники
         *
         */
        $view->jQuery()
             ->addOnLoad('$(":input").attr("autocomplete", "off");')
             ->setRenderMode(ZendX_JQuery::RENDER_JQUERY_ON_LOAD)
             ->enable();

        $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
        $viewRenderer->setView($view);
        Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
        return $view;
    }
    /**
     * Запуск приложения
     *
     * Проверка установлен ли контролллер по умолчанию. Если нет - выброс исключения
     *
     * Если фронт-контролллер настроен правильно, загрузчик регистрируется в параметре 'bootstrap'
     * фронт-контролллера, после чего начинается диспетчиризация фронт-контролллера
     *
     * @return void
     * @throws Zend_Application_Bootstrap_Exception
     */
    public function run()
    {
        if ('development' == $this->getEnvironment()) {
            FirePHP_Debug::getGenerateTime('Start');
        }

        $front   = $this->getResource('FrontController');
        if (null === $front->getControllerDirectory($front->getDefaultModule())) {
            throw new Zend_Application_Bootstrap_Exception(
                'No default controller directory registered with front controller'
            );
        }

        $front->setParam('bootstrap', $this);

        $front->dispatch();
    }
}
