<?php declare(strict_types=1);

namespace VitesseCms\Datagroup\Fields;

use VitesseCms\Content\Models\Item;
use VitesseCms\Core\Helpers\ItemHelper;
use VitesseCms\Database\AbstractCollection;
use VitesseCms\Database\Utils\MongoUtil;
use VitesseCms\Datafield\AbstractField;
use VitesseCms\Datafield\Models\Datafield;
use VitesseCms\Form\AbstractForm;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Form\Interfaces\AbstractFormInterface;
use VitesseCms\Form\Models\Attributes;
use VitesseCms\Sef\Utils\SefUtil;

class Datagroup extends AbstractField
{
    public static function beforeMaincontent(Item $item, Datafield $datafield): void
    {
        $test = $item->_($datafield->getCallingName());
        if (is_string($test) && MongoUtil::isObjectId($test)) :
            $object = Item::findById($item->_($datafield->getCallingName()));
            if ($object) :
                $item->set($datafield->getCallingName() . 'Display', $object);
            endif;
        endif;
    }

    public function buildItemFormElement(
        AbstractForm       $form,
        Datafield          $datafield,
        Attributes         $attributes,
        AbstractCollection $data = null
    )
    {
        Item::setFindValue('datagroup', $datafield->getDatagroup());
        if ($datafield->_('itemParent')) :
            Item::setFindValue('parentId', $datafield->_('itemParent'));
        endif;

        $options = [];
        /** @var Item $item */
        foreach (Item::findAll() as $item) :
            $value = (string)$item->getId();
            $name = [$item->getNameField()];

            if ($item->_('formValue')) :
                $value = $item->_('formValue');
            endif;

            if ($item->getParentId()) :
                $name = [];
                $pathItems = ItemHelper::getPathFromRoot($item);
                /** @var Item $pathItem */
                foreach ($pathItems as $pathItem) :
                    $name[] = $pathItem->getNameField();
                endforeach;
            endif;
            $options[$value] = implode(' > ', $name);
        endforeach;
        $options = array_flip($options);
        ksort($options);
        $options = array_flip($options);

        $attributes->setOptions(ElementHelper::arrayToSelectOptions($options));

        if ($datafield->_('multiple')) :
            $attributes->setMultiple()->setInputClass('select2');
        endif;

        $form->addDropdown(
            $datafield->getNameField(),
            $datafield->getCallingName(),
            $attributes
        );
    }

    public function renderFilter(AbstractFormInterface $filter, Datafield $datafield): void
    {
        Item::addFindOrder('name');
        Item::setFindValue('datagroup', $datafield->getDatagroup());
        if ($datafield->_('itemParent')) :
            Item::setFindValue('parentId', $datafield->_('itemParent'));
        endif;

        $filter->addDropdown(
            $datafield->getNameField(),
            $this->getFieldname($datafield) . '[]',
            (new Attributes())->setMultiple()
                ->setInputClass('select2')
                ->setNoEmptyText()
                ->setOptions(Item::findAll())
        );
    }

    public function renderAdminlistFilter(AbstractFormInterface $filter, Datafield $datafield): void
    {
        Item::addFindOrder('name');
        Item::setFindValue('datagroup', $datafield->getDatagroup());
        if ($datafield->_('itemParent')) :
            Item::setFindValue('parentId', $datafield->_('itemParent'));
        endif;

        $filter->addDropdown(
            $datafield->getNameField(),
            $this->getFieldname($datafield),
            (new Attributes())->setOptions(Item::findAll())
        );
    }

    public function renderSlugPart(AbstractCollection $item, string $languageShort, Datafield $datafield): string
    {
        $datagroupItem = Item::findById($item->_($datafield->_('calling_name')));
        if ($datagroupItem) :
            $slug = $datagroupItem->_('slug', $languageShort);
            if (is_string($slug)) :
                return SefUtil::generateSlugFromString($slug);
            endif;
        endif;

        return '';
    }
}
