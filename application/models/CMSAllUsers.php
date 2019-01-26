<?php

namespace application\models;
use ItForFree\SimpleMVC\Config;

class CMSAllUsers extends \ItForFree\SimpleMVC\mvc\Model{
	/**
     * Имя таблицы пользователей
     */
    public $tableName = 'users';
	
	/**
     * @var string Критерий сортировки строк таблицы
     */
    public $orderBy = 'name ASC';
	
	//Свойства
	/**
    * @var int ID пользователя из базы данных
    */
	public $userId = null;
	
	/**
    * @var string Имя пользователя
    */
    public $name = null;
	
	/**
    * @var string пароль пользователя
    */
    public $pass = null;
	
	/**
    * @var bool индикатор, показывающий активен пользователь или нет 
    */
    public $active = null;
	
	public function isUserExist($login){
		$sql = "SELECT name FROM users WHERE name = :name";
		$st= $this->pdo->prepare($sql);
		$st->bindValue( ":name", $login, \PDO::PARAM_STR );
		$st->execute();
		if( $st->fetch()[0]){
			return true;
		}else{
			return false;
		}
	} 
	
	/**
	 * Устанавливаем свойства пользователя из формы редактирования
	 * @param assoc $params Значения свойств, переданные из формы
	 * 
	 */
	public function storeFormValues($params){
		$this->__construct( $params );
	}
	
	/**
	 * Вставляем текущий обьект Subcategory в базу данных и устанавливаем его свойство ID 
	 */
	
	public function insert(){
		// Проверяем есть ли уже у обьекта CMSAllUsers ID ?
		if ( !is_null( $this->id ) ) trigger_error ( "CMSAllUsers->insert(): Attempt to insert a User object that already has its ID property set (to $this->id).", E_USER_ERROR );
		//Вставляем пользователя
		$sql = "INSERT INTO users(name, pass, active) VALUES(:name, :pass, :active)";
		$st = $this->pdo->prepare($sql);
		$st->bindValue(":name", $this->name, \PDO::PARAM_STR );
		$st->bindValue(":pass", $this->pass, \PDO::PARAM_STR );
		$st->bindValue(":active", $this->active, \PDO::PARAM_INT );
		$st->execute();
		$this->id = $this->pdo->lastInsertId();
	}
	
	/**
    * Обновляем текущий объект User в базе данных.
    */

    public function update() {

      // У объекта Category  есть ID?
      if ( is_null( $this->userId ) ) trigger_error ( "User::update(): Attempt to update a User object that does not have its ID property set.", E_USER_ERROR );

      // Обновляем категорию
      $sql = "UPDATE users SET name=:name, pass=:pass, active=:active WHERE id = :id";
      $st = $this->pdo->prepare( $sql );
      $st->bindValue( ":name", $this->name,\PDO::PARAM_STR );
      $st->bindValue( ":pass", $this->pass, \PDO::PARAM_STR );
	  $st->bindValue( ":active", $this->active, \PDO::PARAM_INT );
      $st->bindValue( ":id", $this->userId, \PDO::PARAM_INT );
      $st->execute();
    }
}
