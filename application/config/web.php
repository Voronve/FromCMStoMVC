<?php
/**
 * Конфигурационной файл приложения
 */
$config = [
    'core' => [ // подмассив используемый самим ядром фреймворка
        'db' => [
            'dns' => 'mysql:host=localhost;dbname=dbname',
            'username' => 'root',
            'password' => '1234'
        ],
        'router' => [
            'class' => \ItForFree\SimpleMVC\Router::class    
        ],
        'url' => [ 
            'class' => \ItForFree\SimpleMVC\Url::class
        ],
        'mvc' => [
            'views' => [
                'base-template-path' => '../application/CMSviews/',
                'base-layouts-path' => '../application/CMSviews/layouts/',
                'footer-path' => '',
                'header-path' => ''
            ]
        ],
        'user' => [
            'class' => \application\models\CMSUser::class
        ],
        'session' => [
            'class' => ItForFree\SimpleMVC\Session::class
        ],
		'homepageNumArticles' => 5,
		'admin' => [
			'username' => 'admin',
			'password' => 'thundu14'
		]
    ]    
];

return $config;