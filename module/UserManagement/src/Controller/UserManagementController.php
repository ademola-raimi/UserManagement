<?php

namespace UserManagement\Controller;

use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use UserManagement\Model\UsersTable;
use UserManagement\Form\UserForm;
use UserManagement\Model\UserManagement;
use Zend\InputFilter\InputFilterInterface;
use Zend\Mvc\Controller\AbstractActionController;


class UserManagementController extends AbstractActionController
{
	 private $table;
	 private $user;

    /**
     * Inject UserTable and UserManagement.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(UsersTable $table)
    {
        $this->table = $table;
        $this->user = new UserManagement;
    }

	/**
     * Show the list of Books.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexAction()
    {
    	return new ViewModel([
            'users' => $this->table->fetchAll(),
        ]);
    }

    /**
     * Show the list of Books.
     *
     * @return \Illuminate\Http\Response
     */
    public function listAction()
    {
    	return $this->indexAction(); 
    }

    /**
     * Show the list of Books.
     *
     * @return \Illuminate\Http\Response
     */
    public function addAction()
    {
        $request = $this->getRequest();

        if (! $request->isPost()) {
            return new ViewModel();
        }

        // $user = new UserManagement();
        $filter = $this->user->getInputFilter();

		$isValid = $filter->setData($request->getPost())
            ->setValidationGroup(InputFilterInterface::VALIDATE_ALL)
            ->isValid();

		if (!$isValid)
		{
		    return new ViewModel([
	            'errors' => $filter->getMessages(),
	        ]);
		}

		session_start();
		$_SESSION['first_name'] = $request->getPost()->first_name;

         $this->user->exchangeArray((array) $request->getPost());

        $this->table->saveuser($this->user);

        return $this->redirect()->toRoute('list');
    }

    /**
     * Show the list of Books.
     *
     * @return \Illuminate\Http\Response
     */
    public function editAction()
    {
    }

    /**
     * Delete a user.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteAction()
    {
    	$id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('list');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->table->deleteUser($id);
            }

            // Redirect to list of users
            return $this->redirect()->toRoute('list');
        }

        return [
            'id'    => $id,
            'user' => $this->table->getUser($id),
        ];
    }
}