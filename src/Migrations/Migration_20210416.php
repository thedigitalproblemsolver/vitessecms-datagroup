<?php

declare(strict_types=1);

namespace VitesseCms\Datagroup\Migrations;

use stdClass;
use VitesseCms\Database\AbstractMigration;
use VitesseCms\Datagroup\Enums\DatagroupEnum;

class Migration_20210416 extends AbstractMigration
{
    public function up(): bool
    {
        $result = true;
        if (!$this->parseDatagroups()) :
            $result = false;
        endif;

        return $result;
    }

    private function parseDatagroups(): bool
    {
        $result = true;
        $datagroupRepository = $this->eventsManager->fire(DatagroupEnum::GET_REPOSITORY->value, new stdClass());

        $datagroups = $datagroupRepository->findAll(null, false);
        $dir = str_replace(
            'install/src/Migrations',
            'core/src/Services/../../../../../vendor/vitessecms/mustache/src/',
            __DIR__
        );
        $search = [
            'default/',
            'templates/',
            'Templates/',
            'Template/core/',
            $dir
        ];
        $replace = [
            'core/',
            'Template/',
            'Template/',
            '',
            ''
        ];
        while ($datagroups->valid()):
            $datagroup = $datagroups->current();
            $template = str_replace($search, $replace, $datagroup->getTemplate());

            if (substr($template, 0, 6) === "views/") :
                $datagroup->setTemplate($template)->save();
            else :
                $this->terminalService->printError(
                    'wrong template "' . $template . '" for datagroup "' . $datagroup->getNameField() . '"'
                );
                $result = false;
            endif;

            $datagroups->next();
        endwhile;
        $this->terminalService->printMessage('Datagroups template repaired');

        return $result;
    }
}