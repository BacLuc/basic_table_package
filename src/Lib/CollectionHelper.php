<?php

namespace BasicTablePackage\Lib;

use Tightenco\Collect\Support\Collection;

class CollectionHelper
{
}

function collect ($value = null)
{
    return new Collection($value);
}