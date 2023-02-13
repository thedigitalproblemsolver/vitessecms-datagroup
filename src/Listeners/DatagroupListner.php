<?php declare(strict_types=1);

namespace VitesseCms\Datagroup\Listeners;

use VitesseCms\Datagroup\Repositories\DatagroupRepository;

class DatagroupListner
{
    public function __construct(private readonly DatagroupRepository $datagroupRepository)
    {
    }

    public function getRepository(): DatagroupRepository
    {
        return $this->datagroupRepository;
    }
}