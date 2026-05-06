<?php

namespace App\Back\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollection;

class ImportAdmin extends AbstractAdmin
{
    /**
     * {@inheritdoc}
     */
    protected $baseRouteName = 'admin_app_core_import';

    /**
     * {@inheritdoc}
     */
    protected $baseRoutePattern = 'app/core/import';

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(array('list'));
        $collection->add(
            'import_traitement',
            'import-traitement',
            array('_controller' => 'App\Back\Controller\ImportAdminController::importTraitement')
        );
        $collection->add(
            'verrouillage_import',
            'verrouillage_import',
            array('_controller' => 'App\Back\Controller\ImportAdminController::verrouillageImport')
        );
        $collection->add('export', 'export');
        $collection->add('exportExterne', 'exportExterne');
    }
}
