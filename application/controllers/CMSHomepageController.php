<?php

namespace application\controllers;
use ItForFree\SimpleMVC\Config;
use application\models\CMSArticle;
use application\models\CMSCategory;
use application\models\CMSSubcategory;

class CMSHomepageController extends \ItForFree\SimpleMVC\mvc\Controller
{
    
    public $layoutPath = 'main.php';
	
	public $title = 'Простая CMS на PHP';
	
	public $articlesData = array();
	
	public $results = array();
	
	public $Article = null;
	
	public $Category = null;
	
	public $Subcategory = null;
	
	/**
	 * Инициализирует все сущности, необходимые для работы со статьями
	 */
	protected function initModelObjects(){
		$this->Article = new CMSArticle;
		$this->Category = new CMSCategory;
		$this->Subcategory = new CMSSubcategory;
	}
	
	protected function getArticles(){
		$this->articlesData = $this->Article->getList(Config::get('core.homepageNumArticles'));
		$this->results['articles'] = $this->articlesData['results'];
		$this->results['totalRows'] = $this->articlesData['totalRows'];
		$this->articlesData = $this->Subcategory->getList();
		$this->results['subcategories'] = array();
		foreach ( $this->articlesData['results'] as $subcategory ) { 
			$this->results['subcategories'][$subcategory->id] = $subcategory;
			$this->results['categories'][$subcategory->id] = $this->Category->getById($subcategory->cat_id);
		}
		foreach ( $this->results['articles'] as $article ) 
		{ 
			$article->content = substr($article->content, 0, 100) . ' ...';
		} 
		$articles = null;
	}
	
	/**
     * Выводит на экран страницу "Домашняя страница"
     */
    public function indexAction(){
		$this->initModelObjects();
		$this->getArticles();
        $this->view->addVar('title', $this->title); // передаём переменную по view
		$this->view->addVar('results', $this->results);
        $this->view->render('homepage/homepage.php');
    }
	
	public function archive() 
	{
		$this->initModelObjects();
		$this->getArticles();
		$this->articlesData = $this->Article->getList(100000);

		$this->results['articles'] = $data['results'];
		$this->results['totalRows'] = $data['totalRows'];
		$this->results['category'] = 0;
		$this->results['subcategory'] = 0;
		$data = $this->Subcategory->getList();
		$this->results['subcategories'] = array();

		foreach ( $this->articlesData['results'] as $subcategory ) {
			$this->results['subcategories'][$subcategory->id] = $subcategory;
		}

		$results['pageHeading'] = "Article Archive";
		$results['pageTitle'] = $results['pageHeading'] . " | Widget News";

		require( TEMPLATE_PATH . "/archive.php" );
	}
}