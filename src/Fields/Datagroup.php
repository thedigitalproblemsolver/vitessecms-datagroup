<?php

declare(strict_types=1);

namespace VitesseCms\Datagroup\Fields;

use VitesseCms\Content\Enum\ItemEnum;
use VitesseCms\Content\Models\Item;
use VitesseCms\Content\Repositories\ItemRepository;
use VitesseCms\Core\Helpers\ItemHelper;
use VitesseCms\Database\AbstractCollection;
use VitesseCms\Database\Models\FindOrder;
use VitesseCms\Database\Models\FindOrderIterator;
use VitesseCms\Database\Models\FindValue;
use VitesseCms\Database\Models\FindValueIterator;
use VitesseCms\Database\Utils\MongoUtil;
use VitesseCms\Datafield\AbstractField;
use VitesseCms\Datafield\Models\Datafield;
use VitesseCms\Form\AbstractForm;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Form\Interfaces\AbstractFormInterface;
use VitesseCms\Form\Models\Attributes;
use VitesseCms\Sef\Utils\SefUtil;

final class Datagroup extends AbstractField
{
    private readonly ItemRepository $itemRepository;

    public function __construct()
    {
        parent::__construct();

        $this->itemRepository = $this->eventsManager->fire(ItemEnum::GET_REPOSITORY, new \stdClass());
    }

    public static function beforeMaincontent(Item $item, Datafield $datafield): void
    {
        $test = $item->_($datafield->getCallingName());
        if (is_string($test) && MongoUtil::isObjectId($test)) {
            $object = Item::findById($item->_($datafield->getCallingName()));
            if ($object) {
                $item->set($datafield->getCallingName().'Display', $object);
            }
        }
    }

    public function buildItemFormElement(
        AbstractForm $form,
        Datafield $datafield,
        Attributes $attributes,
        AbstractCollection $data = null
    ): void {
        $findValues = new FindValueIterator([new FindValue('datagroup', $datafield->getDatagroup())]);

        if ($datafield->has('itemParent')) {
            $findValues->add(new FindValue('parentId', $datafield->getString('itemParent')));
        }

        $items = $this->itemRepository->findAll($findValues);

        $options = [];
        while ($items->valid()) {
            $item = $items->current();
            $value = (string) $item->getId();
            $name = [$item->getNameField()];

            if ($item->has('formValue')) {
                $value = $item->getString('formValue');
            }

            if ($item->getParentId()) {
                $name = [];
                $pathItems = ItemHelper::getPathFromRoot($item);
                /** @var Item $pathItem */
                foreach ($pathItems as $pathItem) {
                    $name[] = $pathItem->getNameField();
                }
            }
            $options[$value] = implode(' > ', $name);
            $items->next();
        }
        $options = array_flip($options);
        ksort($options);
        $options = array_flip($options);

        $attributes->setOptions(ElementHelper::arrayToSelectOptions($options));

        if ($datafield->_('multiple')) {
            $attributes->setMultiple()->setInputClass('select2');
        }

        $form->addDropdown(
            $datafield->getNameField(),
            $datafield->getCallingName(),
            $attributes
        );
    }

    public function renderFilter(AbstractFormInterface $filter, Datafield $datafield): void
    {
        $findValues = new FindValueIterator([new FindValue('datagroup', $datafield->getDatagroup())]);

        if ($datafield->has('itemParent')) {
            $findValues->add(new FindValue('parentId', $datafield->getString('itemParent')));
        }

        $filter->addDropdown(
            $datafield->getNameField(),
            $this->getFieldname($datafield).'[]',
            (new Attributes())->setMultiple()
                ->setInputClass('select2')
                ->setNoEmptyText()
                ->setOptions(
                    ElementHelper::modelIteratorToOptions(
                        $this->itemRepository->findAll(
                            $findValues,
                            true,
                            null,
                            new FindOrderIterator([new FindOrder('name', 1)])
                        )
                    )
                )
        );
    }

    public function renderAdminlistFilter(AbstractFormInterface $filter, Datafield $datafield): void
    {
        $findValues = new FindValueIterator([new FindValue('datagroup', $datafield->getDatagroup())]);

        if ($datafield->has('itemParent')) {
            $findValues->add(new FindValue('parentId', $datafield->getString('itemParent')));
        }

        $filter->addDropdown(
            $datafield->getNameField(),
            $this->getFieldname($datafield),
            (new Attributes())->setOptions(
                ElementHelper::modelIteratorToOptions(
                    $this->itemRepository->findAll(
                        $findValues,
                        true,
                        null,
                        new FindOrderIterator([new FindOrder('name', 1)])
                    )
                )
            )
        );
    }

    public function renderSlugPart(AbstractCollection $item, string $languageShort, Datafield $datafield): string
    {
        $datagroupItem = $this->itemRepository->getById($item->getString($datafield->getCallingName()));
        if (null !== $datagroupItem) {
            $slug = $datagroupItem->getString('slug', $languageShort);
            if (!empty($slug)) {
                return SefUtil::generateSlugFromString($slug);
            }
        }

        return '';
    }
}
