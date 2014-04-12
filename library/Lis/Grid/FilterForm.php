<?php

class Lis_Grid_FilterForm extends ZendX_JQuery_Form {

    public static $htmlClass = 'ui-jqgrid-filter';

    protected $_columnModel = null;
    protected $_filters = null;

    public function __construct($options = null) {
        parent::__construct($options);
        $this->addElementPrefixPath('Lis_Form_Decorator', 'Lis/Form/Decorator', 'decorator')
                ->setDisplayGroupDecorators(array(
                    'FormElements',
                    'Fieldset',
                    array('HtmlTag', array('tag' => 'div')),
                ));
    }

    public function render(Zend_View_Interface $view = null) {
        foreach ($this->getElements() as $element) {
            if ($element instanceof Zend_Form_Element_Hidden) {
                $element->setDecorators(array('ViewHelper'));
            } else {
                $element->setDecorators(array('CompositeElement'));
            }
        }
        $сlass = $this->getAttrib('class');
        if ($сlass === null) {
            $this->setAttrib('class', self::$htmlClass);
        } else {
            if (!in_array(self::$htmlClass, explode(' ', $сlass))) {
                $сlass .= (' ' . self::$htmlClass);
                $сlass = trim($сlass);
                $this->setAttrib('class', $сlass);
            }
        }
        return parent::render();
    }

    public function loadDefaultDecorators() {
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

    public function attachToColumnModel(Lis_Grid_ColumnModel $columnModel) {
        $this->_columnModel = $columnModel;
        if($this !== $columnModel->getFilterForm()){
            $columnModel->attachFilterForm($this);
        }
        return $this;
    }

    public function detachFromColumnModel() {
        if($this->_columnModel === null) {
            return $this;
        }
        $columnModel = $this->_columnModel;
        $this->_columnModel = null;
        if($columnModel->getFilterForm() === $this){
            $columnModel->detachFilterForm();
        }
        return $this;
    }

    public function getColumnModel() {
        return $this->_columnModel;
    }

    public function attachFilterToElement($provider, $columnIndex, $filterType) {
        if ($this->_columnModel === null) {
            throw new Lis_Grid_FilterForm_Exception('Programming error. Can not attach filter to form with no grid data object.');
        }
        $element = $this->getElement($provider);
        $column = $this->_columnModel->getColumnByIndex($columnIndex);
        if($element === null) {
            throw new Lis_Grid_FilterForm_Exception('Try to set non exists field "' . $provider . '" as a grid data filter.');
        }
        if($column === null) {
            throw new Lis_Grid_FilterForm_Exception('Try to set filter to non exists column "' . $columnIndex . '"');
        }
        $this->_filters[$provider . '>>>' . $columnIndex] = array(
            'element'  => $element,
            'columnIndex'   => $columnIndex,
            'type'  => $filterType,
        );
        return $this;
    }

    public function getFilters() {
        $data = Zend_Controller_Front::getInstance()->getRequest()->getParam('filter');
        $filters = array();
        if (!is_array($data) || !$this->isValid($data)) {
            return $filters;
        }
        foreach ($this->_filters as $filterData) {
            $column = $this->_columnModel->getColumnByIndex($filterData['columnIndex']);
            $columnName = $column->getOption('name');
            $element = $filterData['element'];
            if($element->getValue() !== null) {
                $filters[] = new Lis_Grid_Filter($columnName, $filterData['type'], $element->getValue());
            }
        }
        return $filters;
    }

}