<?php
namespace App\Foundation;


trait HasRelationDetector
{
    public static function getRelationDetector()
    {
        return RelationDetector::detect(new static());
    }
}