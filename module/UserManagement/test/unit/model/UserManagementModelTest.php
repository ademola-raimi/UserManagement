<?php

namespace UserManagementTest\Model;

use UserManagement\Model\UserManagement;
use PHPUnit\Framework\TestCase as TestCase;
 
class UserManagementModelTest extends TestCase
{
    public function testInitialUserValuesAreNull()
    {
        $user = new UserManagement();

        $this->assertNull($user->id, '"id" should be null by default');
        $this->assertNull($user->first_name, '"first_name" should be null by default');
        $this->assertNull($user->last_name, '"last_name" should be null by default');
        $this->assertNull($user->email, '"email" should be null by default');
        $this->assertNull($user->created_at, '"created_at" should be null by default');
        $this->assertNull($user->updated_at, '"updated_at" should be null by default');
    }

    public function testExchangeArraySetsPropertiesCorrectly()
    {
        $user = new UserManagement();
        $data  = [
            'first_name' => 'some first name',
            'id'     => 123,
            'last_name'  => 'some last name',
            'email'  => 'some email',
            'created_at'  => '2018-02-07 08:34:00',
            'updated_at'  => '2018-02-07 08:34:00',
        ];

        $user->exchangeArray($data);

        $this->assertSame(
            $data['first_name'],
            $user->first_name,
            '"first_name" was not set correctly'
        );

        $this->assertSame(
            $data['id'],
            $user->id,
            '"id" was not set correctly'
        );

        $this->assertSame(
            $data['last_name'],
            $user->last_name,
            '"last_name" was not set correctly'
        );

        $this->assertSame(
            $data['email'],
            $user->email,
            '"email" was not set correctly'
        );

        $this->assertSame(
            $data['created_at'],
            $user->created_at,
            '"created_at" was not set correctly'
        );

        $this->assertSame(
            $data['updated_at'],
            $user->updated_at,
            '"updated_at" was not set correctly'
        );
    }

    public function testExchangeArraySetsPropertiesToNullIfKeysAreNotPresent()
    {
        $user = new UserManagement();

        $user->exchangeArray([
            'first_name' => 'some first name',
            'id'     => 123,
            'last_name'  => 'some last name',
            'email'  => 'some email',
            'created_at'  => '2018-02-07 08:34:00',
            'updated_at'  => '2018-02-07 08:34:00',
        ]);

        $user->exchangeArray([]);

        $this->assertNull($user->first_name, '"artist" should default to null');
        $this->assertNull($user->last_name, '"id" should default to null');
        $this->assertNull($user->email, '"title" should default to null');
        $this->assertNull($user->created_at, '"created_at" should default to null');
        $this->assertNull($user->updated_at, '"updated_at" should default to null');
    }

    public function testInputFiltersAreSetCorrectly()
    {
        $user = new UserManagement();

        $inputFilter = $user->getInputFilter();

        $this->assertSame(3, $inputFilter->count());
        $this->assertTrue($inputFilter->has('last_name'));
        $this->assertTrue($inputFilter->has('first_name'));
        $this->assertTrue($inputFilter->has('email'));
    }
}