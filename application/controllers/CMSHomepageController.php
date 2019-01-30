<?php

namespace application\controllers;
use ItForFree\SimpleMVC\Config;
use application\models\CMSArticle;
use application\models\CMSCategory;
use application\models\CMSSubcategory;
use application\models\CMSConnection;
use application\models\CMSAllUsers;

class CMSHomepageController extends \ItForFree\SimpleMVC\mvc\Controller
{
    
    public $layoutPath = 'main.php';
	
	public $title = 'Простая CMS на PHP';
	
	public $articlesData = array();
	
	public $results = array();
	
	public $Article = null;
	
	public $Category = null;
	
	public $Subcategory = null;
	
	public $Connection = null;
	
	public $Users = null;
	
	/**
	 * Инициализирует все сущности, необходимые для работы контроллера
	 */
	protected function initModelObjects(){
		$this->Article = new CMSArticle;
		$this->Category = new CMSCategory;
		$this->Subcategory = new CMSSubcategory;
		$this->Connection = new CMSConnection();
		$this->User = new CMSAllUsers();
	}
	
	protected function getArticles(){
		$this->results['articles'] = $this->articlesData['results'];
		$this->results['totalRows'] = $this->articlesData['totalRows'];
		$this->articlesData = $this->Subcategory->getList();
		$this->results['subcategories'] = array();
		foreach ( $this->articlesData['results'] as $subcategory ) { 
			$this->results['subcategories'][$subcategory->id] = $subcategory;
			$this->results['categories'][$subcategory->id] = $this->Category->
					getById($subcategory->cat_id);
		}
	}
	
	/**
     * Выводит на экран страницу "Домашняя страница"
     */
    public function indexAction(){
		$this->initModelObjects();
		
		$this->articlesData = $this->Article->getList(
				Config::get('core.homepageNumArticles'));
		$this->getArticles();
		foreach ( $this->results['articles'] as $article ) 
		{ 
			$article->content = substr($article->content, 0, 100) . ' ...';
		} 
		
        $this->view->addVar('title', $this->title); // передаём переменную по view
		$this->view->addVar('results', $this->results);
        $this->view->render('homepage/homepage.php');
    }
	
	/**
	 *  Выводит краткое описание статьи на главной странице
	 */
	public function viewArticleAction(){
		$this->initModelObjects();
		$this->articlesData['id'] = $_GET['articleId'];
		$SingleArticle = $this->Article->getById($this->articlesData['id']);
		$this->title = $SingleArticle->title . ' | ' . $this->title;
		$this->results['article']['id'] = $SingleArticle->id;
		$this->results['article']['title'] = $SingleArticle->title;
		$this->results['article']['publicationDate'] = $SingleArticle->publicationDate;
		$this->results['article']['subcategoryId'] = $SingleArticle->subcategoryId;
		$this->results['article']['summary'] = $SingleArticle->summary;
		$this->results['article']['content'] = $SingleArticle->content;
		$this->results['article']['active'] = $SingleArticle->active;
		$this->results['article']['subcategory'] = $this->Subcategory->getById(
				$this->results['article']['subcategoryId']);
		$connections = $this->Connection->getById( $this->results['article']['id'] );
	    $connectionsCount = count($connections);
	
		foreach( $connections as $connection)
		{
			$userId = $connection->user_id;
			$this->results['authors'][] = $this->User->getById($userId)->name;
		}
		$this->view->addVar('article', $SingleArticle);
		$this->view->addVar('results', $this->results);
		$this->view->addVar('title', $this->title);
		$this->view->render('homepage/singleArticle.php');
	}
	
	/**
	 *  Выводит главную страницу архива статей
	 */
	public function archiveAction(){
		$this->initModelObjects();
		$this->articlesData = $this->Article->getList(100000);
		$this->getArticles();
		$this->results['category'] = 0;
		$this->results['subcategory'] = 0;
		$this->results['pageHeading'] = "Article Archive";
		$this->title = $this->results['pageHeading'] . " | Widget News";
		
		$this->view->addVar('title', $this->title);
		$this->view->addVar('results', $this->results);
		/*Передаем также объект категории т.к. его методы унаследованы от 
		 * родительского класса model и не являются статическими*/
		$this->view->addVar('Category', $this->Category);
		$this->view->render('homepage/archive.php');
	}
	
