<?php declare(strict_types=1);

namespace VitesseCms\Datagroup\Factories;

use VitesseCms\Datafield\Models\Datafield;
use VitesseCms\Datagroup\Models\Datagroup;
use VitesseCms\Database\Utils\MongoUtil;
use stdClass;

class DatagroupFactory
{
    public static function create(
        string $title,
        string $template,
        string $component,
        array $datafields = [],
        bool $published = false,
        bool $includeInSitemap = false,
        string $parentId = null,
        string $itemOrdering = ''
    ): Datagroup
    {
        $datagroup = new Datagroup();
        $datagroup->set('name', $title, true);
        $datagroup->setTenplate($template)
            ->setComponent($component)
            ->setDatafields($datafields)
            ->setPublished($published)
            ->setSitemap($includeInSitemap)
            ->setParent($parentId)
            ->setItemOrdering($itemOrdering);

        if ($parentId !== null && MongoUtil::isObjectId($parentId)) :
            $parentDatagroup = Datagroup::findById($parentId);
            $parentDatagroup->set('hasChildren', true)->save();
        endif;

        return $datagroup;
    }

    public static function createDatafieldEntry(
        Datafield $datafield,
        bool $published = true,
        bool $required = false,
        bool $filterable = false
    ): array
    {
        return [
            'id' => (string)$datafield->getId(),
            'published' => $published,
            'required' => $required,
            'filterable' => $filterable,
        ];
    }
}
