<?php

/**
 * Настройка карты сайта
 *
 */
$navigation = array(
    array(
        'label' => 'Workspace',
        'uri' => '#',
        'class' => 'navigation-category',
        'resource' => 'workspace',
        'privilege' => 'read',
        'privileges' => array(
            'read' => true,
            'edit' => false,
            'remove' => false,
            'execute' => false,
        ),
        'pages' => array(
            array(
                'label' => 'RealEstateObjects',
                'uri' => '/workspace/real-estate-proposals',
                'class' => 'navigation-accessor',
                'resource' => 'workspace::real-estate-proposals',
                'privilege' => 'read',
                'privileges' => array(
                    'read' => true,
                    'edit' => true,
                    'remove' => true,
                    'execute' => false,
                ),
            ),
            array(
                'label' => 'ProposalBlankSettings',
                'uri' => '/workspace/proposal-blank-settings',
                'class' => 'navigation-accessor',
                'resource' => 'workspace::proposal-blank-settings',
                'privilege' => 'read',
                'privileges' => array(
                    'read' => true,
                    'edit' => true,
                    'remove' => true,
                    'execute' => false,
                ),
            ),
            array(
                'label' => 'Coupling',
                'uri' => '#',
                'class' => 'navigation-schema',
                'resource' => 'workspace::coupling',
                'privilege' => 'read',
                'privileges' => array(
                    'read' => true,
                    'edit' => false,
                    'remove' => false,
                    'execute' => false,
                ),
                'pages' => array(
                    array(
                        'label' => 'CouplingWithSiteKurerInContext',
                        'uri' => '/workspace/coupling-with-kurer',
                        'class' => 'navigation-accessor',
                        'resource' => 'workspace::coupling::with-site-kurer',
                        'privilege' => 'read',
                        'privileges' => array(
                            'read' => true,
                            'edit' => false,
                            'remove' => true,
                            'execute' => true,
                        ),
                    ),
                    array(
                        'label' => 'CouplingWithCallJournalInContext',
                        'uri' => '/workspace/coupling-with-journal',
                        'class' => 'navigation-accessor',
                        'resource' => 'workspace::coupling::with-call-journal',
                        'privilege' => 'read',
                        'privileges' => array(
                            'read' => true,
                            'edit' => false,
                            'remove' => true,
                            'execute' => true,
                        ),
                    )
                )
            )
        )
    ),
    array(
        'label' => 'Directories',
        'uri' => '#',
        'class' => 'navigation-category',
        'resource' => 'directory',
        'privilege' => 'read',
        'privileges' => array(
            'read' => true,
            'edit' => false,
            'remove' => false,
            'execute' => false,
        ),
        'pages' => array(
            array(
                'label' => 'Districts',
                'uri' => '#',
                'class' => 'navigation-schema',
                'resource' => 'directories::districts',
                'privilege' => 'read',
                'privileges' => array(
                    'read' => true,
                    'edit' => false,
                    'remove' => false,
                    'execute' => false,
                ),
                'pages' => array(
                    array(
                        'label' => 'GeographicalDistrictsInContext',
                        'uri' => '/directories/geographical-districts',
                        'class' => 'navigation-accessor',
                        'resource' => 'directories::districts::geographical',
                        'privilege' => 'read',
                        'privileges' => array(
                            'read' => true,
                            'edit' => true,
                            'remove' => true,
                            'execute' => false,
                        ),
                    ),
                    array(
                        'label' => 'AdministrativeDistrictsInContext',
                        'uri' => '/directories/administrative-districts',
                        'class' => 'navigation-accessor',
                        'resource' => 'directories::districts::administrative',
                        'privilege' => 'read',
                        'privileges' => array(
                            'read' => true,
                            'edit' => true,
                            'remove' => true,
                            'execute' => false,
                        ),
                    )
                )
            ),
            array(
                'label' => 'Settlements',
                'uri' => '/directories/settlements',
                'class' => 'navigation-accessor',
                'resource' => 'directories::settlements',
                'privilege' => 'read',
                'privileges' => array(
                    'read' => true,
                    'edit' => true,
                    'remove' => true,
                    'execute' => false,
                ),
            ),
            array(
                'label' => 'Streets',
                'uri' => '/directories/streets',
                'class' => 'navigation-accessor',
                'resource' => 'directories::streets',
                'privilege' => 'read',
                'privileges' => array(
                    'read' => true,
                    'edit' => true,
                    'remove' => true,
                    'execute' => false,
                ),
            ),
            array(
                'label' => 'StreetTypes',
                'uri' => '/directories/street-types',
                'class' => 'navigation-accessor',
                'resource' => 'directories::street-types',
                'privilege' => 'read',
                'privileges' => array(
                    'read' => true,
                    'edit' => true,
                    'remove' => true,
                    'execute' => false,
                ),
            ),
            array(
                'label' => 'Contragents',
                'uri' => '/directories/contragents',
                'class' => 'navigation-accessor',
                'resource' => 'directories::contragents',
                'privilege' => 'read',
                'privileges' => array(
                    'read' => true,
                    'edit' => true,
                    'remove' => true,
                    'execute' => false,
                ),
            ),
            array(
                'label' => 'ContragentsPhones',
                'uri' => '/directories/contragents-phones',
                'class' => 'navigation-accessor',
                'resource' => 'directories::contragents-phones',
                'privilege' => 'read',
                'privileges' => array(
                    'read' => true,
                    'edit' => false,
                    'remove' => false,
                    'execute' => false,
                ),
            ),
            array(
                'label' => 'RealEstate',
                'uri' => '#',
                'class' => 'navigation-category',
                'resource' => 'directories::real-estate',
                'privilege' => 'read',
                'privileges' => array(
                    'read' => true,
                    'edit' => false,
                    'remove' => false,
                    'execute' => false,
                ),
                'pages' => array(
                    array(
                        'label' => 'RealEstateTypes',
                        'uri' => '/directories/real-estate-types',
                        'class' => 'navigation-accessor',
                        'resource' => 'directories::real-estate::types',
                        'privilege' => 'read',
                        'privileges' => array(
                            'read' => true,
                            'edit' => true,
                            'remove' => true,
                            'execute' => false,
                        ),
                    ),
                    array(
                        'label' => 'RealEstatePurposes',
                        'uri' => '/directories/real-estate-purposes',
                        'class' => 'navigation-accessor',
                        'resource' => 'directories::real-estate::purposes',
                        'privilege' => 'read',
                        'privileges' => array(
                            'read' => true,
                            'edit' => true,
                            'remove' => true,
                            'execute' => false,
                        ),
                    ),
                    array(
                        'label' => 'HeatingTypes',
                        'uri' => '/directories/heating-types',
                        'class' => 'navigation-accessor',
                        'resource' => 'directories::real-estate::heating-types',
                        'privilege' => 'read',
                        'privileges' => array(
                            'read' => true,
                            'edit' => true,
                            'remove' => true,
                            'execute' => false,
                        ),
                    ),
                    array(
                        'label' => 'HotWaterSupplyTypes',
                        'uri' => '/directories/hot-water-supplies',
                        'class' => 'navigation-accessor',
                        'resource' => 'directories::real-estate::hot-water-supplies',
                        'privilege' => 'read',
                        'privileges' => array(
                            'read' => true,
                            'edit' => true,
                            'remove' => true,
                            'execute' => false,
                        ),
                    ),
                    array(
                        'label' => 'PlanningTypes',
                        'uri' => '/directories/planning-types',
                        'class' => 'navigation-accessor',
                        'resource' => 'directories::real-estate::planning-types',
                        'privilege' => 'read',
                        'privileges' => array(
                            'read' => true,
                            'edit' => true,
                            'remove' => true,
                            'execute' => false,
                        ),
                    ),
                    array(
                        'label' => 'BathroomTypes',
                        'uri' => '/directories/bathroom-types',
                        'class' => 'navigation-accessor',
                        'resource' => 'directories::real-estate::bathroom-types',
                        'privilege' => 'read',
                        'privileges' => array(
                            'read' => true,
                            'edit' => true,
                            'remove' => true,
                            'execute' => false,
                        ),
                    ),
                    array(
                        'label' => 'ProposalStatuses',
                        'uri' => '/directories/proposal-statuses',
                        'class' => 'navigation-accessor',
                        'resource' => 'directories::proposal-statuses',
                        'privilege' => 'read',
                        'privileges' => array(
                            'read' => true,
                            'edit' => true,
                            'remove' => true,
                            'execute' => false,
                        ),
                    ),
                ),
            ),
        )
    ),
    array(
        'label' => 'Administartion',
        'uri' => '#',
        'class' => 'navigation-category',
        'resource' => 'administration',
        'privilege' => 'read',
        'privileges' => array(
            'read' => true,
            'edit' => false,
            'remove' => false,
            'execute' => false
        ),
        'pages' => array(
            array(
                'label' => 'Users',
                'uri' => '/administration/users',
                'class' => 'navigation-accessor',
                'resource' => 'user',
                'privilege' => 'read',
                'privileges' => array(
                    'read' => true,
                    'edit' => true,
                    'remove' => false,
                    'execute' => false
                ),
            ),
            array(
                'label' => 'StructuralUnits',
                'uri' => '/administration/departments',
                'class' => 'navigation-accessor',
                'resource' => 'department',
                'privilege' => 'read',
                'privileges' => array(
                    'read' => true,
                    'edit' => true,
                    'remove' => true,
                    'execute' => false
                ),
            ),
            array(
                'label' => 'UsersSpecializations',
                'uri' => '/administration/specializations',
                'class' => 'navigation-accessor',
                'resource' => 'specialization',
                'privilege' => 'read',
                'privileges' => array(
                    'read' => true,
                    'edit' => true,
                    'remove' => true,
                    'execute' => false
                ),
            ),
            array(
                'label' => 'Roles',
                'uri' => '/administration/roles',
                'class' => 'navigation-accessor',
                'resource' => 'role',
                'privilege' => 'read',
                'privileges' => array(
                    'read' => true,
                    'edit' => true,
                    'remove' => true,
                    'execute' => false
                ),
            ),
            array(
                'label' => 'ColumnModelSettings',
                'uri' => '/administration/column-models',
                'class' => 'navigation-accessor',
                'resource' => 'column-model-setting',
                'privilege' => 'read',
                'privileges' => array(
                    'read' => true,
                    'edit' => true,
                    'remove' => true,
                    'execute' => false
                ),
            ),
        ),
    ),
);