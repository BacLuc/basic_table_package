<?php

namespace BaclucC5Crud\Controller;

class PaginationConfiguration {
    /**
     * @var int
     */
    private $currentPage;
    /**
     * @var int
     */
    private $pageSize;

    public function __construct(int $currentPage, $pageSize) {
        $this->currentPage = $currentPage;
        $this->pageSize = $pageSize;
    }

    /**
     * @return int
     */
    public function getCurrentPage() {
        return $this->currentPage;
    }

    /**
     * @return int
     */
    public function getPageSize() {
        return $this->pageSize;
    }

    public function getOffset() {
        if (null == $this->pageSize) {
            return 0;
        }

        return $this->currentPage * $this->pageSize;
    }
}
