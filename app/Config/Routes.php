<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->post('/daftar', 'Home::daftar');
$routes->get('/daftar_sukses', 'Home::daftar_sukses');
$routes->get('/daftar_gagal', 'Home::daftar_gagal');

$routes->get('/admin','Home::admin');

$routes->get('/send_ticket','Home::send_ticket');

$routes->get('/tiket_sent','Home::tiket_sent');
$routes->get('/tiket_notsent','Home::tiket_notsent');

$routes->get('/tiket',"Home::tiket");