<?php

namespace Trax\Framework\Repo\Eloquent;

class PostgreSqlGrammar extends Grammar
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
    public function likeCondition($builder, string $prop, $value, bool $orWhere)
    {
        $where = $orWhere ? 'orWhere' : 'where';

        // PostreSQL is case sensitive by default.
        // We make it case insensitive.
        return $builder->$where($prop, 'ilike', '%' . $value . '%');
    }

    /**
     * Add a not equal condition to the query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder  $builder
     * @param  string  $prop
     * @param  mixed  $value
     * @param  bool  $orWhere
     * @return void
     */
    public function notEqualCondition($builder, string $prop, $value, bool $orWhere)
    {
        $where = $orWhere ? 'orWhere' : 'where';

        // PostreSQL does not include NULL values in notEqual answers.
        // So we must use "is distinct from".
        return $builder->$where($prop, 'is distinct from', $value);
    }

    /**
     * Add a not in condition to the query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder  $builder
     * @param  string  $prop
     * @param  mixed  $value
     * @param  bool  $orWhere
     * @return void
     */
    public function notInCondition($builder, string $prop, $value, bool $orWhere)
    {
        // PostreSQL does not include NULL values in whereNotIn answers.
        // So we must use COALESCE($prop,'') not in (...)
        $qmarks = implode(',', array_map(function ($val) {
            return '?';
        }, $value));
        $where = $orWhere ? 'orWhere' : 'where';
        return $builder->{$where.'Raw'}("$prop not in ($qmarks) or $prop is null", [$value]);
    }

    /**
     * Add a JSON contains condition to the query builder.
     *
     * In an array of strings: 'meta->topic->tags[*]' => 'aicc'
     * > where (meta #> '{topic,tags}')::jsonb @> '"aicc"'::jsonb
     *
     * In an array of objects: 'meta->children[*]->name' => 'child1'
     * > where (meta #> '{children}')::jsonb @> '[{"name" : "child1"}]'::jsonb
     *
     * In an array of objects: 'meta->children[*]' => ['name' => 'child1', 'age' => 10]
     * > where (meta #> '{children}')::jsonb @> '[{"name" : "child1", "age" : 10}]'::jsonb
     *
     * @param  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder  $builder
     * @param  string  $prop
     * @param  mixed  $value
     * @param  bool  $orWhere
     * @return void
     */
    public function addJsonContainsCondition($builder, string $prop, $value, bool $orWhere)
    {
        $where = $orWhere ? 'orWhereRaw' : 'whereRaw';
        $parts = explode('[*]', $prop);

        if (empty($parts[1])) {
            if (is_array($value)) {
                // JSON value (associative array, not object).
                $candidate = '['.json_encode($value).']';
            } elseif (is_string($value)) {
                // String value.
                $candidate = '"' . $value . '"';
            } else {
                // Other scalar values (e.g. number, boolean)
                $candidate = $value;
            }
        } else {
            // We create a candidate object give the path to one of its properties.
            $candidate =  '['.$this->jsonObjectCandidate($parts[1], $value).']';
        }

        // Search scalar value in an array at the first level of the column.
        if (empty($parts[1]) && !is_array($value) && count(explode('->', $parts[0])) == 1) {
            $jsonContains = $parts[0] . " @> '".$candidate."'";
            $builder->$where($jsonContains);
            return;
        }

        $path = $this->jsonPath($parts[0]);
        $jsonContains = "(".$path.")::jsonb @> '".$candidate."'::jsonb";
        $builder->$where($jsonContains);
    }

    /**
     * Add a JSON search condition to the query builder.
     *
     * In an array of objects: 'meta->children[*]->name' => 'child'
     * > where jsonb_path_exists(meta, '$.children[*].name ? @ like_regex "child"')
     *
     * @param  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder  $builder
     * @param  string  $prop
     * @param  mixed  $value
     * @param  bool  $orWhere
     * @return void
     */
    public function addJsonSearchCondition($builder, string $prop, $value, bool $orWhere)
    {
        $where = $orWhere ? 'orWhereRaw' : 'whereRaw';
        list($target, $path) = $this->jsonTargetAndPath($prop);
        $jsonSearch = "jsonb_path_exists($target::jsonb, '$path ? (@ like_regex \"$value\")')";
        $builder->$where($jsonSearch);
    }

    /**
     * Get the JSON target and path to be used with JSON_CONTAINS.
     *
     * E.g. meta->topic->tags  >  meta #> '{topic,tags}'
     *
     * @param  string  $property
     * @return array
     */
    protected function jsonPath(string $property)
    {
        $names = explode('->', $property);
        $target = array_shift($names);
        $path = implode(',', $names);
        return "$target #> '{".$path."}'";
    }
}
