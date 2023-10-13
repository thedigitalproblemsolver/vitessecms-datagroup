<?php declare(strict_types=1);

namespace VitesseCms\Datagroup\Enums;

enum DatagroupEnum: string
{
    case LISTENER = 'DatagroupListener';
    case GET_REPOSITORY = 'DatagroupListener:getRepository';
}