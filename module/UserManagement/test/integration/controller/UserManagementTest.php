<?php

namespace UserManagementTest\Controller;

use UserManagement\Model\UserManagement;
use RuntimeException;
use Prophecy\Argument;
use Zend\Session\Container;
use Zend\Session\SessionManager;
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
        $sessionManager = new SessionManager();
        $sessionContainer = new Container('ContainerNamespace', $sessionManager);

        $sessionContainer->csrf = '34fw45eefrt5';
        $this->usersTable
            ->saveUser(Argument::type(UserManagement::class))
            ->shouldBeCalled();
        $this->usersTable->fetchAll()->willReturn([]);

        $postData = [
            'first_name'  => 'John',
            'csrf' => '34fw45eefrt5',
            'last_name' => 'Doe',
            'email'     => 'john@sample.com',
        ];

        $this->dispatch('/user/add', 'POST', $postData);
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/user/list');
    }

    public function testThatAddActionfailedFirstNameValidation()
    {
        $sessionManager = new SessionManager();
        $sessionContainer = new Container('ContainerNamespace', $sessionManager);

        $sessionContainer->csrf = 'sdfdserf56yhg5';
        $postData = [
            'first_name'  => '',
            'last_name' => 'Doe',
            'csrf' => 'sdfdserf56yhg5',
            'email'     => 'john@sample.com',
        ];
        $test = $this->dispatch('/user/add', 'POST', $postData);
        $this->assertResponseStatusCode(200);
        $this->assertNotRedirect();
        $this->assertQuery('h1', 'Add User');
    }

    public function testThatAddActionfailedLastNameValidation()
    {
        $sessionManager = new SessionManager();
        $sessionContainer = new Container('ContainerNamespace', $sessionManager);

        $sessionContainer->csrf = 'dffvh34567uhg67ytr';

        $postData = [
            'first_name'  => 'John',
            'last_name' => '',
            'csrf' => 'dffvh34567uhg67ytr',
            'email'     => 'john@sample.com',
        ];
        $test = $this->dispatch('/user/add', 'POST', $postData);
        $this->assertResponseStatusCode(200);
        $this->assertNotRedirect();
        $this->assertQuery('h1', 'Add User');
    }

    public function testThatAddActionfailedEmptyEmail()
    {
        $sessionManager = new SessionManager();
        $sessionContainer = new Container('ContainerNamespace', $sessionManager);

        $sessionContainer->csrf = 'dffvh34567uhg67ytr';

        $postData = [
            'first_name'  => 'John',
            'last_name' => 'Doe',
            'csrf' => 'dffvh34567uhg67ytr',
            'email'     => '',
        ];
        $test = $this->dispatch('/user/add', 'POST', $postData);
        $this->assertResponseStatusCode(200);
        $this->assertNotRedirect();
        $this->assertQuery('h1', 'Add User');
    }

    public function testThatAddActionfailedInvalidEmail()
    {
        $sessionManager = new SessionManager();
        $sessionContainer = new Container('ContainerNamespace', $sessionManager);

        $sessionContainer->csrf = 'fewerthy654ewdfgy65r';

        $postData1 = [
            'first_name'  => 'John',
            'last_name' => 'Doe',
            'csrf' => 'fewerthy654ewdfgy65r',
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
        $sessionManager = new SessionManager();
        $sessionContainer = new Container('ContainerNamespace', $sessionManager);

        $sessionContainer->csrf = 'dfgfdswe456ytre4534rf';

        $this->usersTable
            ->saveUser(Argument::type(UserManagement::class))
            ->shouldBeCalled();
        $this->usersTable->fetchAll()->willReturn([]);

        $postData = [
            'first_name'  => 'John',
            'last_name' => 'Doe',
            'csrf' => 'dfgfdswe456ytre4534rf',
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
        $sessionManager = new SessionManager();
        $sessionContainer = new Container('ContainerNamespace', $sessionManager);

        $sessionContainer->csrf = 'dfghyt54efghjiuygfdr5';

        $this->usersTable
            ->saveUser(Argument::type(UserManagement::class))
            ->shouldBeCalled();
        $this->usersTable->fetchAll()->willReturn([]);

        $postData = [
            'first_name'  => 'John',
            'last_name' => 'Doe',
            'csrf' => 'dfghyt54efghjiuygfdr5',
            'email'     => 'john@sample.com',
        ];
        $this->dispatch('/user/add', 'POST', $postData);

        $this->dispatch('/user/edit/100', 'POST', $postData);
        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/user/list');
    }

    public function testDeleteActionRedirectsAfterValidDeletion()
    {
        $sessionManager = new SessionManager();
        $sessionContainer = new Container('ContainerNamespace', $sessionManager);

        $sessionContainer->csrf = 'fggtreswe45tre34r';

        $this->usersTable
            ->saveUser(Argument::type(UserManagement::class))
            ->shouldBeCalled();
        $this->usersTable->fetchAll()->willReturn([]);

        $postData = [
            'first_name'  => 'John',
            'last_name' => 'Doe',
            'csrf' => 'fggtreswe45tre34r',
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
        $sessionManager = new SessionManager();
        $sessionContainer = new Container('ContainerNamespace', $sessionManager);

        $sessionContainer->csrf = 'fghytre45tgfde5tfdsdf';

        $postData = [
            'first_name'  => 'John',
            'last_name' => 'Doe',
            'csrf' => 'fghytre45tgfde5tfdsdf',
            'email'     => 'john@sample.com',
        ];
        $this->dispatch('/user/add', 'GET', $postData);

        $this->dispatch('/user/delete/100', 'GET', $postData);
        $this->assertResponseStatusCode(200);
    }

    /**
     * @expectedExceptionMessage csrf attack
     */
    public function testCSRFProtectionFailed()
    {
        $postData = [
            'first_name'  => 'John',
            'last_name' => 'Doe',
            'email'     => 'john@sample.com',
        ];
        $this->dispatch('/user/add', 'POST', $postData);

        $this->assertResponseStatusCode(500);
    }

    public function test404Redirect()
    {
        $this->dispatch('/wddwd/erdwd', 'GET');
        $this->assertResponseStatusCode(404);
    }
}