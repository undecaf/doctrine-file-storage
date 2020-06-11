<?php

use TYPO3Fluid\Fluid\View\TemplateView;
use Entity\File;

const UUID_PARAM = 'uuid';

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../bootstrap.php';

try {
    // Get the requested file
    $query = $entityManager->createQueryBuilder()
        ->select('f')
        ->from('Entity\File', 'f')
        ->where('f.uuid = :uuid')
        ->setParameter('uuid', $_GET['uuid'])
        ->getQuery();
    $file = $query->getSingleResult();

    // Build headers for inline or downloaded content
    header('Content-Type: ' . $file->getMimetype());
    if (isset($_GET['dl']))
    {
        header('Content-Disposition: attachment; filename="' . $file->getName() . '"');
    }

    // Serve the content
    fpassthru($file->getContent());

} catch (Exception $ex) {
    header('Content-Type: application/octet-stream', true, 404);
}
