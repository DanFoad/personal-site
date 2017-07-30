<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

function getCurrentUri() {
    $basepath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
        $uri = substr($_SERVER['REQUEST_URI'], strlen($basepath));
        if (strstr($uri, '?')) $uri = substr($uri, 0, strpos($uri, '?'));
        $uri = '/' . trim($uri, '/');
        return $uri;
}

$base_url = getCurrentUri();
$routes = array();
$routesexplode = explode('/', $base_url);
foreach ($routesexplode as $route) {
    if (trim($route) != '') array_push($routes, $route);
}

if (!isset($routes[0])) require_once __DIR__ . '/index.html' ;
else {
    
    if ($routes[0] == 'freelance') {
        require_once __DIR__ . '/template/header.php';
        $project = htmlspecialchars($routes[1]);
        include_once __DIR__ . '/freelance/' . $project . '.html' ;
        require_once __DIR__ .'/template/footer.php';
    }
  
    if ($routes[0] == 'projects') {
        $project = htmlspecialchars($routes[1]);
        include_once __DIR__ . '/projects/' . $project . '/index.html' ;
    }
    
    if ($routes[0] == 'blog') {
        require_once __DIR__ . '/template/header.php';
        
        if (isset($routes[1])) {
            if ($routes[1] === 'category') {
                if (isset($routes[2])) {
                    $blogCategory = $routes[2];
                    include_once __DIR__ . '/blog.php' ;
                } else {
                    header('Location: /blog/', true, 302);
                }
            } else if ($routes[1] === 'archives') {
                if (isset($routes[2])) {
                    $blogDate = $routes[2];
                    include_once __DIR__ . '/blog.php' ;
                } else {
                    header('Location: /blog/', true, 302);
                }
            } else if ($routes[1] === 'tagged') {
                if (isset($routes[2])) {
                    $blogTagged = $routes[2];
                    include_once __DIR__ . '/blog.php';
                } else {
                    header('Location: /blog/', true, 302);
                }
            } else if ($routes[1] === 'search') {
                if (isset($routes[2])) {
                    $blogSearch = $routes[2];
                    include_once __DIR__ . '/blog.php' ;
                } else {
                    header('Location: /blog/', true, 302);
                }
            } else {
                $blogURI = $routes[1];
                include_once __DIR__ . '/blog.php' ;
            }
        } else {
            include_once __DIR__ . '/blog.php' ;
        }
        
        require_once __DIR__ .'/template/footer.php';
    }
    
}

?>