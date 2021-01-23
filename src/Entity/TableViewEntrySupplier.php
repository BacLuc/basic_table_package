<?php

namespace BaclucC5Crud\Entity;

use BaclucC5Crud\Controller\PaginationConfiguration;

interface TableViewEntrySupplier {
    public function getEntries(PaginationConfiguration $paginationConfiguration);

    public function count();
}
