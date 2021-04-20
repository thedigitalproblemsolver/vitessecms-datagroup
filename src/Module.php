<?php declare(strict_types=1);

namespace VitesseCms\Datagroup;

use VitesseCms\Admin\Utils\AdminUtil;
use VitesseCms\Core\AbstractModule;
use Phalcon\DiInterface;
use VitesseCms\Datafield\Repositories\DatafieldRepository;
use VitesseCms\Datagroup\Repositories\AdminRepositoryCollection;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;

class Module extends AbstractModule
{
    public function registerServices(DiInterface $di, string $string = null)
    {
        parent::registerServices($di, 'Datagroup');
        if (AdminUtil::isAdminPage()) :
            $di->setShared('repositories', new AdminRepositoryCollection(
                new DatagroupRepository(),
                new DatafieldRepository()
            ));
        endif;
    }
}
