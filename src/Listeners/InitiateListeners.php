<?php
declare(strict_types=1);

namespace VitesseCms\Datagroup\Listeners;

use VitesseCms\Core\Interfaces\InitiateListenersInterface;
use VitesseCms\Core\Interfaces\InjectableInterface;
use VitesseCms\Datagroup\Enums\DatagroupEnum;
use VitesseCms\Datagroup\Listeners\Admin\AdminMenuListener;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;

class InitiateListeners implements InitiateListenersInterface
{
    public static function setListeners(InjectableInterface $injectable): void
    {
        if ($injectable->user->hasAdminAccess()) :
            $injectable->eventsManager->attach('adminMenu', new AdminMenuListener());
        endif;
        $injectable->eventsManager->attach(
            DatagroupEnum::LISTENER->value,
            new DatagroupListner(new DatagroupRepository())
        );
    }
}
