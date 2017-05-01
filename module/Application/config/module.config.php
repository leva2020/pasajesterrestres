<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */
return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'index'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
	                'origen-destino' => array(
                	    'type' => 'Segment',
	                    'options' => array(
	                	    'route' => ':empresa[/:source][/:pstrLocalidadOrigen]',
	                	    'constraints' => array(
	                    	    'empresa' => '(ochoa)',
	                    	    'source' => '[a-zA-Z][a-zA-Z0-9_-]*',
	                    	    'pstrLocalidadOrigen' => '[0-9]+'
	                        ),
	                	    'defaults' => array(
	                    	    'controller' => 'Application\Controller\Integration',
	                    	    'action' => 'ochoa'
	                        )
	                    ),
	                    'may_terminate' => true,
	                    'child_routes' => array(
	                        'save-bd' => array(
	                            'type' => 'literal',
	                            'options' => array(
	                                'route' => '/save-bd',
	                                'defaults' => array(
	                                    'action' => 'save-cities-data-base'
	                                )
	                            ),
	                        ),
                        ),
                    ),
                    'tarifas' => array(
                    	'type' => 'Segment',
                    	'options' => array(
                    	    'route' => 'tarifas/:empresa',
                    	    'constraints' => array(
                    	        'empresa' => '(ochoa)'
                            ),
                            'defaults' => array(
                            	'controller' => 'Application\Controller\Integration',
                            	'action' => 'obtener-tarifas'
                            )
                        )
                    ),
                    'puestos' => array(
                    	'type' => 'Segment',
                    	'options' => array(
                    	    'route' => 'puestos/:empresa',
                    	    'constraints' => array(
                    	        'empresa' => '(ochoa)'
                            ),
                            'defaults' => array(
                            	'controller' => 'Application\Controller\Integration',
                            	'action' => 'puestos'
                            )
                        )
                    ),
                    'bloquear-puestos' => array(
                    	'type' => 'literal',
                    	'options' => array(
                    	    'route' => 'bloquear-puesto',
                    	    'defaults' => array(
                    	        'controller' => 'Application\Controller\Integration',
                    	        'action' => 'bloquear-puestos'
            	            )
                        )
                    ),
                    'estaticos' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => ':action',
                            'constraints' => array(
                                'action' => '(terminos-condiciones|politica-privacidad|quienes-somos)'
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Comun'
                            )
                        )
                    )
                )
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/application',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action' => 'index'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
                            ),
                            'defaults' => array()
                        )
                    )
                )
            ),
            'compra' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/compra',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Compra',
                        'action' => 'index'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'respuesta' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/respuesta-compra',
                            'constraints' => array(
                                'controller' => 'Application\Controller\Respuestas',
                                'action' => 'respuesta'
                            ),
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller' => 'Respuestas',
                                'action' => 'respuesta'
                            )
                        )
                    ),
                    'confirmacion' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/confirmacion-compra',
                            'constraints' => array(
                                'controller' => 'Application\Controller\Respuestas',
                                'action' => 'confirmacion'
                            ),
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller' => 'Respuestas',
                                'action' => 'confirmacion'
                            )
                        )
                    )
                )
            )
        )
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory'
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator'
        )
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo'
            )
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Application\Controller\Integration' => 'Application\Controller\IntegrationController',
            'Application\Controller\Compra' => 'Application\Controller\CompraController',
            'Application\Controller\Respuestas' => 'Application\Controller\RespuestasController',
            'Application\Controller\Comun' => 'Application\Controller\ComunController'
        )
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml'
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view'
        ) ,
        'strategies' => array(
            'ViewJsonStrategy',
        )
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array()
        )
    )
);