<?php

namespace Ucscode\Paginator\Pagination;

use ArrayIterator;
use Traversable;
use Ucscode\Paginator\Paginator;

/**
* Get an array of paginated page data.
*
* ```
* [
*     array ('num' => 1,     'url' => '/example/page/1',  'isCurrent' => false),
*     array ('num' => '...', 'url' => NULL,               'isCurrent' => false),
*     array ('num' => 3,     'url' => '/example/page/3',  'isCurrent' => false),
*     array ('num' => 4,     'url' => '/example/page/4',  'isCurrent' => true ),
*     array ('num' => 5,     'url' => '/example/page/5',  'isCurrent' => false),
*     array ('num' => '...', 'url' => NULL,               'isCurrent' => false),
*     array ('num' => 10,    'url' => '/example/page/10', 'isCurrent' => false),
* ]
* ```
*/
class PaginationResult implements \IteratorAggregate
{
    protected array $items = [];

    public function __construct(protected Paginator $paginator, protected int $maxPagesToShow)
    {
        if ($paginator->getTotalPages() > 1) {
            $paginator->getTotalPages() > $maxPagesToShow ?
                $this->createNormalizedPaginationStack() :
                $this->createPaginationStack()
            ;
        }
    }

    public function getPaginator(): Paginator
    {
        return $this->paginator;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    /**
     * @return array<int,array{pageNum:int,url:?string,isCurrent:bool}>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getCurrentPageFirstItemNumber(): ?int
    {
        $first = $this->paginator->getCurrentPageOffset() + 1;

        return $first > $this->paginator->getTotalItems() ? null : $first;
    }

    public function getCurrentPageLastItemNumber(): ?int
    {
        if (null === $first = $this->getCurrentPageFirstItemNumber()) {
            return null;
        }

        $last = $first + $this->paginator->getItemsPerPage() - 1;

        return $last > $this->paginator->getTotalItems() ? $this->paginator->getTotalItems() : $last;
    }


    protected function createPaginationStack(): void
    {
        for ($index = 1; $index <= $this->paginator->getTotalPages(); $index++) {
            $this->items[] = $this->createPage(
                $index,
                $index == $this->paginator->getCurrentPage()
            );
        }
    }

    protected function createNormalizedPaginationStack(): void
    {
        // Determine the sliding range, centered around the current page.
        $numAdjacents = (int) floor(($this->maxPagesToShow - 3) / 2);

        $slidingStart = $this->paginator->getCurrentPage() + $numAdjacents > $this->paginator->getTotalPages() ?
            $this->paginator->getTotalPages() - $this->maxPagesToShow + 2 :
            $this->paginator->getCurrentPage() - $numAdjacents
        ;

        if ($slidingStart < 2) {
            $slidingStart = 2;
        }

        $slidingEnd = $slidingStart + $this->maxPagesToShow - 3;

        if ($slidingEnd >= $this->paginator->getTotalPages()) {
            $slidingEnd = $this->paginator->getTotalPages() - 1;
        }

        // Build the list of pages.
        $this->items[] = $this->createPage(1, $this->paginator->getCurrentPage() == 1);

        if ($slidingStart > 2) {
            $this->items[] = $this->createPageEllipsis();
        }

        for ($i = $slidingStart; $i <= $slidingEnd; $i++) {
            $this->items[] = $this->createPage($i, $i == $this->paginator->getCurrentPage());
        }

        if ($slidingEnd < $this->paginator->getTotalPages() - 1) {
            $this->items[] = $this->createPageEllipsis();
        }

        $this->items[] = $this->createPage(
            $this->paginator->getTotalPages(),
            $this->paginator->getCurrentPage() == $this->paginator->getTotalPages()
        );
    }

    protected function createPage(int $pageNum, bool $isCurrent = false): array
    {
        return [
            'pageNum' => $pageNum,
            'url' => $this->paginator->getPageUrl($pageNum),
            'isCurrent' => $isCurrent,
        ];
    }

    protected function createPageEllipsis(): array
    {
        return array(
            'pageNum' => '...',
            'url' => null,
            'isCurrent' => false,
        );
    }
}
