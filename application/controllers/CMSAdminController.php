<?php

namespace application\controllers;
use ItForFree\SimpleMVC\Config;
use ItForFree\SimpleMVC\Url;
use application\models\CMSArticle;

class CMSAdminController extends \ItForFree\SimpleMVC\mvc\Controller
{
	public $articlesData = array();
	
	public $results = array();
	
	public $title = 'Простая CMS на PHP';
	
	protected function getArticles(){
		$Articles = new CMSArticle;
		$this->articlesData = $Articles->getList();
		$this->results['articles'] = $this->articlesData['results'];
		$this->results['totalRows'] = $this->articlesData['totalRows'];
		
	}
	
	public function indexAction(){
		$this->getArticles();
		$this->view->addVar('title', $this->title);
		$this->view->addVar('results', $this->results);
        $this->view->render('admin/admin.php');
    }
	
	public function viewArticleAction(){
		$this->articlesData['id'] = $_GET['articleId'];
		$Article = new CMSArticle();
		$SingleArticle = $Article->getById($this->articlesData['id']);
		$this->results['article']['id'] = $SingleArticle->id;
		$this->results['article']['title'] = $SingleArticle->title;
		$this->results['article']['publicationDate'] = $SingleArticle->publicationDate;
		$this->results['article']['subcategoryId'] = $SingleArticle->subcategoryId;
		$this->results['article']['summary'] = $SingleArticle->summary;
		$this->results['article']['content'] = $SingleArticle->content;
		$this->results['article']['active'] = $SingleArticle->active;
		$this->view->addVar('results', $this->results);
		$this->view->render('singleArticle/singleArticle.php');
		
	}
	
}