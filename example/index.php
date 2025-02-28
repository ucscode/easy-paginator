<?php

use Ucscode\Paginator\Paginator;

require_once '../vendor/autoload.php';

$paginator = new Paginator(20, 3, $_GET['page'] ?? 1);

?>
<!doctype html>
<html>
    <head>
        <title>Sample</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <?php echo $paginator->getBuilder(5)->render(1); ?>
    </body>
</html>