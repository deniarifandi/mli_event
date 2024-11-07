<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->post('/daftar', 'Home::daftar');
$routes->get('/daftar_sukses', 'Home::daftar_sukses');

$routes->get('/admin','Home::admin');

$routes->get('/send_email','Home::send_email');
