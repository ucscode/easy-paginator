<?php

namespace Ucscode\Paginator\Pagination;

use Ucscode\UssElement\Collection\Attributes;
use Ucscode\UssElement\Collection\ClassList;
use Ucscode\UssElement\Contracts\ElementInterface;
use Ucscode\UssElement\Contracts\NodeInterface;
use Ucscode\UssElement\Enums\NodeNameEnum;
use Ucscode\UssElement\Node\ElementNode;
use Ucscode\UssElement\Node\TextNode;

class PaginationBuilder implements \Stringable
{
    protected NodeInterface|string $previousText = '&lt;';
    protected NodeInterface|string $nextText = '&gt;';

    public function __construct(protected PaginationResult $paginationResult)
    {

    }

    public function __toString(): string
    {
        return $this->render();
    }

    protected function createElement(): ElementInterface
    {
        $paginator = $this->paginationResult->getPaginator();

        $divElement = new ElementNode(NodeNameEnum::NODE_DIV, [
            'class' => 'navigation',
        ]);

        $ulElement = $divElement->appendChild(
            new ElementNode(NodeNameEnum::NODE_UL, [
                'class' => 'pagination',
            ])
        );

        if ($paginator->getPrevUrl()) {
            $prevLiElement = $this->configurePagerElement(
                $paginator->getPrevUrl(),
                $this->getPreviousText(),
            );

            $ulElement->appendChild($prevLiElement);
        }

        foreach ($this->paginationResult->getItems() as $item) {
            $pagerElement = $item['url'] ?
                $this->configurePagerElement(
                    $item['url'],
                    $item['pageNum'],
                    $item['isCurrent'] ? 'active' : null
                ) :
                $this->configurePagerElement(
                    null,
                    $item['pageNum'],
                    'disabled',
                )
            ;

            $ulElement->appendChild($pagerElement);
        };

        if ($paginator->getNextUrl()) {
            $nextLiElement = $this->configurePagerElement(
                $paginator->getNextUrl(),
                $this->getNextText()
            );

            $ulElement->appendChild($nextLiElement);
        }

        $divElement->appendChild($ulElement);

        return $divElement;
    }

    public function render(?int $indent = null): string
    {
        return $this->createElement()->render($indent);
    }

    public function setPreviousText(string|NodeInterface $text): static
    {
        $this->previousText = $text;

        return $this;
    }

    public function getPreviousText(): string|NodeInterface
    {
        return $this->previousText;
    }

    public function setNextText(string|NodeInterface $text): static
    {
        $this->nextText = $text;

        return $this;
    }

    public function getNextText(): string|NodeInterface
    {
        return $this->nextText;
    }

    protected function configurePagerElement(?string $href, string|NodeInterface $text, ?string $classes = null): ElementInterface
    {
        $liElement = new ElementNode(NodeNameEnum::NODE_LI, [
            'class' => new ClassList(['page-item', $classes]),
        ]);

        $innerElement = new ElementNode(
            $href ? NodeNameEnum::NODE_A : NodeNameEnum::NODE_SPAN,
            new Attributes([
                'class' => new ClassList(['page-link']),
            ])
        );

        if (!$text instanceof NodeInterface) {
            $text = new TextNode($text);
        }

        $innerElement->appendChild($text);
        $liElement->appendChild($innerElement);

        return $liElement;
    }
}
