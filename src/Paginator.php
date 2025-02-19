<?php

namespace Ucscode\Paginator;

use Ucscode\Paginator\Pagination\PaginationBuilder;
use Ucscode\Paginator\Pagination\PaginationResult;

class Paginator
{
    public const NUM_PLACEHOLDER = '(:num)';

    /**
     * The total number of items to be paginated
     *
     * @var integer
     */
    protected int $totalItems = 0;

    /**
     * The number of items that should be render per page
     *
     * @var integer
     */
    protected int $itemsPerPage = 10;

    /**
     * The current page number
     *
     * @var integer
     */
    protected int $currentPage = 1;

    /**
     * The total number of pages required for pagination
     *
     * @var integer
     */
    protected int $totalPages;

    /**
     * URL pattern for generating pagination links
     *
     * @var string
     */
    protected string $urlPattern;

    public function __construct(int $totalItems = 0, int $itemsPerPage = 10, int $currentPage = 1, string $urlPattern = '?page=' . self::NUM_PLACEHOLDER)
    {
        $this->totalItems = $totalItems;
        $this->itemsPerPage = $itemsPerPage;
        $this->currentPage = $currentPage;
        $this->urlPattern = $urlPattern;

        $this->updatePaginator();
    }

    public function setTotalItems(int $totalItems): static
    {
        $this->totalItems = $totalItems;
        $this->updatePaginator();

        return $this;
    }

    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    public function setItemsPerPage(int $itemsPerPage): static
    {
        $this->itemsPerPage = $itemsPerPage;
        $this->updatePaginator();

        return $this;
    }

    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    public function setCurrentPage(int $currentPage): static
    {
        $this->currentPage = $currentPage;

        return $this;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    public function getNextPage(): ?int
    {
        if ($this->currentPage < $this->totalPages) {
            return $this->currentPage + 1;
        }

        return null;
    }

    public function getPrevPage(): ?int
    {
        if ($this->currentPage > 1) {
            return $this->currentPage - 1;
        }

        return null;
    }

    public function setUrlPattern(string $urlPattern): static
    {
        $this->urlPattern = $urlPattern;

        return $this;
    }

    public function getUrlPattern(): string
    {
        return $this->urlPattern;
    }

    public function getPageUrl(int $pageNum): string
    {
        return str_replace(self::NUM_PLACEHOLDER, $pageNum, $this->urlPattern);
    }

    public function getNextUrl(): ?string
    {
        if (!$this->getNextPage()) {
            return null;
        }

        return $this->getPageUrl($this->getNextPage());
    }

    public function getPrevUrl(): ?string
    {
        if (!$this->getPrevPage()) {
            return null;
        }

        return $this->getPageUrl($this->getPrevPage());
    }

    public function getCurrentPageFirstItem(): ?int
    {
        $first = ($this->getCurrentPage() - 1) * $this->getItemsPerPage() + 1;

        return $first > $this->getTotalItems() ? null : $first;
    }

    public function getCurrentPageLastItem(): ?int
    {
        $first = $this->getCurrentPageFirstItem();

        if ($first === null) {
            return null;
        }

        $last = $first + $this->getItemsPerPage() - 1;

        return $last > $this->getTotalItems() ? $this->getTotalItems() : $last;
    }

    public function getResult(int $maxPagesToShow = 10): PaginationResult
    {
        return new PaginationResult($this, $maxPagesToShow);
    }

    public function getBuilder(int $maxPagesToShow = 10): PaginationBuilder
    {
        return new PaginationBuilder($this->getResult($maxPagesToShow));
    }

    protected function updatePaginator()
    {
        $this->totalPages = ($this->itemsPerPage == 0 ? 0 : (int) ceil($this->totalItems / $this->itemsPerPage));
    }
}
