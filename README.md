# Easy Paginator

Ucscode Paginator is a lightweight PHP pagination library designed to handle paginated data efficiently. It provides an easy-to-use interface for managing and generating pagination links.

This project requires a minimum of PHP 8.2

## Installation

Install the package via Composer:
```sh
composer require ucscode/easy-paginator
```

## Usage

### Creating a Paginator
```php
use Ucscode\Paginator\Paginator;

$paginator = new Paginator(
    totalItems: 100,       // Total number of items
    itemsPerPage: 10,      // Items per page
    currentPage: 3,        // Current page
    urlPattern: '?page=(:num)' // URL pattern with placeholder
);
```

### Getting Pagination Information
```php
$paginator->getTotalPages();   // Total number of pages
$paginator->getCurrentPage();  // Current page number
$paginator->getNextPage();     // Next page number or null if last page
$paginator->getPrevPage();     // Previous page number or null if first page
```

### Generating URLs
```php
$paginator->getPageUrl(5); // Returns '?page=5'
$paginator->getNextUrl();  // URL for the next page
$paginator->getPrevUrl();  // URL for the previous page
```

## Pagination Builder

The `Builder` class generates an array of `Item`, which can be iterated to display page links.

### Example

```php
$builder = $paginator->getBuilder();

$builder->getPrevItem(); // Item|null

foreach ($builder->getItems() as $item) {
    echo <<<HTML
        <li class="{$item->isActive() ? 'active' : ''}">
            <a href="{$item->getUrl()}" class="">
                {$item->getContent()}
            </a>
        </li>
    HTML;
}

$builder->getNextItem(); // Item|null
```

### Pagination Items

The `Builder::getItems()` generates an array of `Item`:
```php
[
    new Item(), 
    new Item(),
    new Item(), 
]
```

Where an `Item` contains getters and setters for the following properties:

```php
class Item {
    protected ?string $url = null;
    protected bool $active = false;
    protected int $pageNumber = 0;
    protected string|NodeInterface $content = '';
}
```

---

For structured HTML generation, use can still use the `Builder`. This class converts pagination data into an HTML structure with `ul` and `li` elements, making it easy to integrate into any UI.

### Example Usage

```php
$builder = $paginator->getBuilder();

echo $builder->render();
```

The will generate HTML similar to bootstrap 5 pagination:

```html
<nav class="navigation" aria-label="...">
    <ul class="pagination">
        <li class="page-item disabled">
            <span class="page-link">&laquo;</span>
        </li>
        <li class="page-item">
            <a class="page-link" href="?page=1">1</a>
        </li>
        <li class="page-item active" aria-current="page">
            <span class="page-link">2</span>
        </li>
        <li class="page-item">
            <a class="page-link" href="?page=3">3</a>
        </li>
        <li class="page-item">
            <a class="page-link" href="#">&raquo;</a>
        </li>
    </ul>
</nav>
```

### Updating the HTML Element

You can customize the HTML Element before rendering it by taking advantage of the [UssElement Library](https://github.com/ucscode/uss-element).

```php
$navElement = $builder->createElement();

$navElement->getClassList()->add('my-nav-class');
$navElement->setAttribute('data-name', 'pagination');

$ulElement = $navElement->querySelector('ul.pagination');

$ulElement->setAttribute('id', 'nav-id')
```

Checkout [UssELement](https://github.com/ucscode/uss-element) and read the official documentation for more details 

## Acknowledgement

Inspired by [jasongrimes/php-paginator](https://github.com/jasongrimes/php-paginator/tree/master)

## Licence

Easy Paginator is licensed under the [MIT License](./LICENSE).