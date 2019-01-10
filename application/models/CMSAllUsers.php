<?php

namespace application\models;
use ItForFree\SimpleMVC\Config;

class CMSAllUsers extends \ItForFree\SimpleMVC\mvc\Model{
	/**
     * Имя таблицы пользователей
     */
    public $tableName = 'users';
	
	/**
     * @var string Критерий сортировки строк таблицы
     */
    public $orderBy = 'name ASC';
	
	//Свойства
	/**
    * @var int ID пользователя из базы данных
    */
	public $id = null;
	
	/**
    * @var string Имя пользователя
    */
    public $name = null;
	
	/**
    * @var string пароль пользователя
    */
    public $pass = null;
	
	/**
    * @var bool индикатор, показывающий активен пользователь или нет 
    */
    public $active = null;
}
