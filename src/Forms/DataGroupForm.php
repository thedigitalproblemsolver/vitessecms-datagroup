<?php declare(strict_types=1);

namespace VitesseCms\Datagroup\Forms;

use VitesseCms\Block\Utils\BlockUtil;
use VitesseCms\Core\Enum\SystemEnum;
use VitesseCms\Core\Interfaces\RepositoryInterface;
use VitesseCms\Datagroup\Models\Datagroup;
use VitesseCms\Form\AbstractFormWithRepository;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Form\Interfaces\FormWithRepositoryInterface;
use VitesseCms\Form\Models\Attributes;

class DataGroupForm extends AbstractFormWithRepository
{
    public function buildForm(): FormWithRepositoryInterface
    {
        $this->addText('%CORE_NAME%', 'name', (new Attributes())->setRequired()->setMultilang());

        $files = BlockUtil::getTemplateFiles('MainContent', $this->configuration);
        $options = [];
        foreach ($files as $key => $label) :
            $selected = false;
            if ($this->entity->getTemplate() === $key) :
                $selected = true;
            endif;
            $options[] = [
                'value' => $key,
                'label' => $label,
                'selected' => $selected,
            ];
        endforeach;
        $this->addDropdown(
            '%ADMIN_CHOOSE_A_TEMPLATE%',
            'template',
            (new Attributes())->setRequired()->setOptions($options))
            ->addDropdown(
                '%ADMIN_CMS_COMPONENT%',
                'component',
                (new Attributes())->setRequired()->setOptions(ElementHelper::arrayToSelectOptions(SystemEnum::COMPONENTS)))
            ->addNumber('%ADMIN_ORDERING%', 'ordering')
            ->addDropdown(
                '%ADMIN_DATAGROUP_ITEM_ORDER%',
                'itemOrdering',
                (new Attributes())->setOptions(ElementHelper::arrayToSelectOptions([
                    '' => '%ADMIN_ITEM_ORDER_NAME%',
                    'ordering' => '%ADMIN_ITEM_ORDER_ORDERING%',
                    'createdAt' => 'Created date',
                ])))
            ->addNumber('%ADMIN_ORDERING%', 'page_limit')
            ->addDropdown(
                '%ADMIN_DATAFIELD%',
                'datafield',
                (new Attributes())->setInputClass('select2')
                    ->setOptions(ElementHelper::modelIteratorToOptions($this->repositories->datafield->findAll())))
            ->addHtml($this->entity->_('dataHtml'))
            ->addText(
                'Category slug delimiter',
                'slugCategoryDelimiter',
                (new Attributes())->setRequired()->setInputClass('noLengthCheck')->setDefaultValue('/'))
            ->addText(
                '%ADMIN_SLUG_DELIMITER%',
                'slugDelimiter',
                (new Attributes())->setRequired()->setInputClass('noLengthCheck')->setDefaultValue('-'))
            ->addToggle('%ADMIN_SITEMAP_INCLUDE%', 'sitemap');

        if (empty($this->entity->getParentId())) :
            $this->addToggle('%ADMIN_SORTABLE_LIST%', 'sortable');
        endif;

        $this->addSubmitButton('%CORE_SAVE%');

        return $this;
    }
}
