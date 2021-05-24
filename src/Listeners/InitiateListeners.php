<?php declare(strict_types=1);

namespace VitesseCms\Datagroup\Listeners;

use Phalcon\Events\Manager;
use VitesseCms\Datagroup\Listeners\Admin\AdminMenuListener;

class InitiateListeners
{
    public static function setListeners(Manager $eventsManager): void
    {
        $eventsManager->attach('adminMenu', new AdminMenuListener());
    }
}
