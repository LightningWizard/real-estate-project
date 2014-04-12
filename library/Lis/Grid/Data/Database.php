<?php

abstract class Lis_Grid_Data_Database extends Lis_Grid_Data_Abstract{
    protected $_conditions = array();

    public function addCondition($condition) {
        $this->_conditions[] = $condition;
        return $this;
    }

    public function getConditions() {
        $conditions = array();
        foreach ($this->_filters as $filter) {
            $conditions[] = $this->_getCondition($filter);
        }
        foreach ($this->_conditions as $condition) {
            array_push($conditions, $condition);
        }
        return $conditions;
    }

    public function resetMemory() {
        parent::resetMemory();
        $this->_conditions = array();
    }

    protected function _getCondition(Lis_Grid_Filter $filter) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $filterColumn = $filter->getFilterColumn();
        $filterValue = $filter->getFilterValue();
        switch ($filter->getFilterType()) {
            case Lis_Grid_Filter::TYPE_EQUAL:
                $cond = '=?';
                break;
            case Lis_Grid_Filter::TYPE_LIKE:
                $cond = "LIKE ?";
                $filterValue = "%" . $filterValue . "%";
                break;
            case Lis_Grid_Filter::TYPE_LESS:
                $cond = "<?";
                break;
            case Lis_Grid_Filter::TYPE_GREATER:
                $cond = ">?";
                break;
            case Lis_Grid_Filter::TYPE_NOTEQUAL:
                $cond = "!= ?";
                break;
            case Lis_Grid_Filter::TYPE_NOTLIKE:
                $filterValue = "%" . $filterValue . "%";
                $cond = "NOT LIKE ?";
                break;
            case Lis_Grid_Filter::TYPE_EQUAL | Lis_Grid_Filter::TYPE_LESS:
                $cond = "<=?";
                break;
            case Lis_Grid_Filter::TYPE_EQUAL | Lis_Grid_Filter::TYPE_GREATER:
                $cond = ">=?";
                break;
            case Lis_Grid_Filter::TYPE_CONTAIN:
                $cond = "IN (?)";
                $filterValue = (array) $filterValue;
                break;
            case Lis_Grid_Filter::TYPE_NOTCONTAIN:
                $cond = "NOT IN (?)";
                $filterValue = (array) $filterValue;
                break;
            default:
                throw new Lis_Grid_Filter('Unknown filter type');
        }

        $statement = $db->quoteInto($db->quoteIdentifier($filterColumn) . $cond, $filterValue);
        return $statement;
    }
}