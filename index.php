<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

function substrwords($text, $maxchar, $end='...') {
    if (strlen($text) > $maxchar || $text == '') {
        $words = preg_split('/\s/', $text);      
        $output = '';
        $i      = 0;
        while (1) {
            $length = strlen($output)+strlen($words[$i]);
            if ($length > $maxchar) {
                break;
            } 
            else {
                $output .= " " . $words[$i];
                ++$i;
            }
        }
        $output .= $end;
    } 
    else {
        $output = $text;
    }
    return $output;
}

function urlify($text) {
    $text = strtolower($text);
    $text = preg_replace("/[^A-Za-z0-9 ]/", '', $text);
    $text = preg_replace("/[ ]/", '-', $text);
    return $text;
}

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

if (!isset($routes[0])) {
    require_once __DIR__ . "/includes/config.php";
    
    try {
        $indexpage = file_get_contents(__DIR__ . '/index.html');
        
        // Recent blogs
        $sql = 'SELECT *
        FROM blog_posts
        ORDER BY postDate DESC
        LIMIT 2';
        $sth = $db->prepare($sql);
        $sth->execute();
        $recentPosts = $sth->fetchAll();
        
        $replace = array('{{title_recent1}}', '{{date_recent1}}', '{{categoryURI_recent1}}', '{{category_recent1}}', '{{tags_recent1}}', '{{uri_recent1}}', '{{title_recent2}}', '{{date_recent2}}', '{{categoryURI_recent2}}', '{{category_recent2}}', '{{tags_recent2}}', '{{uri_recent2}}');
        $with = array();
        
        foreach ($recentPosts as $recentPost) {
            array_push($with, $recentPost['postTitle']);
            array_push($with, date('jS M Y', strtotime($recentPost['postDate'])));
            array_push($with, urlify($recentPost['postCategory']));
            array_push($with, $recentPost['postCategory']);
            $tags = '';
            foreach (explode(',', $recentPost['postTags']) as $tag) {
                $tag = trim($tag);
                $tags .= '<a href="/blog/tagged/' . urlify($tag) . '/">' . $tag . '</a>';
            }
            array_push($with, $tags);
            array_push($with, $recentPost['postURI']);
        }
        $indexpage = str_replace($replace, $with, $indexpage);
        echo $indexpage;
    } catch(PDOException $e) {
        echo $e->getMessage();
    }
}
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
    
    if ($routes[0] == 'post') {
        require_once __DIR__ . '/template/header.php';
        include_once __DIR__ . '/post.php';
        require_once __DIR__ . '/template/footer.php';
    }
    
}

?>