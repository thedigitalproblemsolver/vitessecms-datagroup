<?php declare(strict_types=1);

namespace VitesseCms\Datagroup\Listeners;

use Phalcon\Events\Manager;
use VitesseCms\Datafield\Repositories\DatafieldRepository;
use VitesseCms\Datagroup\Blocks\Datagroup;
use VitesseCms\Datagroup\Controllers\AdmindatagroupController;
use VitesseCms\Datagroup\Listeners\Admin\AdminMenuListener;
use VitesseCms\Datagroup\Listeners\Blocks\BlockDatagroupListener;
use VitesseCms\Datagroup\Listeners\Controllers\AdmindatagroupControllerListener;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;

class InitiateAdminListeners
{
    public static function setListeners(Manager $eventsManager): void
    {
        $eventsManager->attach('adminMenu', new AdminMenuListener());
        $eventsManager->attach(AdmindatagroupController::class, new AdmindatagroupControllerListener());
        $eventsManager->attach(Datagroup::class, new BlockDatagroupListener(
            new DatagroupRepository(),
            new DatafieldRepository()
        ));
    }
}
