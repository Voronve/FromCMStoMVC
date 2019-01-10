<?php

namespace application\controllers;
use ItForFree\SimpleMVC\Config;
use ItForFree\SimpleMVC\Url;
use application\models\CMSArticle;
use application\models\CMSCategory;
use application\models\CMSSubcategory;
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
	
	/**
	 * Инициализирует все сущности, необходимые для работы со статьями
	 */
	protected function initModelObjects(){
		$this->Article = new CMSArticle;
		$this->Category = new CMSCategory;
		$this->Subcategory = new CMSSubcategory;
		$this->Users = new CMSAllUsers;
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
	}
	
	public function indexAction(){
		$this->initModelObjects();
		$this->getArticles();
		$this->view->addVar('title', $this->title);
		$this->view->addVar('results', $this->results);
        $this->view->render('admin/admin.php');
    }
	
	public function viewArticleAction(){
		$this->articlesData['id'] = $_GET['articleId'];
		$Article = new CMSArticle();
		$SingleArticle = $Article->getById($this->articlesData['id']);
		$this->title = $SingleArticle->title . ' | ' . $this->title;
		$this->results['article']['id'] = $SingleArticle->id;
		$this->results['article']['title'] = $SingleArticle->title;
		$this->results['article']['publicationDate'] = $SingleArticle->publicationDate;
		$this->results['article']['subcategoryId'] = $SingleArticle->subcategoryId;
		$this->results['article']['summary'] = $SingleArticle->summary;
		$this->results['article']['content'] = $SingleArticle->content;
		$this->results['article']['active'] = $SingleArticle->active;
		$this->view->addVar('results', $this->results);
		$this->view->addVar('title', $this->title);
		$this->view->render('singleArticle/singleArticle.php');
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
	
}