<?php declare(strict_types=1);

namespace VitesseCms\Datagroup\Listeners;

use Phalcon\Events\Manager;
use VitesseCms\Datagroup\Blocks\Datagroup;
use VitesseCms\Datagroup\Controllers\AdmindatagroupController;

class InitiateAdminListeners
{
    public static function setListeners(Manager $eventsManager): void
    {
        $eventsManager->attach('adminMenu', new AdminMenuListener());
        $eventsManager->attach(AdmindatagroupController::class, new AdmindatagroupControllerListener());
        $eventsManager->attach(Datagroup::class, new BlockDatagroupListener());
    }
}
