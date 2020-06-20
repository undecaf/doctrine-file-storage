<?php

use TYPO3Fluid\Fluid\View\TemplateView;
use Entity\Image;

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../bootstrap.php';

$view = new TemplateView();
$paths = $view->getTemplatePaths();
$paths->setPartialRootPaths([ __DIR__.'/../Resources/Private/Partials' ]);
$paths->setTemplatePathAndFilename(__DIR__.'/../Resources/Private/Templates/Page.html');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Query all sizes of a certain name
        $query = $entityManager->createQueryBuilder()
            ->select('img')
            ->from(Image::class, 'img')
            ->where('img.name = :name')
            ->setParameter('name', $_GET['name'])
            ->orderBy('img.width', 'ASC')
            ->getQuery();
        $images = $query->getArrayResult();

        // Prepare the model for Fluid
        $model = [
            'title' => 'Images',
            'partial' => 'Images',
            'images' => $images,
        ];
        break;
    
    default:
        // Bad request
        header('Content-Type: application/octet-stream', true, 400);
        return;
}

$view->assignMultiple($model);
echo $view->render();
