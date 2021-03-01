<?php declare(strict_types=1);

namespace VitesseCms\Datagroup\Helpers;

use VitesseCms\Datafield\Models\Datafield;
use VitesseCms\Datagroup\Models\Datagroup;

class DatagroupHelper
{
    public static function getPathFromRoot(Datagroup $datagroup): array
    {
        return array_reverse(self::getPathToRoot($datagroup));
    }

    public static function getPathToRoot(Datagroup $datagroup, array $fields = []): array
    {
        $fields[] = $datagroup;
        if ($datagroup->getParentId() !== null) :
            $fields = self::getPathToRoot(
                Datagroup::findById($datagroup->getParentId()),
                $fields
            );
        endif;

        return $fields;
    }

    //TODO return iterator
    public static function getChildrenFromRoot(Datagroup $datagroup, array $datagroups = []): array
    {
        $datagroups[] = $datagroup;
        Datagroup::setFindValue('parentId', (string)$datagroup->getId());
        $groups = Datagroup::findAll();
        if (\count($groups)) :
            /** @var Datagroup $group */
            foreach ($groups as $group) :
                $datagroups = self::getChildrenFromRoot($group, $datagroups);
            endforeach;
        endif;

        return $datagroups;
    }

    public static function hasFilterableFields(Datagroup $datagroup): bool
    {
        foreach ((array)$datagroup->_('datafields') as $field) :
            if (!empty($field['filterable'])) :
                $datafield = Datafield::findById($field['id']);
                /** @var Datafield $datafield */
                if (\is_object($datafield) && $datafield->isPublished()) :
                    return true;
                endif;
            endif;
        endforeach;

        return false;
    }
}
