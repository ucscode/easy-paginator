<?php

namespace Ucscode\Paginator\Pagination;

use Ucscode\Paginator\Paginator;
use Ucscode\UssElement\Contracts\ElementInterface;
use Ucscode\UssElement\Contracts\NodeInterface;
use Ucscode\UssElement\Enums\NodeNameEnum;
use Ucscode\UssElement\Node\ElementNode;
use Ucscode\UssElement\Node\TextNode;

class BuilderFactory
{
    /**
     * @var Item[] $items
     */
    protected array $items = [];
    protected NodeInterface|string $prevText = '&laquo;';
    protected NodeInterface|string $nextText = '&raquo';

    public function __construct(protected Paginator $paginator, protected int $maxVisibleItem)
    {
        $this->paginator = $paginator;

        if ($paginator->getTotalPages() > 1) {
            $paginator->getTotalPages() > $this->maxVisibleItem ?
                $this->createNormalizedItemStack() :
                $this->createItemStack()
            ;
        }

        return $this;
    }

    /**
     * @return Item[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function createElement(): ElementInterface
    {
        $nav = new ElementNode(NodeNameEnum::NODE_NAV, ['aria-label' => 'page navigation example']);
        $ul = new ElementNode(NodeNameEnum::NODE_UL, ['class' => 'pagination']);

        if ($this->paginator->getPrevPage()) {
            $item = $this->itemFactory($this->paginator->getPrevPage())
                ->setContent($this->prevText instanceof NodeInterface ? $this->prevText : new TextNode($this->prevText))
            ;
            $ul->appendChild($item->createElement());
        }

        foreach ($this->items as $item) {
            $ul->appendChild($item->createElement());
        }

        if ($this->paginator->getNextPage()) {
            $item = $this->itemFactory($this->paginator->getNextPage())
                ->setContent($this->nextText instanceof NodeInterface ? $this->nextText : new TextNode($this->nextText))
            ;
            $ul->appendChild($item->createElement());
        }

        $nav->appendChild($ul);

        return $nav;
    }

    public function setMaxVisibleItems(int $maxVisibleItem): static
    {
        $this->maxVisibleItem = $maxVisibleItem;

        return $this;
    }
    
    protected function createItemStack(): void
    {
        for ($pageNumber = 1; $pageNumber <= $this->paginator->getTotalPages(); $pageNumber++) {
            $this->items[] = $this->itemFactory(
                $pageNumber,
                $pageNumber === $this->paginator->getCurrentPage()
            );
        }
    }

    protected function createNormalizedItemStack(): void
    {
        $numAdjacents = (int) \floor(($this->maxVisibleItem - 3) / 2);

        // Ensure slidingStart does not exclude 2
        $slidingStart = max(2, $this->paginator->getCurrentPage() - $numAdjacents);

        // Ensure slidingEnd does not exclude second-to-last page
        $slidingEnd = min($this->paginator->getTotalPages() - 1, $slidingStart + $this->maxVisibleItem - 3);

        // Adjust slidingStart if slidingEnd is at max
        $slidingStart = max(2, $slidingEnd - ($this->maxVisibleItem - 3));

        // Build the list of items
        $this->items[] = $this->itemFactory(1, $this->paginator->getCurrentPage() === 1);

        // Show "..." if gap exists after 1
        if ($slidingStart > 2) {
            $this->items[] = $this->itemFactory();
        }

        for ($i = $slidingStart; $i <= $slidingEnd; $i++) {
            $this->items[] = $this->itemFactory($i, $i == $this->paginator->getCurrentPage());
        }

        // Show "..." if there's a gap before the last page
        if ($slidingEnd < $this->paginator->getTotalPages() - 1) {
            $this->items[] = $this->itemFactory();
        }

        $this->items[] = $this->itemFactory(
            $this->paginator->getTotalPages(),
            $this->paginator->getCurrentPage() == $this->paginator->getTotalPages()
        );
    }


    protected function itemFactory(?int $pageNumber = null, bool $active = false): Item
    {
        if ($pageNumber === null) {
            return (new Item())->setContent('...');
        }

        return (new Item())
            ->setPageNumber($pageNumber)
            ->setUrl($this->paginator->getPageUrl($pageNumber))
            ->setActive($active)
            ->setContent($pageNumber)
        ;
    }
}
