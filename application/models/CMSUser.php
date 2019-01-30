<?php

namespace application\models;
use ItForFree\SimpleMVC\Config;

/**
 * Реализация класса user в соответствии с особенностями  My first CMS
 */

class CMSUser extends \ItForFree\SimpleMVC\User
{
	public $tableName = 'users';
	public $orderBy = 'name ASC';
	
	/**
	 * По сути - функция-заглушка т.к. в firstCMS есть только 1 админ, 
	 * а все остальные - просто пользователи без конкретных ролей
	 * 
	 * @param string $userName
	 * @return string
	 */
	protected function getRoleByUserName($userName){
		if( $userName == Config::get('core.admin.username') ){
			return $userName;
		}else{
			return "authorized";
		}
		
	}
	/**
	 * Проверка соответствия имени пользователя и пароля
	 * @param string $login 
	 * @param string $pass
	 * @return boolean
	 */
	protected function checkAuthData($login, $pass){
		$result = false;
		if( $login == Config::get('core.admin.username') ){
			
			if( $pass == Config::get('core.admin.password') ){
				$result = true;
			}
		}else{
			$sql = "SELECT pass FROM users WHERE name = :name;";
			$query = $this->pdo->prepare($sql);
			$query->bindValue( ":name", $login, \PDO::PARAM_STR);
			$query->execute();
			$truePass = $query->fetch();
			if($truePass[0] == $pass){
				$result = true;
			}
		}
		return $result;
	}
}

