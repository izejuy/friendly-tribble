<?php
$view['slots']->set('title', 'Gem Chess - A better playing chess website powered by Symfony.');
?>
<!DOCTYPE html>
<html lang="%">
    <?php $view->extend('/partials/head.html.php') ?>
    <body>
        <h1>Hello world!</h1>
        
        <?php $view->extend('/partials/script.html.php') ?>
    </body>
</html>
