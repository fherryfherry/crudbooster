<?php

namespace CrudBooster\Helpers;

use Illuminate\Support\Facades\Schema;

class SchemaUtil
{
    public function getTableListing()
    {
        // schemaQualified: false so table names come back plain (e.g. "kategori_artikel"),
        // not schema-prefixed (e.g. SQLite's "main.kategori_artikel") — a prefixed name
        // breaks anything that uses it as a literal table reference, like a JOIN.
        return Schema::getTableListing(schemaQualified: false);
    }
}
