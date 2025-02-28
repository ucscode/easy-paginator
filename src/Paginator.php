<?php

namespace Ucscode\Paginator;

use Ucscode\Paginator\Pagination\Builder;

class Paginator
{
    public const NUM_PLACEHOLDER = '(:num)';

    /**
     * The total number of items to be paginated
     * @var integer
     */
    protected int $totalItems = 0;

    /**
     * The number of items that should be render per page
     * @var integer
     */
    protected int $itemsPerPage = 10;

    /**
     * The current page number
     * @var integer
     */
    protected int $currentPage = 1;

    /**
     * URL pattern for generating pagination links
     * @var string
     */
    protected string $urlPattern;

    /**
     * The total number of pages available for pagination
     * @var integer
     */
    protected int $totalPages;

    public function __construct(?int $totalItems = null, int $itemsPerPage = 10, int $currentPage = 1, string $urlPattern = '?page=' . self::NUM_PLACEHOLDER)
    {
        $this->totalItems = $totalItems ?? 0;
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

    public function setUrlPattern(string $urlPattern): static
    {
        $this->urlPattern = $urlPattern;

        return $this;
    }

    public function getUrlPattern(): string
    {
        return $this->urlPattern;
    }

    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    public function getNextPage(): ?int
    {
        return ($this->currentPage < $this->totalPages) ? ($this->currentPage + 1) : null;
    }

    public function getPrevPage(): ?int
    {
        return ($this->currentPage > 1) ? ($this->currentPage - 1) : null;
    }

    public function getNextUrl(): ?string
    {
        return $this->getNextPage() ? $this->getPageUrl($this->getNextPage()) : null;
    }

    public function getPrevUrl(): ?string
    {
        return $this->getPrevPage() ? $this->getPageUrl($this->getPrevPage()) : null;
    }

    public function getPageUrl(int $pageNum): string
    {
        return \str_replace(self::NUM_PLACEHOLDER, $pageNum, $this->urlPattern);
    }

    /**
     * Get the offset from which the current page items start
     *
     * This offset is zero-based and can be used for array slicing or database queries with LIMIT and OFFSET.
     * Example: array_slice([...], $paginator->getCurrentPageOffset(), $paginator->getItemsPerPage())
     */
    public function getCurrentPageOffset(): int
    {
        return ($this->getCurrentPage() - 1) * $this->getItemsPerPage();
    }

    public function getCurrentPageFirstItemNumber(): ?int
    {
        $first = $this->getCurrentPageOffset() + 1;

        return $first > $this->getTotalItems() ? null : $first;
    }

    public function getCurrentPageLastItemNumber(): ?int
    {
        if (null === $first = $this->getCurrentPageFirstItemNumber()) {
            return null;
        }

        $last = $first + $this->getItemsPerPage() - 1;

        return $last > $this->getTotalItems() ? $this->getTotalItems() : $last;
    }

    public function getBuilder(int $maxVisibleItems = 10): Builder
    {
        return new Builder($this, $maxVisibleItems);
    }

    protected function updatePaginator()
    {
        $this->totalPages = $this->itemsPerPage === 0 ? 0 : (int) ceil($this->totalItems / $this->itemsPerPage);
    }
}
