<?php
declare(strict_types=1);

namespace VitesseCms\Datagroup\Listeners;

use VitesseCms\Core\Interfaces\InitiateListenersInterface;
use VitesseCms\Core\Interfaces\InjectableInterface;
use VitesseCms\Datafield\Repositories\DatafieldRepository;
use VitesseCms\Datagroup\Blocks\Datagroup;
use VitesseCms\Datagroup\Controllers\AdmindatagroupController;
use VitesseCms\Datagroup\Enums\DatagroupEnum;
use VitesseCms\Datagroup\Listeners\Admin\AdminMenuListener;
use VitesseCms\Datagroup\Listeners\Blocks\BlockDatagroupListener;
use VitesseCms\Datagroup\Listeners\Controllers\AdmindatagroupControllerListener;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;

class InitiateAdminListeners implements InitiateListenersInterface
{
    public static function setListeners(InjectableInterface $injectable): void
    {
        $injectable->eventsManager->attach('adminMenu', new AdminMenuListener());
        $injectable->eventsManager->attach(AdmindatagroupController::class, new AdmindatagroupControllerListener());
        $injectable->eventsManager->attach(
            Datagroup::class,
            new BlockDatagroupListener(
                new DatagroupRepository(),
                new DatafieldRepository()
            )
        );
        $injectable->eventsManager->attach(
            DatagroupEnum::LISTENER->value,
            new DatagroupListner(new DatagroupRepository())
        );
    }
}
