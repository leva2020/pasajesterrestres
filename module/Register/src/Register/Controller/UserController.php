<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Register for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Register\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Register\Traits\ModuleTablesTrait as RegisterModuleTablesTrait;
use Register\Form\UserForm;

class UserController extends AbstractActionController
{
    use RegisterModuleTablesTrait;

    public function addAction()
    {
        $form = $this->getServiceLocator()->get('Register\Form\UserForm');
        
        $viewModel = new ViewModel(array(
            'form' => $form
        ));
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $user = $this->getServiceLocator()->get('Register\Model\User');
            $form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());
            
            if ($form->isValid()) {
                
                $firstName = $form->get('first_name')->getValue();
                $user->exchangeArray($form->getData());
                $user->setUsername($this->setUsername($firstName));
                $user->setDocumentTypeId(1);
                $user->setUserTypeId(1);

                $user->generateUid($user->getUserName());
                
                $userId = $this->getUserTable()->save($user, false);
                $viewModel->setVariable('userId', $userId);
                
                return $this->redirect()->toRoute('register/user/welcome');
            }
        }
        
        return $viewModel;
    }

    public function editAction()
    {
        $user = $this->getUserTable()->get('234');
        
        $view = new ViewModel(array(
            'message' => 'Hello world'
        ));
        
        return $view;
    }

    public function welcomeAction()
    {
        $viewModel = new ViewModel(array(
            'welcome' => 'welcome user'
        ));
    }

    public function setUsername($name, $username = false)
    {
        $splitName = preg_split('/ /', trim($name), 2, PREG_SPLIT_DELIM_CAPTURE);
        $userString = $splitName[0];
        
        $tryUser = str_replace(' ', '', strtolower(substr($userString, 0, 16))) . rand(100000, 999999);
        
        try {
            $checkUser = $this->getUserTable()->getByUserName($tryUser);
            if (! $checkUser) {
                return $tryUser;
            } else {
                return $this->setUsername($name);
            }
        } catch (\Exception $e) {
            error_log("error generando username: " . $e->getMessage());
            return false;
        }
    }
}
