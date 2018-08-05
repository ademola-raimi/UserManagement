<?php
namespace UserManagementTest\Model;

use UserManagement\Model\UsersTable;
use UserManagement\Model\UserManagement;
use PHPUnit\Framework\TestCase as TestCase;
use RuntimeException;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\Db\TableGateway\TableGatewayInterface;

class UsersTableTest extends TestCase
{
    protected function setUp()
    {
        $this->tableGateway = $this->prophesize(TableGatewayInterface::class);
        $this->userTable = new UsersTable($this->tableGateway->reveal());
    }

    public function testFetchAllReturnsAllUsers()
    {
        $resultSet = $this->prophesize(ResultSetInterface::class)->reveal();
        $this->tableGateway->select()->willReturn($resultSet);

        $this->assertSame($resultSet, $this->userTable->fetchAll());
    }

    public function testCanDeleteAnUserByItsId()
    {
        $this->tableGateway->delete(['id' => 123])->shouldBeCalled();
        $this->userTable->deleteUser(123);
    }

public function testSaveUserWillInsertNewUsersIfTheyDontAlreadyHaveAnId()
{
    $userData = [
        'first_name' => 'some first name',
        'last_name'  => 'some last name',
        'email'  => 'some email',
    ];
    $user = new UserManagement();
    $user->exchangeArray($userData);

    $this->tableGateway->insert($userData)->shouldBeCalled();
    $this->userTable->saveUser($user);
}

public function testSaveUSerWillUpdateExistingUsersIfTheyAlreadyHaveAnId()
{
    $userData = [
        'id' => 123,
        'first_name' => 'some first name',
        'last_name'  => 'some last name',
        'email'  => 'some email',
        'created_at' => '2018-02-07 08:34:00',
        'updated_at' => '2018-02-07 08:34:00',
    ];
    $user = new UserManagement();
    $user->exchangeArray($userData);

    $resultSet = $this->prophesize(ResultSetInterface::class);
    $resultSet->current()->willReturn($user);

    $this->tableGateway
        ->select(['id' => 123])
        ->willReturn($resultSet->reveal());
    $this->tableGateway
        ->update(
            array_filter($userData, function ($key) {
                return in_array($key, ['first_name', 'last_name', 'email']);
            }, ARRAY_FILTER_USE_KEY),
            ['id' => 123]
        )->shouldBeCalled();

    $this->userTable->saveUser($user);
}

public function testExceptionIsThrownWhenGettingNonExistentUser()
{
    $resultSet = $this->prophesize(ResultSetInterface::class);
    $resultSet->current()->willReturn(null);

    $this->tableGateway
        ->select(['id' => 123])
        ->willReturn($resultSet->reveal());

    $this->expectException(
        RuntimeException::class,
        'Could not find row with identifier 123'
    );
    $this->userTable->getUser(123);
}
}