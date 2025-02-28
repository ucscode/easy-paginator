<?php

namespace Ucscode\Paginator\Pagination;

use Ucscode\UssElement\Contracts\ElementInterface;

class Builder implements \Stringable
{
    public function __construct(protected BuilderFactory $builderFactory)
    {

    }

    public function __toString(): string
    {
        return $this->render();
    }

    /** @return Item[] */
    public function getItems(): array
    {
        return $this->builderFactory->getItems();
    }

    public function createElement(): ElementInterface
    {
        return $this->builderFactory->createElement();
    }

    public function render(): string
    {
        return $this->createElement()->render();
    }
}
