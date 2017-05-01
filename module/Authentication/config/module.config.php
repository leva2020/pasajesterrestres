<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Authentication\Controller\AuthServer' => 'Authentication\Controller\AuthServerController',
            'Authentication\Controller\SocialNetwork' => 'Authentication\Controller\SocialNetworkController'
        )
    ),
    'router' => array(
        'routes' => array(
            'authentication' => array(
                'type' => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route' => '/auth',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'Authentication\Controller',
                        'controller' => 'AuthServer',
                        'action' => 'index'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'logout' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/logout',
                            'defaults' => array(
                                'action' => 'logout'
                            )
                        )
                    ),
                    'login' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/login[/:client_id][/:grant_type][/:response_type][/:scope][/:state][/:type]',
                            'defaults' => array(
                                'action' => 'login',
                                'type' => 'default',
                                'client_id' => 'authserver',
                                'grant_type' => 'authorization_code',
                                'response_type' => 'code',
                                'scope' => 'login'
                            ),
                            'constraints' => array(
                                'type' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                'client_id' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                'grant_type' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                'response_type' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                'scope' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                'state' => '[a-zA-Z0-9_-]+',
                            ),
                        )
                    ),
                    'grant-token' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/grant-token',
                            'defaults' => array(
                                'action' => 'grant-token'
                            )
                        )
                    )
                )
            ),
            'social' => array(
                'type' => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route' => '/social',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'Authentication\Controller',
                        'controller' => 'SocialNetwork',
                        'action' => 'index'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'google-oauth2' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/google-oauth2-callback[/:client_id][/:login_type]',
                            'defaults' => array(
                                'action' => 'google-oauth2-callback',
                                'client_id' => 'authserver',
                                'login_type' => 'default'
                            ),
                            'constraints' => array(
                                'client_id' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                'login_type' => '[a-zA-Z][a-zA-Z0-9_-]+',
                            ),
                        )
                    )
                )
            )
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Authentication' => __DIR__ . '/../view'
        )
    )
);
