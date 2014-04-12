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
 * @package    Lis_Form
 * @subpackage Decorator
 * @license    Проприетарная лицензия
 * @version    $Id$
 */

/**
 * @see Zend_Form_Decorator_ViewHelper
 */
require_once 'Zend/Form/Decorator/ViewHelper.php';

/**
 * Lis_Form_Decorator_CompositeElement - композитный декоратор элементов формы
 *
 * Декорирует все части отображения елемнтов формы: собственно елемент, метка, описание, ошибки
 *
 * @category   Lis
 * @package    Lis_Form
 * @subpackage Decorator
 * @license    Проприетарная лицензия
 * @version    $Id$
 */
class Lis_Form_Decorator_CompositeElement extends Zend_Form_Decorator_ViewHelper implements ZendX_JQuery_Form_Decorator_UiWidgetElementMarker
{
    /**
     * Рендеринг описания элемента
     *
     * Если у элемента нет описания возвращается пустая строка.
     * Иначе возвращается описание элемента,
     * обёрнутое тегом div класса form-element-description.
     * Если существует зарегистрированный переводчик, то описание будет переведено
     *
     * @return string
     */
    protected function _buildDescription()
    {
        $element = $this->getElement();
        $description = $element->getDescription();
        if (empty($description)) {
            return '';
        }
        if ($translator = $element->getTranslator()) {
            $description = $translator->translate($description);
        }
        return '<div class="form-element-description">' . $description . '</div>';
    }
    /**
     * Рендеринг ошибок, закреплённых за элементом
     *
     * Если за элементом не закреплено сообщений об ошибках, возвращается пустая строка.
     * Иначе возвращается маркированный список ошибок класса form-element-errors.
     * Если существует зарегистрированный переводчик, то описание будет переведено
     *
     * @return string
     */
    protected function _buildErrors()
    {
        $element = $this->getElement();
        $messages = $element->getMessages();
        if (empty($messages)) {
            return '';
        }
        return $element->getView()->formErrors($messages, array('class'=>'form-element-errors'));
    }
    /**
     * Рендеринг ошибок, закреплённых за элементом
     *
     * При помощи методов класса-предка Zend_Form_Decorator_ViewHelper
     * происходит получение значения, атрибутов, названия и идентификатора елемента.
     * В зависимости от бызового класса элемента добавляются дополнительные css-классы.
     * Рендеринг происходит при помощи помощника представления.
     *
     * @return string
     */
    protected function _buildInput()
    {
        $element = $this->getElement();
        $helper = $element->helper;

        $value         = $this->getValue($element);
        $attribs       = $this->getElementAttribs();
        $name          = $element->getFullyQualifiedName();
        $id            = $element->getId();
        $attribs['id'] = $id;

        $specialClass = array();
        if ($element instanceof Zend_Form_Element_Xhtml) {
            $specialClass[] = 'form-element-control input-element';
        }
        if ($element instanceof Zend_Form_Element_Text) {
            $specialClass[] = 'input-field';
            $specialClass[] = 'input-text';
        } else if ($element instanceof Zend_Form_Element_Password) {
            $specialClass[] = 'input-field';
            $specialClass[] = 'input-text';
        } else if ($element instanceof Zend_Form_Element_Textarea) {
            $specialClass[] = 'input-field';
            $specialClass[] = 'input-text';
        } else if ($element instanceof Zend_Form_Element_Select) {
            $specialClass[] = 'input-field';
            $specialClass[] = 'input-select';
        } else if ($element instanceof Zend_Form_Element_Checkbox) {
            $specialClass[] = 'input-flag';
            $specialClass[] = 'input-checkbox';
        } else if ($element instanceof Zend_Form_Element_Radio) {
            $specialClass[] = 'input-flag';
            $specialClass[] = 'input-radio';
        } else if ($element instanceof Zend_Form_Element_Button) {
            $specialClass[] = 'input-button';
        } else if ($element instanceof Zend_Form_Element_Submit) {
            $specialClass[] = 'input-button';
            $specialClass[] = 'input-submit';
        } else if ($element instanceof Zend_Form_Element_Reset) {
            $specialClass[] = 'input-button';
            $specialClass[] = 'input-reset';
        } else if ($element instanceof Zend_Form_Element_File) {
            throw new Zend_Exception('CompositeElement Decorator cant be used for file input element.');
        }
        if (isset($attribs['class'])) {
            $attribs['class'] = implode(' ', array_merge(explode(' ', $attribs['class']), $specialClass));
        } else {
            $attribs['class'] = $specialClass;
        }

        if ($element instanceof ZendX_JQuery_Form_Element_UiWidget) {
            $param = $element->getJQueryParams();
        } else {
            $param = $attribs;
        }

        $html = $element->getView()->$helper($name, $value, $param, $element->options);

        return $html;
    }
    /**
     * Рендеринг метки элемента
     *
     * Если у элемента нет метки возвращается пустая строка.
     * Иначе возвращается описание элемента полученное при помощи помощника представления,
     * обёрнутое тегом div класса form-element-label.
     * Если существует зарегистрированный переводчик, то описание будет переведено.
     *
     * @return string
     */
    protected function _buildLabel()
    {
        $element = $this->getElement();
        if (in_array($element->getType(), $this->_buttonTypes)) {
           return '';
        }
        $label = $element->getLabel();
        if ($translator = $element->getTranslator()) {
            $label = $translator->translate($label);
        }
        $class = 'form-element-label';
        if ($element->isRequired()) {
            $label .= '*';
            $class .= ' form-element-label-required';
        }
        $html = $element->getView()
                        ->formLabel($element->getName(), $label, array('class'=>$class));
        return $html;
    }
    /**
     * Рендеринг элемента
     *
     * Строится "основная" и "дополнительная" часть элемента,
     * сосавный части обвёртываются тегом div c классом form-element,
     * результат добавляется к остальному контенту в указанной позиции (в начале или конце) через указанный разделитель.
     * Если элемент или вид не зарегистрированы, то возвращается входной параметр без изменений.
     *
     * @param  string $content
     * @return string
     */
    public function render($content)
    {
        $element = $this->getElement();
        if (!$element instanceof Zend_Form_Element) {
            return $content;
        }
        if (null === $element->getView()) {
            return $content;
        }

        $separator = $this->getSeparator();
        $placement = $this->getPlacement();

        $output = '<div class="form-element">'
                . $this->_buildLabel()
                . '<div class="form-element-item">'
                . $this->_buildInput()
                . $this->_buildDescription()
                . $this->_buildErrors()
                . '</div>'
                . '</div>';
        switch ($placement) {
            case (self::PREPEND):
                return $output . $separator . $content;
            case (self::APPEND):
            default:
                return $content . $separator . $output;
        }
    }
}