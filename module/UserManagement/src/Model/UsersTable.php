<?php 

namespace UserManagement\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;

class UsersTable
{
    private $tableGateway;

    /**
     * Constructor method to initialize TableGatewayInterface.
     *
     * @return null
     */
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * Fetch all user data.
     *
     * @return object
     */
    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    /**
     * get a single data.
     *
     * @return array
     */
    public function getUser($id)
    {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();
        if (! $row) {
            throw new RuntimeException(sprintf(
                'Could not find row with identifier %d',
                $id
            ));
        }

        return $row;
    }

    /**
     * save or update a user data.
     *
     * @return array || exception
     */
    public function saveUser(UserManagement $user)
    {
        $data = [
            'first_name' => $user->first_name,
            'last_name'  => $user->last_name,
            'email'      => $user->email,
        ];

        $id = (int) $user->id;

        if ($id === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        if (! $this->getUser($id)) {
            throw new RuntimeException(sprintf(
                'Cannot update user with identifier %d; does not exist',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    /**
     * delete a user data.
     *
     * @return array
     */
    public function deleteUser($id)
    {
        $this->tableGateway->delete(['id' => (int) $id]);
    }
}