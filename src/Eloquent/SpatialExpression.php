<?php

namespace Grimzy\LaravelMysqlSpatial\Eloquent;

use Illuminate\Database\Query\Expression;
use Illuminate\Database\Grammar;

/**
 * @property-read object $value
 */
class SpatialExpression extends Expression
{
    public function getValue(Grammar $grammar = null)
    {
        return "ST_GeomFromText(?, ?)";
    }

    public function getSpatialValue()
    {
        return $this->value->toWkt();
    }

    public function getSrid()
    {
        return $this->value->getSrid();
    }
}
