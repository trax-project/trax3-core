<?php

namespace Trax\Framework\Repo\Eloquent;

class MySqlGrammar extends Grammar
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
        return $builder->$where($prop, 'like', '%' . $value . '%');
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
        // MySQL does not include NULL values in <> answers.
        // So we must use NOT <=>.
        $where = $orWhere ? 'orWhereNot' : 'whereNot';
        return $builder->$where($prop, '<=>', $value);
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
        // MySQL does not include NULL values in whereNotIn answers.
        // So we must use COALESCE($prop,'') not in (...)
        $qmarks = implode(',', array_map(function ($val) {
            return '?';
        }, $value));
        $where = $orWhere ? 'orWhere' : 'where';
        return $builder->{$where.'Raw'}("COALESCE($prop,'') not in ($qmarks)", [$value]);
    }

    /**
     * Add a JSON contains condition to the query builder.
     *
     * In an array of strings: 'meta->topic->tags[*]' => 'aicc'
     * > where JSON_CONTAINS(meta, '"aicc"', '$.topic.tags')
     *
     * In an array of objects: 'meta->children[*]->name' => 'child1'
     * > where JSON_CONTAINS(meta, '{"name" : "child1"}', '$.children')
     *
     * In an array of objects: 'meta->children[*]' => ['name' => 'child1', 'age' => 10]
     * > where JSON_CONTAINS(meta, '{"name" : "child1", "age" : 10}', '$.children')
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
                // JSON value (array or associative array, not object).
                $candidate = json_encode($value);
            } elseif (is_string($value)) {
                // String value.
                $candidate = '"' . $value . '"';
            } else {
                // Other scalar values (e.g. number, boolean)
                $candidate = $value;
            }
        } else {
            // We create a candidate object give the path to one of its properties.
            $candidate =  $this->jsonObjectCandidate($parts[1], $value);
        }

        // Sometimes the '/' char is escaped (e.g. MariaDB). Unescape it.
        $candidate = str_replace('/', '\\\/', $candidate);
        list($target, $path) = $this->jsonTargetAndPath($parts[0]);

        // Search scalar value in an array at the first level of the column.
        if (empty($parts[1]) && !is_array($value) && count(explode('->', $parts[0])) == 1) {
            $target = $parts[0] . "->'$[*]'";
            $builder->$where("$candidate MEMBER OF ($target)");
            return;
        }

        // Search a JSON object or search in a JSON object.
        $jsonContains = "JSON_CONTAINS(`$target`, '$candidate', '$path')";
        $builder->$where($jsonContains);
    }

    /**
     * Add a JSON search condition to the query builder.
     *
     * In an array of objects: 'meta->children[*]->name' => 'child'
     * > where JSON_SEARCH(meta, 'all', '%child%', NULL, '$.children[*].name') IS NOT NULL
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
        $jsonSearch = "JSON_SEARCH(`$target`, 'all', '%$value%', NULL, '$path') IS NOT NULL";
        $builder->$where($jsonSearch);
    }
}
