<?php

namespace application\controllers;
use ItForFree\SimpleMVC\Config;
use ItForFree\SimpleMVC\Url;
use application\models\CMSArticle;
use application\models\CMSCategory;
use application\models\CMSSubcategory;

class CMSAdminController extends \ItForFree\SimpleMVC\mvc\Controller
{
	public $articlesData = array();
	
	public $results = array();
	
	public $title = 'Простая CMS на PHP';
	
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
	
}