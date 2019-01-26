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
	 * Инициализирует все сущности, необходимые для работы с методами контроллера
	 */
	protected function initModelObjects(){
		$this->Article = new CMSArticle;
		$this->Category = new CMSCategory;
		$this->Subcategory = new CMSSubcategory;
		$this->Users = new CMSAllUsers;
		$this->Connection = new CMSConnection;
	}
	
	/*-----------------------Работа с объектом статьи------------------------*/
	
	
	/**
	 * Получает данные о статьях а также категориях и субкатегориях к которым 
	 * они принадлежат
	 */
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
	
	/**
	 * Выводит страницу со списком статей
	 */
	public function indexAction(){
		$this->initModelObjects();
		$this->getArticles();
		$this->view->addVar('title', $this->title);
		$this->view->addVar('results', $this->results);
        $this->view->render('admin/admin.php');
    }
	
	/**
	* Редактирование статьи
	* 
	* @return null
	*/
   function editArticleAction() {
	   $this->initModelObjects();
	   $activeAuthorsId = array();
	   $this->results['pageTitle'] = "Edit Article";
	   $this->title = $this->results['pageTitle'];

	   if (isset($_POST['saveChanges'])) {
		   // Пользователь получил форму редактирования статьи: сохраняем изменения
		   $this->articleSaveChanges();
		   return;
	   } elseif (isset($_POST['cancel'])) {
		   // Пользователь отказался от результатов редактирования: возвращаемся к списку статей
		   //header("Location: admin.php");
		   $this->redirect(Url::link("CMSAdmin/index"));
		   return;
	   } else {
		   // Пользвоатель еще не получил форму редактирования: выводим форму
		   $this->articleGetFormData();
	   }
		   $this->view->addVar('results', $this->results);
		   $this->view->addVar('title', $this->title);
		   $this->view->render('admin/edit/article.php');
   }
   
   /**
    * Сохранение изменений в выбранной статье
    * 
    * @return null
    */
   protected function articleSaveChanges(){
		   if (!$article = $this->Article->getById((int)$_POST['articleId'])) {
			   //header("Location: admin.php?error=articleNotFound");
			   $this->redirect(Url::link("CMSAdmin/index&error=articleNotFound"));
			   return;
		   }
		   if ($_POST['categoryId'] != $this->Subcategory->getById($_POST['subcategoryId'])->cat_id) {
			   //header("Location: admin.php?error=CategoryNotMatch");
			   $this->redirect(Url::link("CMSAdmin/index&error=CategoryNotMatch"));
			   return;
		   }
		   $this->Article->storeFormValues($_POST);
		   $this->Article->update();

		   //Удалаем предыдущие связи  и устанавливаем новые
		   $connections = $this->Connection->getById($article->id);
		   foreach ($connections as $connection){
			   //var_dump($connection); die;
			   $connection->delete();
		   }
		   $activeAuthorsId = $_POST['authorsId'];
		   $connection = 0;
		   foreach ($activeAuthorsId as $authorId) {
			   $connData['article_id'] = $this->Article->articleId;
			   $connData['user_id'] = $authorId;
			   $this->Connection->__construct($connData);
			   $this->Connection->insert();
		   }
		   $this->redirect(Url::link("CMSAdmin/index&status=changesSaved"));
		   //header("Location: admin.php?status=changesSaved");
   }
   
   /**
    * Получение данных для формы редактирования статей
    *    
    * @return null 
    */
   protected function articleGetFormData(){
	    $this->results['article'] = $this->Article->getById((int) $_GET['articleId']);
		$data = $this->Subcategory->getList();
		$this->results['subcategories'] = $data['results'];
		$data = $this->Category->getList();
		$this->results['categories'] = $data['results'];
		$data = $this->Users->getlist();
		$this->results['users'] = $data['results'];
		$data = $this->Connection->getById($this->results['article']->id);
		$results['authors'] = array();
		foreach($data as $connection){
			$this->results['authors'][] = $connection->user_id;
		}
		$this->results['categoryIdCompare'] = $this->Subcategory->getById($this->results['article']->subcategoryId)->cat_id;
   }
   
	/**
	 * Создание новой статьи
	 */
    function newArticleAction() {
	    $this->initModelObjects();
		$this->results['pageTitle'] = "New Article";
		$this->results['formAction'] = "newArticle";
		$this->title = $this->results['pageTitle'];
        
		if (isset($_POST['saveChanges'])) {
			//Пользователь получил данные из формы - добавляем статью
			$this->addNewArticle();
		} elseif (isset($_POST['cancel'])) {
			// Пользователь сбросил результаты редактирования: возвращаемся к списку статей
			$this->redirect(Url::link("CMSAdmin/index"));
		} else {
			// Пользователь еще не получил форму редактирования: выводим форму
			$this->newArticleGetFormData();
		}
	}
	
	/**
	 * Добавление данных новой статьи в базу данных
	 * 
	 * @return null
	 */
	protected function addNewArticle(){
		if ($_POST['categoryId'] != $this->Subcategory->getById($_POST['subcategoryId'])->cat_id) {
			$this->redirect(Url::link("CMSAdmin/index&error=CategoryNotMatch"));
			return;
		}
		// Пользователь получает форму редактирования статьи: сохраняем новую статью
		$this->Article->storeFormValues($_POST);      
		$this->Article->insert();

		//Сохраняем новые связи статья-авторы
		$activeAuthorsId = $_POST['authorsId'];
		$connData = array();
		foreach ($activeAuthorsId as $authorId) {
			$connData['article_id'] = $this->Article->id;
			$connData['user_id'] = $authorId;
			$this->Connection->__construct($connData);
			$this->Connection->insert();
		}
		$this->redirect(Url::link("CMSAdmin/index&status=changesSaved"));
	}
	
	/**
	 * Получения данных для формы новой статьи
	 * 
	 * @return null
	 */
	protected function newArticleGetFormData(){
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
	
	/**
	 * Удаление выбранной статьи
	 */
	function deleteArticleAction() {
		$this->initModelObjects();
		if (!$article = $this->Article->getById((int) $_GET['articleId'])) {
			$this->redirect(Url::link("CMSAdmin/index&error=articleNotFound"));
		}
		$article->delete();
		$this->redirect(Url::link("CMSAdmin/index&status=articleDeleted"));
	}
   
   /*----------------------Работа с объектом категорий -----------------------*/
   
	/**
	 * Выведение страницы со списком категорий 
	 */
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
				$this->results['errorMessage'] = "Error: Category not found.";
			if ($_GET['error'] == "categoryContainsArticles")
				$this->results['errorMessage'] = "Error: Category contains subcategories. Delete the subcategories, or assign them to another category, before deleting this category.";
		}

		if (isset($_GET['status'])) {
			if ($_GET['status'] == "changesSaved")
				$this->results['statusMessage'] = "Your changes have been saved.";
			if ($_GET['status'] == "categoryDeleted")
				$this->results['statusMessage'] = "Category deleted.";
		}
		
		$this->view->addVar('results', $this->results);
		$this->view->render('admin/listCategories.php');
	}
	
	
	/**
	 * добавление новой категории
	 * 
	 * @return null
	 */
	function newCategoryAction() {
		$this->initModelObjects();
		$this->results['pageTitle'] = "New Article Category";
		$this->title = $this->results['pageTitle'];

		if (isset($_POST['saveChanges'])) {

			// User has posted the category edit form: save the new category
			$category = $this->Category;
			$category->storeFormValues($_POST);
			$category->insert();
			$this->redirect(Url::link("CMSAdmin/listCategories&status=changesSaved"));
		} elseif (isset($_POST['cancel'])) {

			// User has cancelled their edits: return to the category list
			$this->redirect(Url::link("CMSAdmin/listCategories"));
		} else {

			// User has not posted the category edit form yet: display the form
			$this->results['category'] = $this->Category;
			$this->view->addVar('title', $this->title);
			$this->view->addVar('results', $this->results);
			$this->view->render('admin/edit/category.php');
		}
	}
	
	function editCategoryAction() {
		$this->initModelObjects();
		$this->results['pageTitle'] = "Edit Article Category";
		$this->title = $this->results['pageTitle'];
		if (isset($_POST['saveChanges'])) {

			// User has posted the category edit form: save the category changes

			if (!$category = $this->Category->getById((int) $_POST['categoryId'])) {
				$this->redirect(Url::link("CMSAdmin/listCategories&error=categoryNotFound"));
				return;
			}

			$category->storeFormValues($_POST);
			$category->update();
			$this->redirect(Url::link("CMSAdmin/listCategories&status=changesSaved"));
		} elseif (isset($_POST['cancel'])) {

			// User has cancelled their edits: return to the category list
			
			$this->redirect(Url::link("CMSAdmin/listCategories"));
		} else {

			// User has not posted the category edit form yet: display the form
			$this->results['category'] = $this->Category->getById((int) $_GET['categoryId']);
			$this->view->addVar('title', $this->title);
			$this->view->addVar('results', $this->results);
			$this->view->render('admin/edit/category.php');
		}
	}
	
	function deleteCategoryAction() {
		$this->initModelObjects();
		if (!$category = $this->Category->getById((int) $_GET['categoryId'])) {
			$this->redirect(Url::link("CMSAdmin/listCategories&error=categoryNotFound"));
		}
		$subcategories = $this->Subcategory->getList(1000000, $category->id);

		if ($subcategories['totalRows'] > 0) {
			$this->redirect(Url::link("CMSAdmin/listCategories&error=categoryContainsArticles"));
		}
		$category->delete();
		$this->redirect(Url::link("CMSAdmin/listCategories&status=categoryDeleted"));
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
				$this->results['errorMessage'] = "Error: Subcategory not found.";
			if ($_GET['error'] == "subcategoryContainsArticles")
				$this->results['errorMessage'] = "Error: Subcategory contains articles. Delete the articles, or assign them to another subcategory, before deleting this subcategory.";
		}

		if (isset($_GET['status'])) {
			if ($_GET['status'] == "changesSaved")
				$this->results['statusMessage'] = "Your changes have been saved.";
			if ($_GET['status'] == "subcategoryDeleted")
				$this->results['statusMessage'] = "Subcategory deleted.";
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
				$this->results['errorMessage'] = "Error: User not found.";
			if ($_GET['error'] == "userExist")
				$this->results['errorMessage'] = "Error: User with such name is alredy exist.";
		}

		if (isset($_GET['status'])) {
			if ($_GET['status'] == "changesSaved")
				$this->results['statusMessage'] = "Your changes have been saved.";
			if ($_GET['status'] == "userDeleted")
				$this->results['statusMessage'] = "User deleted.";
		}

		$this->view->addVar('results', $this->results);
		$this->view->render('admin/listUsers.php');
	}
	
	
	
	function newSubcategoryAction() {
		$this->initModelObjects();
		$this->results['pageTitle'] = "New Article Subcategory";
		$this->title = $this->results['pageTitle'];
		$this->results['formAction'] = "newSubcategory";

		if (isset($_POST['saveChanges'])) {
			//Находим идентификатор категории по её названию в форме
			$_POST['cat_id'] = $this->Subcategory->getCategIdByName($_POST['category']);
			// User has posted the subcategory edit form: save the new subcategory
			$this->Subcategory->storeFormValues($_POST);
			var_dump($_POST);
			$this->Subcategory->insert();
			$this->redirect(Url::link("CMSAdmin/listSubcategories&status=changesSaved"));
		} elseif (isset($_POST['cancel'])) {

			// User has cancelled their edits: return to the category list
			$this->redirect(Url::link("CMSAdmin/listSubcategories"));
		} else {

			// User has not posted the category edit form yet: display the form
			$this->results['subcategory'] = $this->Subcategory;
			$categories = $this->Category->getList();
			$catname = array();
			foreach ($categories['results'] as $category) {
				$catname[] = $category->name;
			}
			$this->view->addVar('title', $this->title);
			$this->view->addVar('results', $this->results);
			$this->view->addVar('categories', $categories);
			$this->view->addVar('catname', $catname);
			$this->view->render('admin/edit/subcategory.php');
		}
	}
	
	function editSubcategoryAction() {
		$this->initModelObjects();
		$this->results['pageTitle'] = "Edit Article Subcategory";
		$this->title = $this->results['pageTitle'];

		if (isset($_POST['saveChanges'])) {

			// User has changed the subcategory: save the subcategory changes

			if (!$subcategory = $this->Subcategory->getById((int) $_POST['id'])) {
				$this->redirect(Url::link("CMSAdmin/listSubcategories&error=categoryNotFound"));
			}
			//Находим идентификатор категории по её названию в форме
			$_POST['cat_id'] = $this->Subcategory->getCategIdByName($_POST['category']);
			$this->Subcategory->storeFormValues($_POST);
			$this->Subcategory->update();
			$this->redirect(Url::link("CMSAdmin/listSubcategories&status=changesSaved"));
		} elseif (isset($_POST['cancel'])) {
			// User has cancelled their edits: return to the category list
			$this->redirect(Url::link("CMSAdmin/listSubcategories"));
		} else {
			// User has not posted the subcategory edit form yet: display the form
			$this->results['subcategory'] = $this->Subcategory->getById((int) $_GET['subcategoryId']);
			$categories = $this->Category->getList();
			$catname = array();
			foreach ($categories['results'] as $category) {
				$catname[] = $category->name;
			}
				$this->view->addVar('title', $this->title);
				$this->view->addVar('results', $this->results);
				$this->view->addVar('categories', $categories);
				$this->view->addVar('catname', $catname);
				$this->view->render('admin/edit/subcategory.php');
		}
	}
	
	function deleteSubcategoryAction() {
		$this->initModelObjects();
		if (!$subcategory = $this->Subcategory->getById((int) $_GET['subcategoryId'])) {
			$this->redirect(Url::link("CMSAdmin/listSubcategories&error=subcategoryNotFound"));
		}

		$articles = $this->Article->getList(1000000, $subcategory->id, true);

		if ($articles['totalRows'] > 0) {
			$this->redirect(Url::link("CMSAdmin/listSubcategories&error=subcategoryContainsArticles"));
		}

		$subcategory->delete();
		$this->redirect(Url::link("CMSAdmin/listSubcategories&status=subcategoryDeleted"));
	}
	
	function newUserAction() {
		$this->initModelObjects();
		$this->results['pageTitle'] = "Add new user";
		$this->title = $this->results['pageTitle'];
		if (isset($_POST['saveChanges'])) {

			// User has added a new user - the change is saved
			if ($this->Users->isUserExist($_POST['name'])) {
				$this->redirect(Url::link("CMSAdmin/listUsers&error=userExist"));
			} else {
				$this->Users->storeFormValues($_POST);
				$this->Users->insert();
				$this->redirect(Url::link("CMSAdmin/listUsers&status=changesSaved"));
			}
		} elseif (isset($_POST['cancel'])) {

			// User has cancelled their edits: return to the category list
			$this->redirect(Url::link("CMSAdmin/listUsers"));
		} else {

			// User has not posted the category edit form yet: display the form
			$this->results['user'] = $this->Users;
			$this->view->addVar('title', $this->title);
			$this->view->addVar('results', $this->results);
			$this->view->render('admin/edit/user.php');
		}
	}
	
	function editUserAction() {
		$this->initModelObjects();
		$this->results['pageTitle'] = "Edit User";
		$this->title = $this->results['pageTitle'];

		if (isset($_POST['saveChanges'])) {

			// User has changed the user information: save the user changes

			if (!$user = $this->Users->getById((int) $_POST['userId'])) {
				$this->redirect(Url::link("CMSAdmin/listUsers&error=userNotFound"));
			}
			$this->Users->storeFormValues($_POST);
			$this->Users->update();
			$this->redirect(Url::link("CMSAdmin/listUsers&status=changesSaved"));
		} elseif (isset($_POST['cancel'])) {
			// User has cancelled their edits: return to the users list
			$this->redirect(Url::link("CMSAdmin/listUsers"));
		} else {
			// User has not posted the user edit form yet: display the form
			$this->results['user'] = $this->Users->getById((int) $_GET['userId']);
				$this->view->addVar('title', $this->title);
				$this->view->addVar('results', $this->results);
				$this->view->render('admin/edit/user.php');
		}
	}
	
	function deleteUserAction() {
		$this->initModelObjects();
		if (!$user = $this->Users->getById((int) $_GET['userId'])) {
			$this->redirect(Url::link("CMSAdmin/listUsers&error=userNotFound"));
		}

		$user->delete();
		$this->redirect(Url::link("CMSAdmin/listUsers&status=userDeleted"));
	}
	
}