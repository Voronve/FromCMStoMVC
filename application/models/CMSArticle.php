<?php

namespace application\models;

use ItForFree\SimpleMVC\Config as Config;
/**
 * Модель для работы со статьями
 */
class CMSArticle extends \ItForFree\SimpleMVC\mvc\Model
{

	// Свойства
	
	public $tableName = 'articles';
	
	
	/**
	 * @var int ID статей из базы данны
	 */
	public $id = null;

	/**
	 * @var int Дата первой публикации статьи
	 */
	public $publicationDate = null;

	/**
	 * @var string Полное название статьи
	 */
	public $title = null;

	/**
	 * @var int ID подкатегории статьи 
	 */
	public $subcategoryId = null;

	/**
	 * @var string Краткое описание статьи
	 */
	public $summary = null;

	/**
	 * @var string HTML содержание статьи
	 */
	public $content = null;

	/**
	 * @var int "активность статьи 1 или 0"
	 */
	public $active = null;

	

	/**
	 * Устанавливаем свойства с помощью значений формы редактирования записи в заданном массиве
	 *
	 * @param assoc Значения записи формы
	 */
	public function storeFormValues($params) 
	{

		// Сохраняем все параметры
		$this->__construct($params);

		// Разбираем и сохраняем дату публикации
		if (isset($params['publicationDate'])) {
			$publicationDate = explode('-', $params['publicationDate']);

			if (count($publicationDate) == 3) {
				list ( $y, $m, $d ) = $publicationDate;
				$this->publicationDate = mktime(0, 0, 0, $m, $d, $y);
			}
		}
	}

	/**
	 * Возвращаем объект статьи соответствующий заданному ID статьи
	 *
	 * @param int ID статьи
	 * @return Article|false Объект статьи или false, если запись не найдена или возникли проблемы
	 */
	public function getById($id, $tableName = '') {
		$tableName = !empty($tableName) ? $tableName : $this->tableName;
		
		$sql = "SELECT *, UNIX_TIMESTAMP(publicationDate) "
				. "AS publicationDate FROM $tableName WHERE id = :id";
		$st = $this->pdo->prepare($sql);
		$st->bindValue(":id", $id, \PDO::PARAM_INT);
		$st->execute();

		$row = $st->fetch();

		if ($row) {
			return new CMSArticle($row);
		}
	}

	/**
	 * Возвращает все (или диапазон) объекты Article из базы данных
	 *
	 * @param int $numRows Количество возвращаемых строк (по умолчанию = 1000000)
	 * @param int $categoryId Вернуть статьи только из категории с указанным ID
	 * @param string $order Столбец, по которому выполняется сортировка статей (по умолчанию = "publicationDate DESC")
	 * @return Array|false Двух элементный массив: results => массив объектов Article; totalRows => общее количество строк
	 */
	/*public static function getList($numRows = 1000000, $categoryId = null, $isSubcategory = null, $order = "publicationDate DESC") {
		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		if (!$isSubcategory) {
			$categoryClause = $categoryId ? "WHERE categoryId = $categoryId" : "";
		} else {
			$categoryClause = $categoryId ? "WHERE subcategoryId = $categoryId" : "";
		}

		if ($categoryClause) {
			$onlyActive = $numRows < 1000000 ? "AND active = 1" : "";
		} else {
			$onlyActive = $numRows < 1000000 ? "WHERE active = 1" : "";
		}
		$sql = "SELECT SQL_CALC_FOUND_ROWS *, UNIX_TIMESTAMP(publicationDate) 
                AS publicationDate
                FROM articles $categoryClause $onlyActive
                ORDER BY  $order  LIMIT :numRows";
		$st = $conn->prepare($sql);

		$st->bindValue(":numRows", $numRows, PDO::PARAM_INT);

		$st->execute(); 

		$list = array();

		while ($row = $st->fetch()) {
			$article = new Article($row);
			$list[] = $article;
		}
		
		$sql = "SELECT FOUND_ROWS() AS totalRows";
		$totalRows = $conn->query($sql)->fetch();
		$conn = null;

		return (array(
			"results" => $list,
			"totalRows" => $totalRows[0]
				)
				);
	}*/
	
	/**
    * Возвращает все (или диапазон) объекты Article из базы данных
    *
    * @param int Optional Количество возвращаемых строк (по умолчанию = all)
    * @param int Optional Вернуть статьи только из категории с указанным ID
    * @param string Optional Столбц, по которому выполняется сортировка статей (по умолчанию = "publicationDate DESC")
    * @return Array|false Двух элементный массив: results => массив объектов Article; totalRows => общее количество строк
    */
        
