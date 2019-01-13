<?php

namespace application\controllers;
use ItForFree\SimpleMVC\Config;
use ItForFree\SimpleMVC\Url;
use application\models\CMSArticle;
use application\models\CMSCategory;
use application\models\CMSSubcategory;
use application\models\CMSConnection;
use application\models\CMSAllUsers;

class CMSAdminController extends \ItForFree\SimpleMVC\mvc\Controller
{
	public $articlesData = array();
	
	public $results = array();
	
	public $title = 'Простая CMS на PHP';
	
	public $Article = null;
	
	public $Category = null;
	
	public $Subcategory = null;
	
	public $Users = null;
	
	public $Connection = null;
	
	/**
	 * Инициализирует все сущности, необходимые для работы со статьями
	 */
	protected function initModelObjects(){
		$this->Article = new CMSArticle;
		$this->Category = new CMSCategory;
		$this->Subcategory = new CMSSubcategory;
		$this->Users = new CMSAllUsers;
		$this->Connection = new CMSConnection;
	}
	
	protected function getArticles(){
		$this->articlesData = $this->Article->getList();
		$this->results['articles'] = $this->articlesData['results'];
		$this->results['totalRows'] = $this->articlesData['totalRows'];
		$this->articlesData = $this->Subcategory->getList();
		$this->results['subcategories'] = array();
		foreach ( $this->articlesData['results'] as $subcategory ) { 
			$this->results['subcategories'][$subcategory->id] = $subcategory;
			$this->results['categories'][$subcategory->id] = $this->Category->getById($subcategory->cat_id);
		}
		if (isset($_GET['status'])) {
			if ($_GET['status'] == "changesSaved")
				$this->results['statusMessage'] = "Your changes have been saved.";
			if ($_GET['status'] == "categoryDeleted")
				$this->results['statusMessage'] = "Category deleted.";
		}
	}
	
	public function indexAction(){
		$this->initModelObjects();
		$this->getArticles();
		$this->view->addVar('title', $this->title);
		$this->view->addVar('results', $this->results);
        $this->view->render('admin/admin.php');
    }
	
	function listCategoriesAction() {
		$this->initModelObjects();
		$data = $this->Category->getList();
		$this->results['categories'] = $data['results'];
		$this->results['totalRows'] = $data['totalRows'];
		$this->results['pageTitle'] = "List of categories";
		$this->title = $this->results['pageTitle'];
		$this->view->addVar('title', $this->title);
		
		if (isset($_GET['error'])) {
			if ($_GET['error'] == "categoryNotFound")
				$results['errorMessage'] = "Error: Category not found.";
			if ($_GET['error'] == "categoryContainsArticles")
				$results['errorMessage'] = "Error: Category contains articles. Delete the articles, or assign them to another category, before deleting this category.";
		}

		if (isset($_GET['status'])) {
			if ($_GET['status'] == "changesSaved")
				$results['statusMessage'] = "Your changes have been saved.";
			if ($_GET['status'] == "categoryDeleted")
				$results['statusMessage'] = "Category deleted.";
		}
		
		$this->view->addVar('results', $this->results);
		$this->view->render('admin/listCategories.php');
	}
	
	
		function listSubcategoriesAction() {
		$this->initModelObjects();
		$data = $this->Subcategory->getList();
		$this->results['subcategories'] = $data['results'];
		$this->results['totalRows'] = $data['totalRows'];
		$this->results['pageTitle'] = "List of subcategories";
		$this->title = $this->results['pageTitle'];
		$this->view->addVar('title', $this->title);
		//Извлекаем название категории по Id
		foreach ($this->results['subcategories'] as $subcategory) {
			$category = $this->Category->getById($subcategory->cat_id);
			$subcategory->cat_name = $category->name;
		}
		if (isset($_GET['error'])) {
			if ($_GET['error'] == "subcategoryNotFound")
				$results['errorMessage'] = "Error: Subcategory not found.";
			if ($_GET['error'] == "subcategoryContainsArticles")
				$results['errorMessage'] = "Error: Subcategory contains articles. Delete the articles, or assign them to another subcategory, before deleting this subcategory.";
		}

		if (isset($_GET['status'])) {
			if ($_GET['status'] == "changesSaved")
				$results['statusMessage'] = "Your changes have been saved.";
			if ($_GET['status'] == "subcategoryDeleted")
				$results['statusMessage'] = "Subcategory deleted.";
		}

		$this->view->addVar('results', $this->results);
		$this->view->render('admin/listSubcategories.php');
	}
	
