<?php

namespace App\Models\Concerns;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait HydrateWithRelation
{
    public static function hydrateWithRelations(array $items, array $relations = [])
    {
        // hydrate all models
        $models = self::hydrate($items);

        // for loop model
        foreach ($models as $model) {
            // we must connect relation into it's model by it's name, and it's foreign key, but the relation will always
            // be a many-to-many relations
            foreach ($relations as $relation) {
                // filter relations by it's foreign key to insert it into model relation
                $data = collect($relation['data'])->where($relation['foreign_key'], $model->getKey())->values();

                $model->setRelation($relation['name'], $data);
            }
        }

        return $models;
    }

    public function toArray()
    {
        $attributes = parent::toArray();

        return array_merge($attributes, $this->relations);
    }
}
