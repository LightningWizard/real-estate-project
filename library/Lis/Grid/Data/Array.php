<?php

class Lis_Grid_Data_Array extends Lis_Grid_Data_Abstract {

    public function attachSource($source) {
        if (!is_array($source)) {
            throw new Lis_Grid_Data_Exception('Input parametr source, must be an array');
        }
        $this->_source = $source;
        $this->resetMemory();
        return $this;
    }

    public function getRows() {
        $filteredRows = $this->getFilteredRows();
        $highLimit = $this->getHighLimit();
        if (count($this->_columnSort) > 0) {
            foreach ($this->_columnSort as $columnName => $orderType) {
                if ($orderType == self::ORDER_TYPE_ASC) {
                    $filteredRows = $this->_dataAsort($filteredRows, $columnName);
                } else {
                    $filteredRows = $this->_dataRsort($filteredRows, $columnName);
                }
            }
        }
        $this->_rowsCount = count($filteredRows);
        return array_slice($filteredRows, $this->getLowLimit(), $highLimit < $this->_rowsCount ? $highLimit : $this->_rowsCount);
    }

    public function getRowsCount($refresh = false) {
        if ($this->_rowsCount === null || $refresh == true) {
            $this->_rowsCount = count($this->getRows());
        }
        return $this->_rowsCount;
    }

    public function getFilteredRows() {
        $filteredRows = array();
        $columnsForDisplay = $this->getColumnsForDisplay();
        $indexColumns = $this->getIndexColumns();
        $columns = array_merge($indexColumns, $columnsForDisplay);
        foreach ($this->_source as $dataRow) {
            if (!empty($columnsForDisplay)) {
                foreach ($dataRow as $colKey => $colVal) {
                    if (!in_array($colKey, $columns)) {
                        unset($dataRow[$colKey]);
                    }
                }
            }
            if ($this->_isSatisfyFilters($dataRow)) {
                $filteredRows[] = $dataRow;
            }
        }
        return $filteredRows;
    }

    protected function _isSatisfyFilters(array $dataRow) {
        $flag = true;
        foreach ($this->_filters as $filter) {
            $flag = $flag && $this->_isSatisfyFilter($filter, $dataRow);
        }
        return $flag;
    }

    protected function _isSatisfyFilter(Lis_Grid_Filter $filter, array $dataRow) {
        $filterColumn = $filter->getFilterColumn();
        $filterValue = $filter->getFilterValue();

        switch ($filter->getFilterType()) {
            case Lis_Grid_Filter::TYPE_EQUAL:
                return (bool) ($dataRow[$filterColumn] == $filterValue);
            case Lis_Grid_Filter::TYPE_LIKE:
                return (bool) (strpos((string) $dataRow[$filterColumn], (string) $filterValue) !== false);
            case Lis_Grid_Filter::TYPE_LESS:
                return (bool) ($dataRow[$filterColumn] < $filterValue);
            case Lis_Grid_Filter::TYPE_GREATER:
                return (bool) ($dataRow[$filterColumn] > $filterValue);
                break;
            case Lis_Grid_Filter::TYPE_NOTEQUAL:
                return (bool) ($dataRow[$filterColumn] != $filterValue);
            case Lis_Grid_Filter::TYPE_NOTLIKE:
                return (bool) (strpos((string) $dataRow[$filterColumn], (string) $filterValue) === false);
            case Lis_Grid_Filter::TYPE_EQUAL | Lis_Grid_Filter::TYPE_LESS:
                return (bool) ($dataRow[$filterColumn] <= $filterValue);
            case Lis_Grid_Filter::TYPE_EQUAL | Lis_Grid_Filter::TYPE_GREATER:
                return (bool) ($dataRow[$filterColumn] >= $filterValue);
                break;
            case Lis_Grid_Filter::TYPE_CONTAIN:
                return in_array($dataRow[$filterColumn], (array) $filterValue);
                break;
            case Lis_Grid_Filter::TYPE_NOTCONTAIN:
                return !in_array($dataRow[$filterColumn], (array) $filterValue);
                $filterValue = (array) $filterValue;
                break;
            default:
                throw new Lis_Grid_Filter('Unknown filter type');
        }
    }

    protected function _dataAsort(array $data, $key) {
        $size = count($data);
        for ($i = 1; $i < $size; $i++) {
            $j = $i - 1;
            $k = $data[$i];
            while ($j >= 0) {
                if ($data[$j][$key] > $k[$key]) {
                    $data[$j + 1] = $data[$j];
                    $data[$j] = $k;
                }
                $j--;
            }
        }
        return $data;
    }

    protected function _dataRsort(array $data, $key) {
        $size = count($data);
        for ($i = 1; $i < $size; $i++) {
            $j = $i - 1;
            $k = $data[$i];
            while ($j >= 0) {
                if ($data[$j][$key] < $k[$key]) {
                    $data[$j + 1] = $data[$j];
                    $data[$j] = $k;
                }
                $j--;
            }
        }
        return $data;
    }

}

