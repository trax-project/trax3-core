<?php

namespace Trax\Framework\Repo\Eloquent;

abstract class Grammar
{
    /**
     * Add a like condition to the query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder  $builder
     * @param  string  $prop
     * @param  mixed  $value
     * @param  bool  $orWhere
     * @return void
     */
    abstract public function likeCondition($builder, string $prop, $value, bool $orWhere);

    /**
     * Add a not equal condition to the query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder  $builder
     * @param  string  $prop
     * @param  mixed  $value
     * @param  bool  $orWhere
     * @return void
     */
    abstract public function notEqualCondition($builder, string $prop, $value, bool $orWhere);

    /**
     * Add a not in condition to the query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder  $builder
     * @param  string  $prop
     * @param  mixed  $value
     * @param  bool  $orWhere
     * @return void
     */
    abstract public function notInCondition($builder, string $prop, $value, bool $orWhere);

    /**
     * Add a JSON contains condition to the query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder  $builder
     * @param  string  $prop
     * @param  mixed  $value
     * @param  bool  $orWhere
     * @return void
     */
    abstract public function addJsonContainsCondition($builder, string $prop, $value, bool $orWhere);

    /**
     * Add a JSON search condition to the query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder  $builder
     * @param  string  $prop
     * @param  mixed  $value
     * @param  bool  $orWhere
     * @return void
     */
    abstract public function addJsonSearchCondition($builder, string $prop, $value, bool $orWhere);

    /**
     * Get the JSON candidate to be used with JSON_CONTAINS.
     *
     * E.g: ->name, child1  >  {"name" : "child1"}
     */
    protected function jsonObjectCandidate($property, $value)
    {
        $parts = explode('->', $property);
        array_shift($parts);
        $parts = array_reverse($parts);
        if (is_array($value)) {
            $candidate = '{"'.$parts[0].'" : ' . json_encode($value) . '}';
        } elseif (is_string($value)) {
            $candidate = '{"'.$parts[0].'" : "'.$value.'"}';
        } else {
            $candidate = '{"'.$parts[0].'" : '.$value.'}';
        }
        array_shift($parts);
        foreach ($parts as $prop) {
            $candidate = '{"'.$prop.'" : '.$candidate.'}';
        }
        return $candidate;
    }

    /**
     * Get the JSON target and path to be used with JSON_CONTAINS.
     *
     * E.g. 'meta->topic->tags'  >  ['meta', '$.topic.tags']
     *
     * E.g. 'meta->topic->tags[*]->name'  >  ['meta', '$.topic.tags[*].name']
     *
     * E.g. 'meta->topic->tags[*]'  >  ['meta', '$.topic.tags']
     *
     * @param  string  $property
     * @return array
     */
    protected function jsonTargetAndPath(string $property): array
    {
        $parts = explode('->', $property);
        $target = array_shift($parts);
        $path = empty($parts) ? '$' : '$.'.implode('.', $parts);
        return [$target, $path];
    }
}
