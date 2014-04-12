<?php

class Lis_View_Helper_GridData extends Zend_View_Helper_Abstract {
    public function gridData(Lis_Grid_Data_Abstract $listData) {
        $columnsForDisplay = $listData->getColumnsForDisplay();
        if(empty($columnsForDisplay)) {
            throw new Lis_Grid_Data_Exception('No columns to display');
        }
        $dataRows = $listData->getRows();
        $pks = $listData->getIndexColumns();
        $rows = array();
        for($i = 0, $j = count($dataRows); $i < $j; $i++) {
            $pkData = array();
            foreach ($pks as $pk) {
                $pkData[] = $dataRows[$i][$pk];
                if(!array_key_exists($pk, $columnsForDisplay)){
                    unset($dataRows[$i][$pk]);
                }
            }
            foreach ($listData->getReflectedColumns() as $column) {
               if(array_key_exists($column, $dataRows[$i])) {
                   $dataRows[$i][$column] = $listData->reflectColumn($column, $dataRows[$i][$column]);
               } else {
                   $columnAlias = array_keys($columnsForDisplay, $column);
                   if(count($columnAlias) > 0) {
                       $columnAlias = array_pop($columnAlias);
                       $dataRows[$i][$columnAlias] = $listData->reflectColumn($column, $dataRows[$i][$columnAlias]);
                   }
               }
            }
            $rows[] = array(
                'id' => implode('_', $pkData),
                'cell' => array_values($dataRows[$i])
            );
        }
        $content = array(
            'total'     => $listData->getTotalPages(),
            'page'      => $listData->getCurrentPage(),
            'records'   => $listData->getRowsCount(),
            'rows'      => $rows,
        );
        $userData = $listData->getUserData();
        if(!empty($userData)) {
            $content['userdata'] = $userData;
        }
        return Zend_Json::encode($content);
    }
}
