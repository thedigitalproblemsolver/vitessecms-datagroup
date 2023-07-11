<?php declare(strict_types=1);

namespace VitesseCms\Datagroup\Listeners;

use VitesseCms\Core\Interfaces\InitiateListenersInterface;
use VitesseCms\Core\Interfaces\InjectableInterface;
use VitesseCms\Datagroup\Enums\DatagroupEnum;
use VitesseCms\Datagroup\Listeners\Admin\AdminMenuListener;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;

class InitiateListeners implements InitiateListenersInterface
{
    public static function setListeners(InjectableInterface $di): void
    {
        if ($di->user->hasAdminAccess()) :
            $di->eventsManager->attach('adminMenu', new AdminMenuListener());
        endif;
        $di->eventsManager->attach(DatagroupEnum::LISTENER->value, new DatagroupListner(new DatagroupRepository()));
    }
}
