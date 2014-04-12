<?php

class RealEstate_ColumnModel_Facade {

    public static function getSettingsByDefault(RealEstate_ColumnModel_Helper_ColumnsList $helper, $dataSource, array $savedDefaults = null) {
        $columnsInSource = $helper->getColumnsList($dataSource);
        $columnsOptions = array();
        $defaultSettings = array(
            'COLUMN_ALIAS' => null,
            'COLUMN_VISIBLE' => 0,
            'COLUMN_PRINTABLE' => 0,
            'COLUMN_WIDTH' => null,
            'COLUMN_HOLDED' => 1,
            'COLUMN_ORDER' => 1000,
            'COLUMN_REFLECTOR' => null,
        );
        $i = 0;
        if ($savedDefaults !== null) {
            $colsNamesMemory = array();
            $colsSettingMemory = array();
            foreach ($columnsInSource as $column) {
                $settingsExist = array_key_exists($column, $savedDefaults);
                if ($settingsExist) {
                    $colsSettingMemory[$i] = $savedDefaults[$column];
                    $colsNamesMemory[$savedDefaults[$column]['COLUMN_ID']] = $column;
                } else {
                    $colsSettingMemory[$i] = $defaultSettings;
                    $columnId = self::_generateId();
                    $colsSettingMemory[$i]['COLUMN_ID'] = $columnId;
                    $colsNamesMemory[$columnId] = $column;
                }
                $i++;
            }
            $colsSettingMemory = self::_dataAsort($colsSettingMemory, 'COLUMN_ORDER');
            foreach ($colsSettingMemory as $colOptions) {
                $columnId = $colOptions['COLUMN_ID'];
                $columnName = $colsNamesMemory[$columnId];
                $columnsOptions[$columnName] = $colOptions;
            }
            unset($colsNamesMemory, $colsSettingMemory);
        } else {
            foreach ($columnsInSource as $column) {
                $columnsOptions[$column] = $defaultSettings;
                $columnsOptions[$column]['COLUMN_ID'] = self::_generateId();
            }
        }
        return $columnsOptions;
    }

    public static function getSettingsForUser(array $defaultSettings, array $userSettings) {
        $settings = array();
        if (empty($userSettings)) {
            foreach ($defaultSettings as $colName => $colSettings) {
                $settings[$colName] = $colSettings;
                $settings[$colName]['SETTING_ID'] = self::_generateId();
            }
        } else {
            $colsNamesMemory = array();
            $colsSettingMemory = array();
            $i = 0;
            foreach ($defaultSettings as $colName => $defaults) {
                $columnId = $defaults['COLUMN_ID'];
                if (array_key_exists($colName, $userSettings)) {
                    $isHolded = $defaults['COLUMN_HOLDED'];
                    if ($isHolded) {
                        $colsNamesMemory[$columnId] = $colName;
                        $colsSettingMemory[$i] = $defaults;
                        $colsSettingMemory[$i]['SETTING_ID'] = self::_generateId();
                    } else {
                        $userColSettings = $userSettings[$colName];
                        $isVisible = $userColSettings['COLUMN_VISIBLE'] !== null ? $userColSettings['COLUMN_VISIBLE'] : $defaults['COLUMN_VISIBLE'];
                        $isPrintable = $userColSettings['COLUMN_PRINTABLE'] !== null ? $userColSettings['COLUMN_PRINTABLE'] : $defaults['COLUMN_PRINTABLE'];
                        $columnWidth = $userColSettings['COLUMN_WIDTH'] !== null ? $userColSettings['COLUMN_WIDTH'] : $defaults['COLUMN_WIDTH'];
                        $columnOrder = $userColSettings['COLUMN_ORDER'] !== null ? $userColSettings['COLUMN_ORDER'] : $defaults['COLUMN_ORDER'];
                        $colsNamesMemory[$columnId] = $colName;
                        $colsSettingMemory[$i] = array(
                            'SETTING_ID' => $userColSettings['SETTING_ID'] ? $userColSettings['SETTING_ID'] : self::_generateId(),
                            'COLUMN_ALIAS' => $defaults['COLUMN_ALIAS'],
                            'COLUMN_VISIBLE' => (int) $isVisible,
                            'COLUMN_PRINTABLE' => (int) $isPrintable,
                            'COLUMN_WIDTH' => (int) $columnWidth,
                            'COLUMN_ORDER' => (int) $columnOrder,
                            'COLUMN_ID' => (int) $defaults['COLUMN_ID'],
                            'COLUMN_HOLDED' => 0,
                            'COLUMN_REFLECTOR' => $defaults['COLUMN_REFLECTOR']
                        );
                    }
                } else {
                    $colsNamesMemory[$columnId] = $colName;
                    $colsSettingMemory[$i] = $defaults;
                    $colsSettingMemory[$i]['SETTING_ID'] = self::_generateId();
                }
                $i++;
            }
            $colsSettingMemory = self::_dataAsort($colsSettingMemory, 'COLUMN_ORDER');
            foreach ($colsSettingMemory as $colOptions) {
                $columnId = $colOptions['COLUMN_ID'];
                $columnName = $colsNamesMemory[$columnId];
                $settings[$columnName] = $colOptions;
            }
            unset($colsNamesMemory, $colsSettingMemory);
        }
        return $settings;
    }

    public static function buildColumnModel(Lis_Grid_ColumnModel $columnModel, $columnsOptions) {
        foreach ($columnsOptions as $colName => $colOptions) {
            $columnAlias = $colOptions['COLUMN_ALIAS'];
            $isVisible = $colOptions['COLUMN_VISIBLE'];
            $columnWidth = !empty($colOptions['COLUMN_WIDTH']) ? $colOptions['COLUMN_WIDTH'] : 100;
            $columnReflector = $colOptions['COLUMN_REFLECTOR'];
            $column = new Lis_Grid_Column(array('name' => $colName, 'alias' => $columnAlias, 'width' => $columnWidth));
            if (!$isVisible) {
                $column->setOption('hidden', true);
            }
            if (!empty($columnReflector)) {
                $column->setOption('reflector', $columnReflector);
            }
            $columnModel->addColumn($column);
        }
    }

    public static function buildColumnModelForPrint(Lis_Grid_ColumnModel $columnModel, $columnsOptions) {
        foreach ($columnsOptions as $colName => $colOptions) {
            $columnAlias = $colOptions['COLUMN_ALIAS'];
            $isVisible = $colOptions['COLUMN_VISIBLE'];
            $columnWidth = !empty($colOptions['COLUMN_WIDTH']) ? $colOptions['COLUMN_WIDTH'] : 100;
            $columnReflector = $colOptions['COLUMN_REFLECTOR'];
            $isPrintable = $colOptions['COLUMN_PRINTABLE'];
            if ($isPrintable) {
                $column = new Lis_Grid_Column(array('name' => $colName, 'alias' => $columnAlias, 'width' => $columnWidth));
                if (!$isVisible) {
                    $column->setOption('hidden', true);
                }
                if (!empty($columnReflector)) {
                    $column->setOption('reflector', $columnReflector);
                }
                $columnModel->addColumn($column);
            }
        }
    }

    private static function _generateId() {
        return 'new_' . uniqid();
    }

    private static function _dataAsort(array $data, $key) {
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

}