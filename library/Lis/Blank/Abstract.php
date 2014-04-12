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
 * @package    Lis_Blank
 * @copyright  Copyright (c) 2009, ООО "Лаборатория информационных систем"
 * @license    Проприетарная лицензия
 * @version    $Id$
 */

/**
 * @see ZendX_JQuery_Form
 */
require_once 'ZendX/JQuery/Form.php';

/**
 * Абстрактный класс формуляра для редактирования документов
 *
 * @category   Lis
 * @package    Lis_Blank
 * @copyright  Copyright (c) 2009, ООО "Лаборатория информационных систем"
 * @license    Проприетарная лицензия
 * @version    $Id$
 */
 class Lis_Blank_Abstract extends ZendX_JQuery_Form
 {
     /**
      * Ассоциативный массив с прикреплёнными к бланку документами
      *
      * @var hash of Lis_Document_Abstract
      */
     protected $_documents = array();
     /**
      * Описание связи между элементами формуляра и полями документов
      *
      * Каждая связь представляет собой ассоцитаивный массив.
      * В этом массиве указываються связь между полями формы и документа
      * в виде array(поле формы, поледокумента) и имя класса преобразователя.
      * Значения должны быть обёрнуты в массивы.
      * Поле документа задаётся в формате array(документ, поле).
      * Преобразователь можно не указывать. В таком случае будеть использоваться перобразователь эквивалентности
      *
      * @var array of hash
      */
     protected $_reflactions = array();
     /**
      * Максимальные длины полей таблицы связанной с формой
      *
      * Почти каждая форма имеет в базе свою таблицу, в которую записываються значения из формы.
      * Элементы массива - числа, количество символов для каждого поля таблицы, которое можно передавать в базу без ошибок
      * Используеться для ограничений input и textarea: Zend_Form_Element_Text('name')->setAttrib('maxlength',$lengths['field'])
      * Массив вынесен сюда чтобы при вызове self::getLengthFields() не запрашивать информацию из базы, а брать ее отсюда
      *
      * @var array of integer
      */
     protected $_lengthFields = array();
     /**
      * Default params for use for ZendX_JQuery_Form_Element_DatePicker
      * @var array
      */
     protected $_datepickerParams = array(
                    'showOn'          => 'button',
                    'buttonImage'     => '/img/icons/16x16/calendar.gif',
                    'buttonImageOnly' => true,
                    'dateFormat'      => 'dd.mm.yy',
     );

     /**
      * Конструктор бланка
      *
      * Вызывает констурктор родителя и регестрирурет дополнительные декораторы елементов управления
      *
      * @param array $options
      */
     public function  __construct($options = null)
     {
         parent::__construct($options);
         $this->addElementPrefixPath('Lis_Form_Decorator', 'Lis/Form/Decorator', 'decorator')
              ->setDisplayGroupDecorators(array(
                'FormElements',
                array(array('ElementsWrapper'=>'HtmlTag'), array('tag' => 'div', 'class' => 'form-elements')),
                'Description',
                'Fieldset',
                array('HtmlTag', array('tag' => 'div', 'class' => 'fieldset-wrapper')),
              ));
     }
     /**
      * Устанавливает декораторы бланка по умолчанию
      * @return void
      */
     public function loadDefaultDecorators()
     {
         if ($this->loadDefaultDecoratorsIsDisabled()) {
             return;
         }

         $decorators = $this->getDecorators();
         if (empty($decorators)) {
             $this->addDecorator('FormElements')
                  ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'form-elements'))
                  ->addDecorator('Form');
         }
     }
     /**
      * Синхронизовать формуляр и документы.
      *
      * Параметр $blankWithDocuments указывает что с чем следует синхронизовать
      *
      * @param boll $blankWithDocuments
      * @return Lis_Blank_Abstract
      */
     public function update($blankWithDocuments = true)
     {
        if (!$blankWithDocuments) {
            foreach ($this->_documents as $name=>$doc) {
                if ($doc->getChecksum() != $this->getElement('__' . $name . 'Hash__')->getValue()) {
                    throw new Lis_Blank_Exception('UnsynchronizedDocumentUpdatingException');
                }
            }
        }
        foreach ($this->_reflactions as $reflection) {
            try {
                $reflector = isset($reflection['reflector']) ? $reflection['reflector'] : 'Lis_Blank_Reflector_Equal';
                require_once str_replace('_', DIRECTORY_SEPARATOR, $reflector) . '.php';
                $reflector = new $reflector($this, $reflection['connect'][0], $this->_documents[$reflection['connect'][1][0]], $reflection['connect'][1][1]);
                $reflector->transform($blankWithDocuments);
            } catch (Exception $e) {
                throw new Lis_Blank_Exception(
                    'Data reflection failed. ' .
                    'Blank Field: ' . @$reflection['connect'][0] . '; ' .
                    'Document: ' . @$reflection['connect'][1][0] . ' type of ' . get_class(@$this->_documents[@$reflection['connect'][1][0]]) . '; ' .
                    'Document Field: '  . @$reflection['connect'][1][1] . '; ' .
                    $e->getMessage()
                );
            }
        }

        if ($blankWithDocuments) {
            foreach ($this->_documents as $name=>$doc) {
                $this->getElement('__' . $name . 'Hash__')->setValue($doc->getChecksum());
            }
        }
        return $this;
     }
     /**
      * Сохранить все изменения документа(ов) в хранилище
      * @return Lis_Blank_Abstract
      */
     public function commit()
     {
         foreach ($this->_documents as $name=>$doc) {
            $doc->save();
         }
         return $this;
     }
     /**
      * Присоеденить документ к бланку
      *
      * Добавляет новый документ к бланку,
      * при этом добавляется новое скрытое поле для отслеживания изменения документа
      * при обработке других запросов во время его редактирования
      *
      * @param Lis_Document_Abstract $document
      * @param string $name
      * @return Lis_Blank_Abstract
      */
     public function addDocument(Lis_Document_Abstract $document, $name)
     {
         if (!($document instanceof Lis_Document_Abstract)) {
             throw new Lis_Blank_Exception('Document attachment failed. Invalid poramming logic. Document object must be instance of Lis_Document_Abstract');
         }
         if (isset($this->_documents[$name])) {
             throw new Lis_Blank_Exception('Document attachment failed. Document with name "' . $name . '" alraedy exists.');
         }
         $this->_documents[$name] = $document;
         $dochash = new Zend_Form_Element_Hidden('__' . $name . 'Hash__');
         $dochash->setValue($document->getChecksum())
                 ->clearDecorators()
                 ->setDecorators(array('ViewHelper'));
         $this->addElement($dochash);
         return $this;
     }

     /**
      * Добавляет список документов
      *
      * Список должен передаваться в виде хэша с ключами 'name' и 'document'
      * В конечном счёте всё сводится к вызову @see Lis_Blank_Abstract::addDocument() для каждого элемента спсика
      * @todo Рассмотеть возможность передавать хэш с ключами - именами и данными - объектами документа
      * @todo Выбрасывать исключения при не правильном формате входных данных
      *
      * @param array $documents
      * @return Lis_Blank_Abstract
      */
     public function addDocuments(array $documents)
     {
         foreach ($documents as $document) {
             $this->addDocument($document['document'], $document['name']);
         }
         return $this;
     }
     /**
      * Устанвливает список связанных с бланком документов
      *
      * Шаблонный метод для вызова операции
      * очистки @see Lis_Blank_Abstract::clearDocuments() и
      * добавления @see Lis_Blank_Abstract::addDocuments($documents) списка докуентов, связанных с бланком
      *
      * @param array $documents
      * @return Lis_Blank_Abstract
      */
     public function setDocuments(array $documents)
     {
         $this->clearDocuments();
         $this->addDocuments($documents);
         return $this;
     }
     /**
      * Получить документ
      *
      * Возвращает объект документа, привязанного к документоу под именем $name
      * @todo Следует добавить публичный метод проверки существования документа с указанным именем и при попытке получить не существующий документ выбрасывать исключение
      *
      * @param string $name
      * @return Lis_Document_Abstract | null
      */
     public function getDocument($name)
     {
        if (array_key_exists($name, $this->_documents)) {
            return $this->_documents[$name];
        }
        return null;
     }
     /**
      * Получить список документов
      *
      * Возвращает хэш, ключи которого указывают имя, под которым документ связан с бланком, а данные которого - собственно объекты документов
      *
      * @return array
      */
     public function getDocuments()
     {
        return $this->_documents;
     }
     /**
      * Удалить из бланка документ
      *
      * Убирает документ с именем $name из списка связанніх с бланком документов
      *
      * @param string $name
      * @return Lis_Blank_Abstract
      */
     public function removeDocument($name)
     {
        $name = (string) $name;
         if (isset($this->_documents[$name])) {
            unset($this->_documents[$name]);
            $this->removeElement('__' . $name . 'Hash__');
         }
         return $this;
     }
     /**
      * Очистить список документов
      * Очищает список связанных с бланокм документов
      * @return Lis_Blank_Abstract
      */
     public function clearDocuments()
     {
        foreach ($this->_documents as $name=>$doc) {
            $this->removeElement('__' . $name . 'Hash__');
        }
        $this->_documents = array();
        return $this;
     }


    /**
     * Получить для каждого поля таблицы, привязанной к документу,
     * максимально допустимую длинну сроки, которая запишеться в поле таблицы без ошибок
     * Если не передавать название таблицы, то:
     *  она загружаеться из соответствующего класса коллекции (TestCenter_Document_Collection_<docname>);
     *  результат сохраниться для повторного использования в $this->_lengthFields.
     * Используеться для ограничений input: Zend_Form_Element_Text('name')->setAttrib('maxlength',$lengths['field'])
     * Типы полей указанны только для Firebird!
     * @param string $tableName
     * @return array $lengths
     */
     public function getLengthFields($tableName = null)
     {
        if((null === $tableName) and (count($this->_lengthFields)>0)){
            $lengths = $this->_lengthFields; // Беру ранее полученные данные
        } else {
            if(null === $tableName){
                $collection_class = str_replace('_Blank_', '_Document_Collection_', get_class($this));
                $collection = new $collection_class;
                $metadata = $collection->info('metadata'); // Zend_Db_Table->info('metadata');
            } else {
                $db = Zend_Db_Table::getDefaultAdapter();
                $metadata = $db->describeTable($tableName);
            }

            $lengths = array();
            foreach($metadata as $field=>$info) {
                if(!empty($info['LENGTH'])){
                    $lengths[$field] = $info['LENGTH'];
                } else {
                    switch($info['DATA_TYPE']){
                        case "BLOB": $lengths[$field] = 32767; break;
                        case "INTEGER":  $lengths[$field] = 9; break;
                        case "SMALLINT":  $lengths[$field] = 4; break;
                        case "NUMERIC":  $lengths[$field] = intval($info['PRECISION'])+1; break; // Normal
                        #case "NUMERIC":  $lengths[$field] = intval($info['PRECISION'])-2; break; // Finance
                        case "DATE":  $lengths[$field] = 10; break;
                        case "DECIMAL":  $lengths[$field] = 19; break;
                        case "BIGINT":  $lengths[$field] = 19; break;
                        case "FLOAT":  $lengths[$field] = 7; break;
                        case "DOUBLE PRECISION": $lengths[$field] = 15; break;
                        case "TIMESTAMP": $lengths[$field] = 19; break;
                        case "TIME": $lengths[$field] = 8; break;
                        default: $lengths[$field] = 1; break;
                    }
                }
            }
            // Сохранить результат для повторного использования.
            if(null === $tableName){
                $this->_lengthFields = $lengths;
            }
        }
        return $lengths;
     }
     
    /**
     * @param array $additionalParams| null
     * @return array
     */
    public function getDatepickerParams(array $additionalParams = null){
        $additionalParams = (array) $additionalParams;
        return array_merge($this->_datepickerParams, $additionalParams);
    }

    /**
     * Рендеринг бланка
     *
     * @param Zend_View_Interface $view
     * @return string
     */
     public function render(Zend_View_Interface $view=null)
     {
         if (count($this->getDisplayGroups())) {
             $this->removeDecorator('HtmlTag');
         }
         return parent::render($view);
     }
 }