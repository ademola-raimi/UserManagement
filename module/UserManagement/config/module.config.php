<?php
namespace UserManagement;

use Zend\Router\Http\Segment;

return [

    'router' => [
        'routes' => [
            'user' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/user[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\UserManagementController::class,
                        'action'     => 'add',
                    ],
                ],
            ],

            'list' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/user/list',
                    'defaults' => [
                        'controller' => Controller\UserManagementController::class,
                        'action'     => 'list',
                    ],
                ],
            ],
            'index' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/user/index',
                    'defaults' => [
                        'controller' => Controller\UserManagementController::class,
                        'action'     => 'list',
                    ],
                ],
            ],
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            'user-management' => __DIR__ . '/../view',
        ],
    ],
];