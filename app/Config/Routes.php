<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

/*
 * We add the article index page as the default route. It will autogenerate an index of topics to start browsing
 */

$routes->get('/admin/generate-pending-articles', 'Articles::generatePendingArticles');

$routes->get('/json/get-title-and-content/(:segment)/(:segment)', 'Articles::getTitleAndContentParagraphs/$1/$2');
$routes->post('/json/get-glossary', 'Articles::getGlossary');
$routes->post('/json/get-interesting-facts', 'Articles::getInterestingFacts');
$routes->post('/json/get-further-readings', 'Articles::getFurtherReads');
$routes->post('/generate-from-news-article', 'Articles::generateFromNewsArticle');
$routes->post('/json/generate-from-url', 'Articles::getTitleAndContentParagraphsFromURL');
$routes->post('/generate-from-url', 'Articles::newArticleFromURL');

$routes->get('/', 'Articles::index');

/*
 * We add the first level articles to be generated by the AI article generator helper passing the topic as
 * the first segment of the URL.
 */
$routes->get('/(:segment)', 'Articles::fromTopic/$1');

/*
 * We add the second level articles to be generated by the AI article generator helper passing the source article
 * slug as the first segment of the URL and the slug of the destination article to be generated.
 */

//$routes->get('/(:segment)/(:segment)', 'Articles::nextArticle/$1/$2');

$routes->get('/(:segment)/(:segment)', 'Articles::nextArticleTemplate/$1/$2');
/*
 * We add the routes for AJAX loading the different parts of the article.
 */