    public function getList($numRows=1000000, $categoryId = null, $isSubcategory = null, $order = "publicationDate DESC")  
    {
		if (!$isSubcategory) {
			$categoryClause = $categoryId ? "WHERE categoryId = $categoryId" : "";
		} else {
			$categoryClause = $categoryId ? "WHERE subcategoryId = $categoryId" : "";
		}
		
		if ($categoryClause) {
			$onlyActive = $numRows < 1000000 ? "AND active = 1" : "";
		} else {
			$onlyActive = $numRows < 1000000 ? "WHERE active = 1" : "";
		}
		
        $sql = "SELECT SQL_CALC_FOUND_ROWS *, UNIX_TIMESTAMP(publicationDate) 
                AS publicationDate
                FROM articles $categoryClause $onlyActive
                ORDER BY  $order  LIMIT :numRows";
        
        $modelClassName = static::class;
       
        $st = $this->pdo->prepare($sql);
        $st->bindValue( ":numRows", $numRows, \PDO::PARAM_INT );
        $st->execute();
        $list = array();
        
        while ($row = $st->fetch()) {
            $example = new $modelClassName($row);
            $list[] = $example;
        }

        $sql = "SELECT FOUND_ROWS() AS totalRows"; //  получаем число выбранных строк
        $totalRows = $this->pdo->query($sql)->fetch();
        return (array ("results" => $list, "totalRows" => $totalRows[0]));
    }

	/**
	 * Вставляем текущий объект статьи в базу данных, устанавливаем его свойства.
	 */

	/**
	 * Вставляем текущий объек Article в базу данных, устанавливаем его ID.
	 */
	/*public function insert() {

		// Есть уже у объекта Article ID?
		if (!is_null($this->id))
			trigger_error("Article::insert(): Attempt to insert an Article object that already has its ID property set (to $this->id).", E_USER_ERROR);

		// Вставляем статью
		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$sql = "INSERT INTO articles ( publicationDate, title, summary, content, active, subcategoryId ) VALUES ( FROM_UNIXTIME(:publicationDate), :title, :summary, :content, :active, :subcategoryId )";
		$st = $conn->prepare($sql);
		$st->bindValue(":publicationDate", $this->publicationDate, PDO::PARAM_INT);
		$st->bindValue(":title", $this->title, PDO::PARAM_STR);
		$st->bindValue(":summary", $this->summary, PDO::PARAM_STR);
		$st->bindValue(":content", $this->content, PDO::PARAM_STR);
		$st->bindValue(":active", $this->active, PDO::PARAM_INT);
		$st->bindValue(":subcategoryId", $this->subcategoryId, PDO::PARAM_INT);
		$st->execute();
		$this->id = $conn->lastInsertId();
		$conn = null;
	}

	/**
	 * Обновляем текущий объект статьи в базе данных
	 */
	/*public function update() {

		// Есть ли у объекта статьи ID?
		if (is_null($this->id))
			trigger_error("Article::update(): "
					. "Attempt to update an Article object "
					. "that does not have its ID property set.", E_USER_ERROR);

		// Обновляем статью
		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$sql = "UPDATE articles SET publicationDate=FROM_UNIXTIME(:publicationDate),"
				. " subcategoryId=:subcategoryId, title=:title, summary=:summary,"
				. " content=:content, active=:active WHERE id = :id";

		$st = $conn->prepare($sql);
		$st->bindValue(":publicationDate", $this->publicationDate, PDO::PARAM_INT);
		$st->bindValue(":subcategoryId", $this->subcategoryId, PDO::PARAM_INT);
		$st->bindValue(":title", $this->title, PDO::PARAM_STR);
		$st->bindValue(":summary", $this->summary, PDO::PARAM_STR);
		$st->bindValue(":content", $this->content, PDO::PARAM_STR);
		$st->bindValue(":active", $this->active, PDO::PARAM_INT);
		$st->bindValue(":id", $this->id, PDO::PARAM_INT);
		$st->execute();
		$conn = null;
	}

	/**
	 * Удаляем текущий объект статьи из базы данных
	 */
	/*public function delete() {

		// Есть ли у объекта статьи ID?
		if (is_null($this->id))
			trigger_error("Article::delete(): Attempt to delete an Article object that does not have its ID property set.", E_USER_ERROR);

		// Удаляем статью
		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$st = $conn->prepare("DELETE FROM articles WHERE id = :id LIMIT 1");
		$st->bindValue(":id", $this->id, PDO::PARAM_INT);
		$st->execute();
		$conn = null;
	}*/

}

