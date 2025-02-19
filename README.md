# Easy Paginator

## Introduction

Ucscode Paginator is a lightweight PHP pagination library designed to handle paginated data efficiently. It provides an easy-to-use interface for managing and generating pagination links.

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
    urlPattern: 'http://domain.com/path/?page=(:num)' // URL pattern with placeholder
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

## Pagination Result

The `PaginationResult` class generates an array of pagination data, which can be iterated to display page links.

### Example
```php
use Ucscode\Paginator\Pagination\PaginationResult;

$result = $paginator->getResult();
foreach ($result->getItems() as $page) {
    echo ($page['isCurrent'] ? '<strong>' : '') . "<a href='{$page['url']}'>{$page['pageNum']}</a>" . ($page['isCurrent'] ? '</strong>' : '');
}
```

### Pagination Structure
The `PaginationResult` generates an array with:
```php
[
    ['pageNum' => 1,     'url' => '/page/1',  'isCurrent' => false],
    ['pageNum' => '...', 'url' => null,       'isCurrent' => false],
    ['pageNum' => 3,     'url' => '/page/3',  'isCurrent' => false],
    ['pageNum' => 4,     'url' => '/page/4',  'isCurrent' => true ],
    ['pageNum' => 5,     'url' => '/page/5',  'isCurrent' => false],
    ['pageNum' => '...', 'url' => null,       'isCurrent' => false],
    ['pageNum' => 10,    'url' => '/page/10', 'isCurrent' => false],
]
```

## Pagination Builder

For structured HTML generation, use the `PaginationBuilder`. This class converts pagination data into an HTML structure with `ul` and `li` elements, making it easy to integrate into any UI.

### Example Usage
```php
use Ucscode\Paginator\Pagination\PaginationBuilder;

$builder = $paginator->getBuilder();
echo $builder->render();
```

### Customizing Navigation Text
You can customize the previous and next navigation text:
```php
$builder->setPreviousText('&laquo;'); // Set custom previous button
$builder->setNextText('&raquo;');     // Set custom next button
```

### Example Output
The `PaginationBuilder` generates the following HTML structure:
```html
<div class="navigation">
    <ul class="pagination">
        <li class="page-item"><a href="/page/1" class="page-link">1</a></li>
        <li class="page-item disabled"><span class="page-link">...</span></li>
        <li class="page-item active"><a href="/page/4" class="page-link">4</a></li>
        <li class="page-item"><a href="/page/5" class="page-link">5</a></li>
        <li class="page-item disabled"><span class="page-link">...</span></li>
        <li class="page-item"><a href="/page/10" class="page-link">10</a></li>
    </ul>
</div>
```
