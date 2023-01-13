<?php declare(strict_types=1);

namespace VitesseCms\Datagroup\Models;

use VitesseCms\Admin\Utils\AdminUtil;
use VitesseCms\Database\AbstractCollection;
use VitesseCms\Datafield\AbstractField;
use VitesseCms\Datafield\Models\Datafield;
use VitesseCms\Datafield\Repositories\DatafieldRepository;
use VitesseCms\Datagroup\Factories\DatagroupFactory;
use VitesseCms\Form\AbstractForm;
use VitesseCms\Form\Models\Attributes;

class Datagroup extends AbstractCollection
{
    /**
     * @var array
     */
    public $slugDatafields;
    /**
     * @var array
     */
    public $seoTitleDatafields;
    /**
     * @var array
     */
    public $seoTitleCategories;
    /**
     * @var array
     */
    public $datafields;
    /**
     * @var array
     */
    public $slugCategories;
    /**
     * @var bool
     */
    public $hasFilterableFields;
    /**
     * @var string
     */
    public $slugDelimiter;

    /**
     * @var string
     */
    public $slugCategoryDelimiter;
    /**
     * @var string
     */
    public $template;
    /**
     * @var string
     */
    public $component;
    /**
     * @var bool
     */
    public $sitemap;
    /**
     * @var string
     */
    public $itemOrdering;
    /**
     * @var array
     */
    protected $excludeFields;

    public function onConstruct(): void
    {
        parent::onConstruct();

        $this->excludeFields = [];
        $this->slugDatafields = [];
        $this->seoTitleDatafields = [];
    }

    public function afterFetch()
    {
        parent::afterFetch();
        if (AdminUtil::isAdminPage()) :
            $this->adminListName = ucfirst($this->_('component')) . ' : ' . $this->getNameField();
        endif;
    }

    //TODO move to listener
    public function buildItemForm(AbstractForm $form, AbstractCollection $data = null): void
    {
        $datafieldRepository = new DatafieldRepository();
        foreach ($this->getDatafields() as $fieldId => $params) :
            if ($params['published'] !== false) :
                $datafield = $datafieldRepository->getById($params['id']);
                if ($datafield && !isset($this->excludeFields[$datafield->getCallingName()])) :
                    $class = $datafield->getType();
                    /** @var AbstractField $field */
                    $field = new $class();
                    $attributes = new Attributes();
                    if ($datafield->isMultilang() && AdminUtil::isAdminPage()) :
                        $attributes->setMultilang();
                    endif;

                    if (!empty($params['required'])) :
                        $attributes->setRequired();
                    endif;

                    if ($data !== null) :
                        $attributes->setDefaultValue($data->_($datafield->_('calling_name')));
                    elseif (isset($datafield->defaultValue)) :
                        $attributes->setDefaultValue($datafield->defaultValue);
                    endif;
                    $field->buildItemFormElement($form, $datafield, $attributes, $data);

                    $this->getDI()->get('eventsManager')->fire($datafield->getType() . ':buildItemFormElement', $form, $data);

                endif;
            endif;
        endforeach;

        if ($data !== null && $data->getId()) :
            $form->addHidden('id', (string)$data->getId());
        endif;
    }

    public function getDatafields(): array
    {
        return $this->datafields ?? [];
    }

    public function setDatafields(array $datafields): Datagroup
    {
        $this->datafields = $datafields;

        return $this;
    }

    public function getAdminlistName(): string
    {
        return ucfirst($this->_('component')) . ' : ' . $this->_('name');
    }

    public function addExcludeField(string $calling_name): void
    {
        $this->excludeFields[$calling_name] = true;
    }

    public function addDatafield(Datafield $datafield): Datagroup
    {
        if (!isset($this->datafields)) :
            $this->datafields = [];
        endif;

        $dataFields = (array)$this->datafields;
        $datafieldId = (string)$datafield->getId();
        if (!isset($dataFields[$datafieldId])) :
            $dataFields[$datafieldId] = DatagroupFactory::createDatafieldEntry($datafield);
            $this->datafields = $dataFields;
        endif;

        return $this;
    }

    public function getSlugDatafields(): array
    {
        return $this->slugDatafields ?? [];
    }

    public function setSlugDatafields(array $slugDatafields): Datagroup
    {
        $this->slugDatafields = $slugDatafields;

        return $this;
    }

    public function getSlugCategories(): array
    {
        return $this->slugCategories ?? [];
    }

    public function setSlugCategories(array $slugCategories): Datagroup
    {
        $this->slugCategories = $slugCategories;

        return $this;
    }

    public function getSeoTitleDatafields(): array
    {
        return $this->seoTitleDatafields ?? [];
    }

    public function setSeoTitleDatafields(array $seoTitleDatafields): Datagroup
    {
        $this->seoTitleDatafields = $seoTitleDatafields;

        return $this;
    }

    public function getSeoTitleCategories(): array
    {
        return $this->seoTitleCategories ?? [];
    }

    public function setSeoTitleCategories(array $seoTitleCategories): Datagroup
    {
        $this->seoTitleCategories = $seoTitleCategories;

        return $this;
    }

    public function hasFilterableFields(): bool
    {
        return (bool)$this->hasFilterableFields;
    }

    public function getSlugDelimiter(): string
    {
        return $this->slugDelimiter ?? '';
    }

    public function slugCategoryDelimiter(): string
    {
        return $this->slugCategoryDelimiter ?? '';
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function setTemplate(string $template): Datagroup
    {
        $this->template = $template;

        return $this;
    }

    public function setComponent(string $component): Datagroup
    {
        $this->component = $component;

        return $this;
    }

    public function setSitemap(bool $sitemap): Datagroup
    {
        $this->sitemap = $sitemap;

        return $this;
    }

    public function setItemOrdering(string $itemOrdering): Datagroup
    {
        $this->itemOrdering = $itemOrdering;

        return $this;
    }
}
