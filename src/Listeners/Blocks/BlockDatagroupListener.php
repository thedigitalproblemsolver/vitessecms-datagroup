<?php declare(strict_types=1);

namespace VitesseCms\Datagroup\Listeners\Blocks;

use Phalcon\Events\Event;
use VitesseCms\Block\Forms\BlockForm;
use VitesseCms\Block\Models\Block;
use VitesseCms\Datafield\Models\DatafieldIterator;
use VitesseCms\Datafield\Repositories\DatafieldRepository;
use VitesseCms\Datagroup\Fields\Datagroup;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Form\Models\Attributes;

class BlockDatagroupListener
{
    /**
     * @var DatagroupRepository
     */
    private $datagroupRepository;

    /**
     * @var DatafieldRepository
     */
    private $datafieldRepository;

    public function __construct(
        DatagroupRepository $datagroupRepository,
        DatafieldRepository $datafieldRepository
    ){
        $this->datagroupRepository = $datagroupRepository;
        $this->datafieldRepository = $datafieldRepository;
    }

    public function buildBlockForm(Event $event, BlockForm $form, Block $block): void
    {
        $form->addDropdown(
            '%ADMIN_DATAGROUP%',
            'datagroup',
            (new Attributes())
                ->setInputClass('select2')
                ->setOptions(ElementHelper::modelIteratorToOptions($this->datagroupRepository->findAll()))
        );

        if (!empty($block->_('datagroup'))):
            $datagroup = $this->datagroupRepository->getById($block->_('datagroup'));
            if ($datagroup !== null):
                $datafieldsIterator = new DatafieldIterator();
                foreach ($datagroup->getDatafields() as $datafieldArray) :
                    $datafield = $this->datafieldRepository->getById($datafieldArray['id']);
                    if ($datafield !== null):
                        if ($datafield->getType() === Datagroup::class) :
                            $datafieldsIterator->add($datafield);
                        endif;
                    endif;
                endforeach;
                if ($datafieldsIterator->count()) :
                    $form->addDropdown(
                        '%ADMIN_DATAFIELD%',
                        'datafield',
                        (new Attributes())
                            ->setOptions(ElementHelper::modelIteratorToOptions($datafieldsIterator))
                            ->setRequired(true)
                    );
                endif;
            endif;
        endif;
    }
}
