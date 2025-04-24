
<?php
$request = $_SERVER['REQUEST_URI'];

switch ($request) {
    case '/':
        echo "Welcome to the home page!";
        break;
    case '/about':
        echo "This is the about page.";
        break;
    default:
        http_response_code(404);
        echo "404 - Page not found";
        break;
}
