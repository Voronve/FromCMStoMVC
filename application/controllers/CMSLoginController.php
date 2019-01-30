<?php

namespace application\controllers;
use ItForFree\SimpleMVC\Config;
use ItForFree\SimpleMVC\Url;
/**
 *  Класс для авторизации админа и пользователей
 */
class CMSLoginController extends \ItForFree\SimpleMVC\mvc\Controller
{
	
	public $layoutPath = 'main.php';
	
	public $title = 'Admin Login | Widget News';
	
	public $errorMessage = 'Неправильный логин или пароль, попробуйте ещё раз.';
	
	/*protected $rules = [ 
			['allow' => true, 'roles' => ['?'], 'actions' => ['login']],
			['allow' => true, 'roles' => ['@'], 'actions' => ['logout']],
		];
	
	/**
     * Вход в систему / Выводит на экран форму для входа в систему
     */
    public function loginAction()
    {
		$User = Config::getObject('core.user.class');
		
		if($User->userName != null && $User->userName != 'guest'){
			$this->redirect(Url::link("CMSAdmin/index"));
		} elseif (!empty($_POST)) {
            $login = $_POST['userName'];
            $pass = $_POST['password'];
            if($User->login($login, $pass)) {
                $this->redirect(Url::link("CMSAdmin/index"));
            }
            else {
                $this->redirect(Url::link("CMSLogin/login&auth=deny"));
            }
        }
        else {
			$this->view->addVar('errorMessage', $this->errorMessage);
			$this->view->addVar('title', $this->title);
            $this->view->render('login/loginForm.php');
        }
    }
	
	/**
     * Выход из системы
     */
    public function logoutAction()
    {
        $User = Config::getObject('core.user.class');
        $User->logout();
        $this->redirect(Url::link("CMSLogin/login"));
    }






	
}

