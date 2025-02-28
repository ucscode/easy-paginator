<?php

namespace Ucscode\Paginator\Pagination;

use Ucscode\UssElement\Contracts\ElementInterface;
use Ucscode\UssElement\Contracts\NodeInterface;
use Ucscode\UssElement\Enums\NodeNameEnum;
use Ucscode\UssElement\Node\ElementNode;
use Ucscode\UssElement\Node\TextNode;

class Item
{
    protected ?string $url = null;
    protected bool $active = false;
    protected int $pageNumber = 0;
    protected string|NodeInterface $content = '';

    public function setUrl(?string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setPageNumber(int $pageNumber): static
    {
        $this->pageNumber = $pageNumber;

        return $this;
    }

    public function getPageNumber(): int
    {
        return $this->pageNumber;
    }

    public function setContent(null|string|NodeInterface $content): static
    {
        $this->content = $content ?? '';

        return $this;
    }

    public function getContent(): string|NodeInterface
    {
        return $this->content;
    }

    // This design is based on bootstrap 5.x pattern -> https://getbootstrap.com/docs/5.3/components/pagination/
    // For custom design, extend the Item class and override the `createElement()` method
    public function createElement(): ElementInterface
    {
        $item = new ElementNode(NodeNameEnum::NODE_LI, ['class' => 'page-item']);
        $link = new ElementNode($this->url ? NodeNameEnum::NODE_A : NodeNameEnum::NODE_SPAN, ['class' => 'page-link']);

        $this->url ? $link->setAttribute('href', $this->url) : $item->getClassList()->add('disabled');

        if ($this->active) {
            $item
                ->setAttribute('aria-current', 'page')
                ->getClassList()
                    ->add('active')
            ;
        }

        $content = is_string($this->content) ? new TextNode($this->content) : $this->content;

        $link->appendChild($content);
        $item->appendChild($link);

        return $item;
    }
}
