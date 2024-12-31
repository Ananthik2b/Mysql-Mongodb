<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Router\RouteCollection;

/**
 * Configure your routes here.
 *
 * @var RouteCollection $routes
 */
$routes = Services::routes();

// Default route to show the list of records and the create form
$routes->get('/', 'RecordController::index');

// Route to create a new record
$routes->post('record/create', 'RecordController::create');

// Route to update an existing record by ID
$routes->post('record/update/(:num)', 'RecordController::update/$1');

// Route to delete an existing record by ID
$routes->post('record/delete/(:num)', 'RecordController::delete/$1');
