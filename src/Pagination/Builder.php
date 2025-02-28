<?php

namespace Ucscode\Paginator\Pagination;

use Ucscode\Paginator\Paginator;
use Ucscode\UssElement\Contracts\ElementInterface;
use Ucscode\UssElement\Enums\NodeNameEnum;
use Ucscode\UssElement\Node\ElementNode;
use Ucscode\UssElement\Node\TextNode;

class Builder implements \Stringable
{
    /**
     * @var Item[] $items
     */
    protected array $items = [];

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

    public function __toString(): string
    {
        return $this->render();
    }

    public function render(?int $indent = null): string
    {
        return $this->createElement()->render($indent);
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
        $nav = new ElementNode(NodeNameEnum::NODE_NAV, [
            'class' => 'navigation',
            'aria-label' => 'page navigation'
        ]);

        $ul = new ElementNode(NodeNameEnum::NODE_UL, ['class' => 'pagination']);

        if ($item = $this->getPrevItem()) {
            $ul->appendChild($item->createElement());
        }

        foreach ($this->items as $item) {
            $ul->appendChild($item->createElement());
        }

        if ($item = $this->getNextItem()) {
            $ul->appendChild($item->createElement());
        }

        $nav->appendChild($ul);

        return $nav;
    }

    public function getPrevItem(): ?Item
    {
        if ($this->paginator->getPrevPage()) {
            return $this->itemFactory($this->paginator->getPrevPage())
                ->setContent(new TextNode('&laquo;'))
            ;
        }

        return null;
    }

    public function getNextItem(): ?Item
    {
        if ($this->paginator->getNextPage()) {
            return $this->itemFactory($this->paginator->getNextPage())
                ->setContent(new TextNode('&raquo'))
            ;
        }

        return null;
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