	/**
	 *  Выводит на странице архива список статей соответствующих определенной
	 * категории 
	 */
	function archiveCatAction() 
	{
		$this->initModelObjects();
		$subcategoryId = ( isset( $_GET['subcategoryId'] ) && 
				$_GET['subcategoryId'] ) ? (int)$_GET['subcategoryId'] : null;
		$this->results['subcategory'] = $this->Subcategory->getById( $subcategoryId );
		$this->results['category'] = $this->Category->getById( 
				$this->results['subcategory']->cat_id );
		$data = $this->Subcategory->getList(1000000, $this->results['subcategory']->cat_id);
		$articleArr = array();
		foreach($data['results'] as $subcategory){
			$articleArr[] = $this->Article->getList(100000, $subcategory->id, true);
		}
		$this->results['articles'] = array();
		$this->results['totalRows'] = 0;
		
		for( $i = 0; $i < count($articleArr); $i++){
			$this->results['articles'] = array_merge($this->results['articles'], 
					$articleArr[$i]['results']);
			$this->results['totalRows'] = $this->results['totalRows'] + 
					$articleArr[$i]['totalRows'];
		}
		if($this->results['category']){
			$this->results['pageHeading'] = $this->results['category']->name;
			$this->title = $this->results['category']->name;
		}else{
			$this->results['pageHeading'] = "Article Archive";
		}
		
		$this->view->addVar('title', $this->title);
		$this->view->addVar('results', $this->results);
		/*Передаем также объект категории т.к. его методы унаследованы от 
		 * родительского класса model и не являются статическими*/
		$this->view->addVar('Category', $this->Category);
		$this->view->render('homepage/archive.php');
	}
	
	
	/**
	 * Выводит на странице архива список статей соответствующих определенной
	 * подкатегории
	 */
	function archiveSubcatAction() 
	{
		//Инициализируем объекты моделей
		$this->initModelObjects();
		
		/*Если в GET параметре передан id субкатегории, сохраняем его в переменной*/
		$subcategoryId = ( isset( $_GET['subcategoryId'] ) && 
				$_GET['subcategoryId'] ) ? (int)$_GET['subcategoryId'] : null;
		
		/* Так как мы выводим субкатегорию то пункт массива category нам не нужен
		 и мы его обнуляем*/
		$this->results['category'] = 0;
		$this->results['subcategory'] = $this->Subcategory->getById( $subcategoryId );
		$this->title = $this->results['subcategory']->name;
		
		/*Получаем список статей выбранной субкатегории и записываем в массив
		В метод getList третим параметром мы передаем булево значение в true,
		что означает, что передаваемый id принадлежит субкатегории а не категории*/
		$articleArr = array();
		$articleArr = $this->Article->getList( 100000, $this->results['subcategory'] ? 
				$this->results['subcategory']->id : null, true );

		$this->results['articles'] = $articleArr['results'];
		$this->results['totalRows'] = $articleArr['totalRows'];

		$SubcatObj = $this->Subcategory->getList();
		$this->results['subcategories'] = array();

		foreach ( $SubcatObj['results'] as $subcategory ) {
			$this->results['subcategories'][$subcategory->id] = $subcategory;
		}

		$this->results['pageHeading'] = $this->results['subcategory'] ?  
				$this->results['subcategory']->name : "Article Archive";
		
		$this->title = $this->results['pageHeading'] . " | Widget News";

		$this->view->addVar('title', $this->title);
		$this->view->addVar('results', $this->results);
		
		/*Передаем также объект субкатегории т.к. его методы унаследованы от 
		 * родительского класса model и не являются статическими*/
		$this->view->addVar('Subcategory', $this->Subcategory);
		$this->view->render('homepage/archive.php');
	}
	
}