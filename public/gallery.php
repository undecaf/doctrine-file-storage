<?php

use TYPO3Fluid\Fluid\View\TemplateView;
use Entity\Image;

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../bootstrap.php';

const FILE_FIELD = 'file';
const TN_SIZE = 100;

$view = new TemplateView();
$paths = $view->getTemplatePaths();
$paths->setPartialRootPaths([ __DIR__.'/../Resources/Private/Partials' ]);
$paths->setTemplatePathAndFilename(__DIR__.'/../Resources/Private/Templates/Page.html');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        // Persist the uploaded image in original size and down-scaled
        $file = $_FILES[FILE_FIELD];
        foreach([ null, TN_SIZE, TN_SIZE*2, TN_SIZE*4 ] as $max_dimension) 
        {
            $entityManager->persist(new Image($file, $max_dimension));
        }
        $entityManager->flush();

        // Prevent re-POST if browser is refreshed
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;

    case 'GET':
        // Query all thumbnails
        $query = $entityManager->createQueryBuilder()
            ->select('img')
            ->from(Image::class, 'img')
            ->where('img.width <= :tn_size and img.height <= :tn_size')
            ->setParameter('tn_size', TN_SIZE)
            ->orderBy('img.name', 'ASC')
            ->getQuery();
        $images = $query->getArrayResult();

        // Prepare the model for Fluid
        $model = [
            'title' => 'Gallery',
            'partial' => 'Gallery',
            'images' => $images,
            'action' => $_SERVER['PHP_SELF'],
            'fileField' => FILE_FIELD,
        ];
        break;
    
    default:
        // Bad request
        header('Content-Type: application/octet-stream', true, 400);
        exit;
}

$view->assignMultiple($model);
echo $view->render();
