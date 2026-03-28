<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Bound value for case-insensitive LIKE clauses: use as LOWER(column) LIKE ?
     */
    protected function caseInsensitiveLike(string $term): string
    {
        return '%'.mb_strtolower($term, 'UTF-8').'%';
    }
}
