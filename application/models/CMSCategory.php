<?php
namespace application\models;

use ItForFree\SimpleMVC\Config as Config;
/**
 * Класс для обработки категорий статей
 */

class CMSCategory extends \ItForFree\SimpleMVC\mvc\Model
{
    // Свойства
	
	public $tableName = 'categories';

    /**
    * @var int ID категории из базы данных
    */
    public $id = null;

    /**
    * @var string Название категории
    */
    public $name = null;

    /**
    * @var string Короткое описание категории
    */
    public $description = null;

	/**
     *  @var string Имя поля по котору сортируем
     */
    public $orderBy = 'name ASC';
    /**
    * Устанавливаем свойства объекта с использованием значений из формы редактирования
    *
    * @param assoc Значения из формы редактирования
    */

    public function storeFormValues ( $params ) {

      // Store all the parameters
      $this->__construct( $params );
    }

    /**
    * Вставляем текущий объект Category в базу данных и устанавливаем его свойство ID.
    */

    public function insert($tableName = '') {
      // У объекта Category уже есть ID?
      if ( !is_null( $this->id ) ) trigger_error ( "Category::insert(): Attempt to insert a Category object that already has its ID property set (to $this->id).", E_USER_ERROR );

	  $tableName = !empty($tableName) ? $tableName : $this->tableName;
      // Вставляем категорию
      $sql = "INSERT INTO $tableName ( name, description ) VALUES ( :name, :description )";
      $st = $this->pdo->prepare ( $sql );
      $st->bindValue( ":name", $this->name, \PDO::PARAM_STR );
      $st->bindValue( ":description", $this->description, \PDO::PARAM_STR );
      $st->execute();
      $this->id = $this->pdo->lastInsertId();
    }


    /**
    * Обновляем текущий объект Category в базе данных.
    */

    public function update() {

      // У объекта Category  есть ID?
      if ( is_null( $this->id ) ) trigger_error ( "Category::update(): Attempt to update a Category object that does not have its ID property set.", E_USER_ERROR );

      // Обновляем категорию
      $sql = "UPDATE categories SET name=:name, description=:description WHERE id = :id";
      $st = $this->pdo->prepare( $sql );
      $st->bindValue( ":name", $this->name,\PDO::PARAM_STR );
      $st->bindValue( ":description", $this->description, \PDO::PARAM_STR );
      $st->bindValue( ":id", $this->id, \PDO::PARAM_INT );
      $st->execute();
    }


    /**
    * Удаляем текущий объект Category из базы данных.
    */

    public function delete() {

      // У объекта Category  есть ID?
      if ( is_null( $this->id ) ) trigger_error ( "Category::delete(): Attempt to delete a Category object that does not have its ID property set.", E_USER_ERROR );

      // Удаляем категорию
      $st = $this->pdo->prepare ( "DELETE FROM categories WHERE id = :id LIMIT 1" );
      $st->bindValue( ":id", $this->id, \PDO::PARAM_INT );
      $st->execute();
    }

}
	  
	


