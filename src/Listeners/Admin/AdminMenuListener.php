<?php declare(strict_types=1);

namespace VitesseCms\Datagroup\Listeners\Admin;

use VitesseCms\Admin\Models\AdminMenu;
use VitesseCms\Admin\Models\AdminMenuNavBarChildren;
use Phalcon\Events\Event;

class AdminMenuListener
{
    public function AddChildren(Event $event, AdminMenu $adminMenu): void
    {
        if ($adminMenu->getUser()->getPermissionRole() === 'superadmin') :
            $children = new AdminMenuNavBarChildren();
            $children->addChild('Data groups', 'admin/datagroup/admindatagroup/adminList');
            $adminMenu->addDropdown('DataDesign', $children);
        endif;
    }
}
