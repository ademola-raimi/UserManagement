<?php

namespace UserManagement\Controller;

use RuntimeException;
use Zend\Session\Container;
use Zend\Session\SessionManager;
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
	 private $sessionContainer;

    /**
     * Inject UserTable and UserManagement.
     *
     * @return null
     */
    public function __construct(UsersTable $table)
    {
        $sessionManager = new SessionManager();
        $this->sessionContainer = new Container('ContainerNamespace', $sessionManager);
        $this->table = $table;
        $this->user = new UserManagement;
    }

	/**
     * Call add action to display form.
     *
     * @return \Http\Response
     */
    public function indexAction()
    {
        $this->addAction();
    }

    /**
     * Show the list of users.
     *
     * @return \Illuminate\Http\Response
     */
    public function listAction()
    {
        return new ViewModel([
            'users' => $this->table->fetchAll(),
        ]);
    }

    /**
     * Display user form and Add a user to the db.
     *
     * @return \Http\Response
     */
    public function addAction()
    {   
        $request = $this->getRequest();
        $data = $request->getPost();

        if (! $request->isPost()) {
            return new ViewModel();
        }

        // prevent CSRF
        if (!isset($this->sessionContainer->csrf) || $this->sessionContainer->csrf !== $data['csrf']) {
            throw new RuntimeException('CSRF attack');
        }

        $filter = $this->user->getInputFilter();

		$isValid = $filter->setData($data)
            ->setValidationGroup(InputFilterInterface::VALIDATE_ALL)
            ->isValid();

		if (!$isValid)
		{
		    return new ViewModel([
	            'errors' => $filter->getMessages(),
	            'oldData' => $data,
	        ]);
		}

		if ($this->emailValidateUniqueness($data['email'])) {
			return new ViewModel([
	            'errors' => ['email' => ['alreadyExist' => 'email already exist in the database'] ],
	            'oldData' => $data,
	        ]);
		}

        $this->user->exchangeArray((array) $data);

        $user = $this->table->saveuser($this->user);

        if ($user) {
            //save and Redirect to list of users
            $this->sessionContainer->first_name = $data->first_name;

            return $this->redirect()->toRoute('list');
        }
        // save failure message to session and Redirect to list of users
	    $this->sessionContainer->failure = "Something went wrong";

        return $this->redirect()->toRoute('list');
    }

    private function emailValidateUniqueness($email, $id=null, $data=null)
    {
        $user =  $this->table->fetchAll();

        $found = false;
        if (count($user) == 0) {
            return false;
        }

        foreach ($user as $value) {
            if ($value->email === $email ) {
                $found = true;
            }
        }

        if ($id !== null) {
            $user =  $this->table->getUser($id);
            if ($user->email === $email) {
                unset($data['email']);
                return false;
            }
        }

        return $found;
    }

    /**
     * Edit a user.
     *
     * @return \Http\Response
     */
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('user', ['action' => 'add']);
        }

        // Retrieve the user with the specified id. Doing so raises
        // an exception if the user is not found, which should result
        // in redirecting to the landing page.
        try {
            $user = $this->table->getUser($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('user', ['action' => 'index']);
        }

        $request = $this->getRequest();
        $data = $request->getPost();



        if (! $request->isPost()) {
            return new ViewModel([
	            'id' => $id,
	            'user' => $user,
	        ]);
        }

        // prevent CSRF
        if (!isset($this->sessionContainer->csrf) || $this->sessionContainer->csrf !== $data['csrf']) {
            throw new RuntimeException('CSRF attack');
        }

        $filter = $this->user->getInputFilter();

		$isValid = $filter->setData($data)
            ->setValidationGroup(InputFilterInterface::VALIDATE_ALL)
            ->isValid();

		if (!$isValid)
		{
            return new ViewModel([
		        'id' => $id,
	            'errors' => $filter->getMessages(),
	            'user' => $user,
	            'oldData' => $data
	        ]);
        }

	   if ($this->emailValidateUniqueness($data['email'], $id, $data)) {
		    return new ViewModel([
			    'id' => $id,
			    'user' => $user,
	            'errors' => ['email' => ['alreadyExist' => 'email already exist in the database'] ],
	            'oldData' => $data,
	        ]);
        }

        $this->user->exchangeArray((array) $data);

        $user = $this->table->saveuser($this->user);
        if ($user) {
            // save and Redirect to list of users
            $this->sessionContainer->update_first_name = $data->first_name;

            return $this->redirect()->toRoute('list');
        }

        // save to failure message to session and Redirect to list of users
	    $this->sessionContainer->failure = "Something went wrong";

        return $this->redirect()->toRoute('list');
    }

    /**
     * Delete a user.
     *
     * @return \Http\Response
     */
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (!$id) {
            return $this->redirect()->toRoute('list');
        }

        $request = $this->getRequest();

        $delete = $this->table->deleteUser($id);

        if ($delete) {
            // save to success message to session and Redirect to list of users
            $this->sessionContainer->success = "User succesfully deleted";

            return $this->redirect()->toRoute('list');
        }
        // save to failure message to session and Redirect to list of users
	    $this->sessionContainer->failure = "Something went wrong";

        return $this->redirect()->toRoute('list');
    }
}
