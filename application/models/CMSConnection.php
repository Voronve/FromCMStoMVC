<?php

namespace application\models;


/**
 *  Класс для работы с авторством статей
 */
class CMSConnection extends \ItForFree\SimpleMVC\mvc\Model{
	
	public $tableName = 'connections';
	
	public $orderBy = 'article_id';
	
	public $user_id = '';
	
	public $article_id = '';
	
	/**
     * Получает из БД все поля одной строки таблицы, с соответствующим 
	 * идентификатором статьи
     * Возвращает объект класса модели
     * @param int $id  id строки
     * @param string   $tableName  имя таблицы (необязатлеьный параметр)
     * 
     * @return \ItForFree\SimpleMVC\mvc\Model
     */
    public function getById($id, $tableName = '')
    {
        $tableName = !empty($tableName) ? $tableName : $this->tableName;
        
        $sql = "SELECT * FROM $tableName where article_id = :id";      
        $modelClassName = static::class;
        
        $st = $this->pdo->prepare($sql); 
        
        $st->bindValue(":id", $id, \PDO::PARAM_INT);
        $st->execute();
		$row = null;

		while ($row = $st->fetch()) {
			$example = new $modelClassName($row);
			$list[] = $example;
		}
		return $list;
    }
	
	public function insert() 
	{
      // Вставляем новую связь 
      $sql = "INSERT INTO connections ( article_id, user_id) VALUES ( :articleId, :userId )";
      $st = $this->pdo->prepare( $sql );
      $st->bindValue( ":articleId", $this->article_id, \PDO::PARAM_INT );
      $st->bindValue( ":userId", $this->user_id, \PDO::PARAM_INT );
      $st->execute();
      $conn = null;
    }
	
}

