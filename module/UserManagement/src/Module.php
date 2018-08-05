<?php
namespace UserManagement;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * array of factory.
     *
     * @return array
     */
    public function getServiceConfig()
    {
        return [
            'factories' => [
                Model\UsersTable::class => function($container) {
                    $tableGateway = $container->get(Model\UsersTableGateway::class);
                    return new Model\UsersTable($tableGateway);
                },
                Model\UsersTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\UserManagement());
                    return new TableGateway('users', $dbAdapter, null, $resultSetPrototype);
                },
            ],
        ];
    }

    /**
     * array of factory.
     *
     * @return array
     */
    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\UserManagementController::class => function($container) {
                    return new Controller\UserManagementController(
                        $container->get(Model\UsersTable::class)
                    );
                },
            ],
        ];
    }
}