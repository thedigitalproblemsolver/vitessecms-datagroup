<?php declare(strict_types=1);

namespace VitesseCms\Datagroup\Controllers;

use VitesseCms\Admin\AbstractAdminController;
use VitesseCms\Core\Interfaces\RepositoriesInterface;
use VitesseCms\Datagroup\Helpers\DatagroupHelper;
use VitesseCms\Datagroup\Models\Datagroup;
use VitesseCms\Mustache\DTO\RenderTemplateDTO;
use VitesseCms\Mustache\Enum\ViewEnum;

class AdminfixdatagroupsController extends AbstractAdminController implements RepositoriesInterface
{
    public function indexAction(): void
    {
        $this->view->setVar(
            'content',
            $this->eventsManager->fire(ViewEnum::RENDER_TEMPLATE_EVENT, new RenderTemplateDTO(
                'fix_datagroups',
                $this->router->getModuleName() . '/src/Resources/views/admin/'
            ))
        );
        $this->prepareView();
    }

    public function fixslugcategoriesAction(): void
    {
        $datagroups = $this->repositories->datagroup->findAll();
        $counter = 0;
        while ($datagroups->valid()):
            $datagroup = $datagroups->current();
            if ($datagroup->hasParent()) :
                $datagroupPath = DatagroupHelper::getPathFromRoot($datagroup);
                $datagroupPath = array_reverse($datagroupPath);
                unset($datagroupPath[0]);

                $slugCateories = $datagroup->getSlugCategories();
                if(count($slugCateories) !== count($datagroupPath)) :
                    $counter++;
                    $newSlugCategories = [];
                    $datagroupPath = array_reverse($datagroupPath);
                    foreach ($datagroupPath as $path) :
                        if(isset($slugCateories[(string)$path->getId()])):
                            $newSlugCategories[(string)$path->getId()] = [
                                'id' => (string)$path->getId(),
                                'published' => $slugCateories[(string)$path->getId()]['published']
                            ];
                        else :
                            $newSlugCategories[(string)$path->getId()] = [
                                'id' => (string)$path->getId(),
                                'published' => true
                            ];
                        endif;
                    endforeach;
                    $datagroup->setSlugCategories($newSlugCategories)->save();
                    $this->log->write(
                        $datagroup->getId(),
                        Datagroup::class,
                        'SlugCategories fixed for datagroup '.$datagroup->getNameField()
                    );
                endif;
            endif;
            $datagroups->next();
        endwhile;

        $this->flash->setSucces($counter.' datagroups fixed');
        $this->redirect();
    }
}