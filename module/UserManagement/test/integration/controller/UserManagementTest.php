<?php

namespace UserManagementTest\Controller;

use UserManagement\Model\UserManagement;
use Prophecy\Argument;
use UserManagement\Model\UsersTable;
use Zend\ServiceManager\ServiceManager;
use UserManagement\Controller\UserManagementController;
use Zend\Stdlib\ArrayUtils;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class UserManagementTest extends AbstractHttpControllerTestCase
{
    protected $traceError = true;
    protected $usersTable;

    public function setUp()
    {
        // The module configuration should still be applicable for tests.
        // You can override configuration here with test case specific values,
        // such as sample view templates, path stacks, module_listener_options,
        // etc.
        $configOverrides = [];


        $this->setApplicationConfig(ArrayUtils::merge(
            // Grabbing the full application configuration:
            include __DIR__ . '/../../../../../config/application.config.php',
            $configOverrides
        ));
        parent::setUp();

        $this->configureServiceManager($this->getApplicationServiceLocator());
    }

    protected function configureServiceManager(ServiceManager $services)
    {
        $services->setAllowOverride(true);

        $services->setService('config', $this->updateConfig($services->get('config')));
        $services->setService(UsersTable::class, $this->mockUserTable()->reveal());

        $services->setAllowOverride(false);
    }

    protected function updateConfig($config)
    {
        $config['db'] = [];
        return $config;
    }

    protected function mockUserTable()
    {
        $this->usersTable = $this->prophesize(UsersTable::class);
        return $this->usersTable;
    }

    public function testIndexActionCanBeAccessed()
    {
        $this->usersTable->fetchAll()->willReturn([]);

        $this->dispatch('/user');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('UserManagement');
        $this->assertControllerName(UserManagementController::class);
        $this->assertControllerClass('UserManagementController');
        $this->assertMatchedRouteName('user');
    }

    public function testAddActionRedirectsAfterValidPost()
    {
        $this->usersTable
            ->saveUser(Argument::type(UserManagement::class))
            ->shouldBeCalled();
        $this->usersTable->fetchAll()->willReturn([]);

        $postData = [
            'first_name'  => 'John',
            'last_name' => 'Doe',
            'email'     => 'john@sample.com',
        ];

        $this->dispatch('/user/add', 'POST', $postData);
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/user/list');
    }

    public function testThatAddActionfailedFirstNameValidation()
    {
        $postData = [
            'first_name'  => '',
            'last_name' => 'Doe',
            'email'     => 'john@sample.com',
        ];
        $test = $this->dispatch('/user/add', 'POST', $postData);
        $this->assertResponseStatusCode(200);
        $this->assertNotRedirect();
        $this->assertQuery('h1', 'Add User');
    }

    public function testThatAddActionfailedLastNameValidation()
    {
        $postData = [
            'first_name'  => 'John',
            'last_name' => '',
            'email'     => 'john@sample.com',
        ];
        $test = $this->dispatch('/user/add', 'POST', $postData);
        $this->assertResponseStatusCode(200);
        $this->assertNotRedirect();
        $this->assertQuery('h1', 'Add User');
    }

    public function testThatAddActionfailedEmptyEmail()
    {
        $postData = [
            'first_name'  => 'John',
            'last_name' => 'Doe',
            'email'     => '',
        ];
        $test = $this->dispatch('/user/add', 'POST', $postData);
        $this->assertResponseStatusCode(200);
        $this->assertNotRedirect();
        $this->assertQuery('h1', 'Add User');
    }

    public function testThatAddActionfailedInvalidEmail()
    {
        $postData1 = [
            'first_name'  => 'John',
            'last_name' => 'Doe',
            'email'     => 'john',
        ];
        $test = $this->dispatch('/user/add', 'POST', $postData1);
        $this->assertResponseStatusCode(200);
        $this->assertNotRedirect();
        $this->assertQuery('h1', 'Add User');

        $postData2 = [
            'first_name'  => 'John',
            'last_name' => 'Doe',
            'email'     => 'john@',
        ];
        $test = $this->dispatch('/user/add', 'POST', $postData2);
        $this->assertResponseStatusCode(200);
        $this->assertNotRedirect();
        $this->assertQuery('h1', 'Add User');
    }

    public function testAddActionRedirectsAfterValidEditing()
    {
        $this->usersTable
            ->saveUser(Argument::type(UserManagement::class))
            ->shouldBeCalled();
        $this->usersTable->fetchAll()->willReturn([]);

        $postData = [
            'first_name'  => 'John',
            'last_name' => 'Doe',
            'email'     => 'john@sample.com',
        ];
        $this->dispatch('/user/add', 'POST', $postData);


        $this->usersTable
            ->getUser(1)
            ->willReturn(new UserManagement());

        $this->dispatch('/user/edit/1', 'POST', $postData);
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/user/list');
    }

    public function testNotFoundUserEdition()
    {
        $this->usersTable
            ->saveUser(Argument::type(UserManagement::class))
            ->shouldBeCalled();
        $this->usersTable->fetchAll()->willReturn([]);

        $postData = [
            'first_name'  => 'John',
            'last_name' => 'Doe',
            'email'     => 'john@sample.com',
        ];
        $this->dispatch('/user/add', 'POST', $postData);

        $this->dispatch('/user/edit/100', 'POST', $postData);
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/user/list');
    }

    public function testDeleteActionRedirectsAfterValidDeletion()
    {
        $this->usersTable
            ->saveUser(Argument::type(UserManagement::class))
            ->shouldBeCalled();
        $this->usersTable->fetchAll()->willReturn([]);

        $postData = [
            'first_name'  => 'John',
            'last_name' => 'Doe',
            'email'     => 'john@sample.com',
        ];
        $this->dispatch('/user/add', 'POST', $postData);


        $this->usersTable
            ->getUser(1)
            ->willReturn(new UserManagement());

        $this->dispatch('/user/delete/1', 'GET', $postData);
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/user/list');
    }

    public function testNotFoundUserDeletion()
    {
        $postData = [
            'first_name'  => 'John',
            'last_name' => 'Doe',
            'email'     => 'john@sample.com',
        ];
        $this->dispatch('/user/add', 'GET', $postData);

        $this->dispatch('/user/delete/100', 'GET', $postData);
        $this->assertResponseStatusCode(200);
    }

    public function test404Redirect()
    {
        $this->dispatch('/wddwd/erdwd', 'GET');
        $this->assertResponseStatusCode(404);
    }
}