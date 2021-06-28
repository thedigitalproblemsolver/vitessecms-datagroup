<?php declare(strict_types=1);

namespace VitesseCms\Datagroup\Migrations;

use VitesseCms\Cli\Services\TerminalServiceInterface;
use VitesseCms\Configuration\Services\ConfigServiceInterface;
use VitesseCms\Datafield\Repositories\DatafieldRepository;
use VitesseCms\Datagroup\Repositories\AdminRepositoryCollection;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;
use VitesseCms\Install\Interfaces\MigrationInterface;

class Migration_20210416 implements MigrationInterface
{
    /**
     * @var AdminRepositoryCollection
     */
    private $repository;

    public function __construct()
    {
        $this->repository = new AdminRepositoryCollection(
            new DatagroupRepository(),
            new DatafieldRepository()
        );
    }

    public function up(
        ConfigServiceInterface $configService,
        TerminalServiceInterface $terminalService
    ): bool
    {
        $result = true;
        if (!$this->parseDatagroups($terminalService)) :
            $result = false;
        endif;

        return $result;
    }

    private function parseDatagroups(TerminalServiceInterface $terminalService): bool
    {
        $result = true;
        $datagroups = $this->repository->datagroup->findAll(null, false);
        $dir = str_replace('install/src/Migrations', 'core/src/Services/../../../../../vendor/vitessecms/mustache/src/', __DIR__);
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
                $terminalService->printError('wrong template "' . $template . '" for datagroup "' . $datagroup->getNameField() . '"');
                $result = false;
            endif;

            $datagroups->next();
        endwhile;
        $terminalService->printMessage('Datagroups template repaired');

        return $result;
    }
}