	function listUsersAction() {
		$this->initModelObjects();
		$data = $this->Users->getList();
		$this->results['users'] = $data['results'];
		$this->results['totalRows'] = $data['totalRows'];
		$this->results['pageTitle'] = "User list";
		$this->title = $this->results['pageTitle'];
		$this->view->addVar('title', $this->title);
		if (isset($_GET['error'])) {
			if ($_GET['error'] == "usersNotFound")
				$results['errorMessage'] = "Error: User not found.";
			if ($_GET['error'] == "userExist")
				$results['errorMessage'] = "Error: User with such name is alredy exist.";
		}

		if (isset($_GET['status'])) {
			if ($_GET['status'] == "changesSaved")
				$results['statusMessage'] = "Your changes have been saved.";
			if ($_GET['status'] == "userDeleted")
				$results['statusMessage'] = "User deleted.";
		}

		$this->view->addVar('results', $this->results);
		$this->view->render('admin/listUsers.php');
	}
	
	/**
	* Редактирование статьи
	* 
	* @return null
	*/
   function editArticle() {
	   

	   $results = array();
	   $activeAuthorsId = array();
	   $results['pageTitle'] = "Edit Article";
	   $results['formAction'] = "editArticle";

	   if (isset($_POST['saveChanges'])) {

		   // Пользователь получил форму редактирования статьи: сохраняем изменения
		   if (!$article = Article::getById((int) $_POST['articleId'])) {
			   header("Location: admin.php?error=articleNotFound");
			   return;
		   }
		   if ($_POST['categoryId'] != Subcategory::getById($_POST['subcategoryId'])->cat_id) {
			   header("Location: admin.php?error=CategoryNotMatch");
			   return;
		   }
		   $article->storeFormValues($_POST);
		   $article->update();

		   //Удалаем предыдущие связи  и устанавливаем новые
		   $connections = Connection::getById($article->id);
		   foreach ($connections as $connection){
			   $connection->delete();
		   }
		   $activeAuthorsId = $_POST['authorsId'];
		   $connection = 0;
		   foreach ($activeAuthorsId as $authorId) {
			   $connData['article_id'] = $article->id;
			   $connData['user_id'] = $authorId;
			   $connection = new Connection($connData);
			   $connection->insert();
		   }
		   header("Location: admin.php?status=changesSaved");
	   } elseif (isset($_POST['cancel'])) {

		   // Пользователь отказался от результатов редактирования: возвращаемся к списку статей
		   header("Location: admin.php");
	   } else {

		   // Пользвоатель еще не получил форму редактирования: выводим форму
		   $results['article'] = Article::getById((int) $_GET['articleId']);
		   $data = Subcategory::getList();
		   $results['subcategories'] = $data['results'];
		   $data = Category::getList();
		   $results['categories'] = $data['results'];
		   $data = User::getlist();
		   $results['users'] = $data['results'];
		   $data = Connection::getById($results['article']->id);
		   $results['authors'] = array();
		   foreach($data as $connection){
			   $results['authors'][] = $connection->userId;
		   }
		   $results['categoryIdCompare'] = Subcategory::getById($results['article']->subcategoryId)->cat_id;

	   }
		   require(TEMPLATE_PATH . "/admin/editArticle.php");
   }
   
   
   function newArticleAction() {
	    $this->initModelObjects();
		$this->results['pageTitle'] = "New Article";
		$this->results['formAction'] = "newArticle";
		$this->title = $this->results['pageTitle'];
        
		if (isset($_POST['saveChanges'])) {
			//echo $_POST['categoryId'] . "<br>" . $this->Subcategory->getById($_POST['subcategoryId'])->cat_id; die;
			if ($_POST['categoryId'] != $this->Subcategory->getById($_POST['subcategoryId'])->cat_id) {
				$this->redirect(Url::link("CMSAdmin/index&error=CategoryNotMatch"));
				//header("Location: admin.php?error=CategoryNotMatch");
				return;
			}
			// Пользователь получает форму редактирования статьи: сохраняем новую статью
			$this->Article->storeFormValues($_POST);      
			$this->Article->insert();

			//Сохраняем новые связи статья-авторы
			$activeAuthorsId = $_POST['authorsId'];
			$connData = array();
			foreach ($activeAuthorsId as $authorId) 
			{
				$connData['article_id'] = $this->Article->id;
				$connData['user_id'] = $authorId;
				$this->Connection->__construct($connData);
				$this->Connection->insert();
			}
			$this->redirect(Url::link("CMSAdmin/index&status=changesSaved"));
			//header("Location: admin.php?status=changesSaved");
		} elseif (isset($_POST['cancel'])) {

			// Пользователь сбросил результаты редактирования: возвращаемся к списку статей
			$this->redirect(Url::link("CMSAdmin/index"));
			//header("Location: admin.php");
		} else {

			// Пользователь еще не получил форму редактирования: выводим форму
			$this->results['article'] = $this->Article;
			$data = $this->Category->getList();
			$this->results['categories'] = $data['results'];
			$data = $this->Subcategory->getList();
			$this->results['subcategories'] = $data['results'];
			$data = $this->Users->getlist();
			$this->results['users'] = $data['results'];
			$this->results['authors'] = array();
			//
			$this->results['categoryIdCompare'] = null;
			$this->view->addVar('results', $this->results);
			$this->view->addVar('title', $this->title);
			$this->view->render('admin/edit/article.php');
		}
	}
	
}