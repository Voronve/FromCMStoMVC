<?php
namespace application\models;

use ItForFree\SimpleMVC\Config as Config;
/**
 * Класс для работы с субкатегориями 
 */
class CMSSubcategory extends \ItForFree\SimpleMVC\mvc\Model
{
	
	public $tableName = 'subcategories';
	
	/**
	 *
	 * @var string название подкатегории  
	 */
	public $name = null;
	
	/**
	 *
	 * @var int идентификатор категории к которой относится данная подкатегория 
	 */
	public $cat_id = null;
	
	/**
	 * Устанавливаем свойства подкатегории из формы редактирования
	 * @param assoc $params Значения свойств, переданные из формы
	 * 
	 */
	public function storeFormValues($params){
		$this->__construct( $params );
	}
	
	/**
	 * Возвращаем все объекты Subcategory из базы данных и их количество
	 * 
	 * @param int $numRows Количество возвращаемых объектов(по умолчанию all)
	 * @param string $order порядок сортировки объектов(по умолчанию "name ASC")
	 * @return array|false возвращаем либо неудачу либо массив из 2-х элементов:
	 * массив с искомыми объектами субкатегорий и их количество  
	 */
	public function getList($numRows=1000000, $categoryId=null, $order="name ASC")
	{
		$categoryClause = $categoryId ? "WHERE cat_id = $categoryId" : "";
		
		$sql = "SELECT * FROM subcategories $categoryClause ORDER BY $order LIMIT :numRows";
		
		$st= $this->pdo->prepare($sql);
		$st->bindValue(":numRows", $numRows, \PDO::PARAM_INT );
		$st->execute();
		$list = array();
		
		while( $row = $st->fetch() ){
			$subcategory = new CMSSubcategory($row);
			$list[] = $subcategory;
		}
		
		$sql = "SELECT FOUND_ROWS() AS totalRows";
		$totalRows = $this->pdo->query($sql)->fetch();
		$conn = null;
		return (array("results" => $list, "totalRows" => $totalRows[0] ) );
	}
	
	
	
	
	/**
	 *  Узнаём id выбранной пользователем категории по её названию
	 * 
	 * @param string $name Категория, которую выбрал пользователь
	 * @return int идентификатор категории  
	 */
	public function getCategIdByName($name){
		$sql = "SELECT id FROM categories WHERE name = :name";
		$st = $this->pdo->prepare($sql);
		$st->bindValue(":name", $name, \PDO::PARAM_STR);
		$st->execute();
		$row = $st->fetch();
		if($row){
			return $row[0];
		}
	}
	
	/**
	 * 
	 * @param string $name Название подкатегории которую нужно проверить на существование 
	 * @return boolean  Возвращаем true если подкатегория существует
	 */
	public static function isSubcategoryExist($name){
		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$sql = "SELECT name FROM subcategories WHERE name = :name";
		$st = $conn->prepare($sql);
		$st->bindValue(":name", $name, PDO::PARAM_STR);
		$st->execute();
		if($st->fetch()[0]){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * Вставляем текущий обьект Subcategory в базу данных и устанавливаем его свойство ID 
	 */
	
	public function insert(){
		// Проверяем есть ли уже у обьекта Subcategory ID ?
		if ( !is_null( $this->id ) ) trigger_error ( "Subcategory::insert(): "
				. "Attempt to insert a Subcategory object that already has its "
				. "ID property set (to $this->id).", E_USER_ERROR );
		//Вставляем субкатегорию
		$sql = "INSERT INTO subcategories(name, cat_id) VALUES(:name, :cat_id)";
		$st = $this->pdo->prepare($sql);
		$st->bindValue(":name", $this->name, \PDO::PARAM_STR );
		$st->bindValue(":cat_id", $this->cat_id, \PDO::PARAM_INT );
		$st->execute();
		$this->id = $this->pdo->lastInsertId();
	}
	
	/**
	 * Обновляем уже существующий в базе данных объект подкатегории
	 */
	
	public function update(){
		// Проверяем есть ли уже у обьекта Subcategory ID ?
		if ( is_null( $this->id ) ) trigger_error ( "Subcategory::insert(): "
				. "Attempt to insert a Subcategory object that does not have its "
				. "ID property set (to $this->id).", E_USER_ERROR );
		$sql = "UPDATE subcategories SET name=:name, cat_id=:cat_id WHERE id=:id";
		$st = $this->pdo->prepare($sql);
		$st->bindValue(":name", $this->name, \PDO::PARAM_STR);
		$st->bindValue(":cat_id", $this->cat_id, \PDO::PARAM_INT);
		$st->bindValue(":id", $this->id, \PDO::PARAM_INT);
		$st->execute();
	}
	
	/**
	 * Удааляем подкатегорию
	 */
	
	public function delete(){
		// У объекта Subcategory  есть ID?
      if ( is_null( $this->id ) ) trigger_error ( "Subcategory::delete(): "
			  . "Attempt to delete a Subcategory object that does not have its "
			  . "ID property set.", E_USER_ERROR );

      // Удаляем подкатегорию
      $st = $this->pdo->prepare ( "DELETE FROM subcategories WHERE id = :id LIMIT 1" );
      $st->bindValue( ":id", $this->id, \PDO::PARAM_INT );
      $st->execute();
		
	}

}


