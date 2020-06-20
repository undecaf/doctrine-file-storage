<?php

use TYPO3Fluid\Fluid\View\TemplateView;
use Entity\File;

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../bootstrap.php';

const FILE_FIELD = 'file';

$view = new TemplateView();
$paths = $view->getTemplatePaths();
$paths->setPartialRootPaths([ __DIR__.'/../Resources/Private/Partials' ]);
$paths->setTemplatePathAndFilename(__DIR__.'/../Resources/Private/Templates/Page.html');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        // Persist the uploaded file
        $file = $_FILES[FILE_FIELD];
        $entity = new File($file);
        $entityManager->persist($entity);
        $entityManager->flush();

        // Prevent re-POST if browser is refreshed
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;

    case 'GET':
        // Query all uploaded files
        $query = $entityManager->createQueryBuilder()
            ->select('file')
            ->from(File::class, 'file')
            ->orderBy('file.name', 'ASC')
            ->getQuery();
        $files = $query->getArrayResult();

        // Prepare the model for Fluid
        $model = [
            'title' => 'File storage',
            'partial' => 'Files',
            'files' => $files,
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
