<?php

namespace application\controllers;
use ItForFree\SimpleMVC\Config;
use application\models\CMSArticle;

class CMSHomepageController extends \ItForFree\SimpleMVC\mvc\Controller
{
    
    public $layoutPath = 'main.php';
	
	public $title = 'Простая CMS на PHP';
	
	public $articlesData = array();
	
	public $results = array();
	

	protected function getArticles()
	{
		$articles = new CMSArticle;
		$this->articlesData = $articles->getList(Config::get('core.homepageNumArticles'));
		$this->results['articles'] = $this->articlesData['results'];
		$this->results['totalRows'] = $this->articlesData['totalRows'];
		foreach ( $this->results['articles'] as $article ) 
		{ 
			$article->content = substr($article->content, 0, 100) . ' ...';
		} 
		$articles = null;
	}

	

	
	/**
     * Выводит на экран страницу "Домашняя страница"
     */
    public function indexAction()
    {
		$this->getArticles();
        $this->view->addVar('title', $this->title); // передаём переменную по view
		$this->view->addVar('results', $this->results);
        $this->view->render('homepage/homepage.php');
    }
	
	/*function homepage() 
{
    $results = array();
    $data = Article::getList(HOMEPAGE_NUM_ARTICLES);
    $results['articles'] = $data['results'];
    $results['totalRows'] = $data['totalRows'];
    $data = Subcategory::getList();
    $results['subcategories'] = array();
    foreach ( $data['results'] as $subcategory ) { 
        $results['subcategories'][$subcategory->id] = $subcategory;
		$results['categories'][$subcategory->id] = Category::getById($subcategory->cat_id);
    }
	foreach ( $results['articles'] as $article ) { 
		//mb_substr();
       $article->content = substr($article->content, 0, 100) . ' ...';
    } 
    
    $results['pageTitle'] = "Простая CMS на PHP";
    
    require(TEMPLATE_PATH . "/homepage.php");
    
}*/
	
	
	
	
	
}

