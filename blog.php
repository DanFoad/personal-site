<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

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

    require_once __DIR__ . "/includes/config.php";

    try {
        
        // Sidebar
        $templateSidebar = file_get_contents(__DIR__ . '/template/blog-sidebar.html');

        // Categories
        $sql = 'SELECT DISTINCT(postCategory) AS postCategory
        FROM blog_posts
        ORDER BY postCategory DESC';
        $sth = $db->prepare($sql);
        $sth->execute();
        $categories = $sth->fetchAll();

        $replace = '{{categories}}';
        $with = "";
        foreach ($categories as $category) {
            $with = $with . '<li><a href="/blog/category/' . urlify($category['postCategory']) . '">' . $category['postCategory'] . '</a></li>';
        }
        $templateSidebar = str_replace($replace, $with, $templateSidebar);
        
        // Recent blogs
        $sql = 'SELECT *
        FROM blog_posts
        ORDER BY postDate DESC
        LIMIT 2';
        $sth = $db->prepare($sql);
        $sth->execute();
        $recentPosts = $sth->fetchAll();

        $replace = '{{recent}}';
        $with = "";
        foreach ($recentPosts as $recentPost) {
            $with .= '<div class="blog__small">';
            $with .= '    <h3><a href="/blog/' . $recentPost['postURI'] . '/">' . $recentPost['postTitle'] . '</a></h3>';
            $with .= '    <p class="blog__date"><i class="fa fa-calendar"></i>' . date('jS M Y', strtotime($recentPost['postDate'])) . '</p>';
            $with .= '</div>';
        }
        $templateSidebar = str_replace($replace, $with, $templateSidebar);
        
        // Archives
        $sql = 'SELECT DISTINCT(postDate) as postDate
        FROM blog_posts
        ORDER BY postDate DESC';
        $sth = $db->prepare($sql);
        $sth->execute();
        $dates = $sth->fetchAll();
        
        $months = "";
        foreach ($dates as $date) {
            $month = date('F Y', strtotime($date['postDate']));
            if (!strpos($months, $month)) {
                $months .= '<li><a href="/blog/archives/' . urlify($month) . '">' . $month . '</a></li>';
            }
        }
        $replace = '{{archives}}';
        $with = $months;
        $templateSidebar = str_replace($replace, $with, $templateSidebar);
        
        //Tags
        $sql = 'SELECT postTags
        FROM blog_posts';
        $sth = $db->prepare($sql);
        $sth->execute();
        $rawtags = $sth->fetchAll();
        
        $tags = "";
        foreach ($rawtags as $rawtag) {
            $tags_split = explode(',', $rawtag['postTags']);
            foreach ($tags_split as $tag) {
                if (!strpos($tags, $tag)) {
                    $tags .= '<a href="/blog/tagged/' . urlify($tag) . '/">' . $tag . '</a>';
                }
            }
        }
        $replace = '{{tags}}';
        $with = $tags;
        $templateSidebar = str_replace($replace, $with, $templateSidebar);
        
        // Individual Blog Pages
        if (isset($blogURI)) {
            // Current blog
            $sql = 'SELECT *
            FROM blog_posts
            WHERE postURI = :bloguri
            LIMIT 1';
            $sth = $db->prepare($sql);
            $sth->execute(array(':bloguri' => $blogURI));
            $post = $sth->fetchAll()[0];
            
            // Next and previous blogs
            $sql = 'SELECT *
            FROM blog_posts
            WHERE postID = :previousID
            LIMIT 1';
            $sth = $db->prepare($sql);
            $sth->execute(array(':previousID' => $post['postID'] - 1));
            $post_previous = $post;
            $hide_previous = "";
            $row = $sth->fetch();
            if (!empty($row)) {
                $post_previous = $row;
            } else {
                $hide_previous = "blog__post--hidden";
            }   
            
            $sql = 'SELECT *
            FROM blog_posts
            WHERE postID = :nextID
            LIMIT 1';
            $sth = $db->prepare($sql);
            $sth->execute(array(':nextID' => $post['postID'] + 1));
            $post_next = $post;
            $hide_next = "";
            $row = $sth->fetch();
            if (!empty($row)) {
                $post_next = $row;
            } else {
                $hide_next = "blog__post--hidden";
            }
            
            // Main template
            $replace = array('{{title}}', '{{content}}', '{{date}}', '{{category}}', '{{tags}}', '{{id}}', '{{title_previous}}', '{{date_previous}}', '{{uri_previous}}', '{{title_next}}', '{{date_next}}', '{{uri_next}}', '{{hide_previous}}', '{{hide_next}}', '{{sidebar}}');
            
            $tags = '';
            foreach (explode(',', $post['postTags']) as $tag) {
                $tags .= '<a href="/blog/tagged/' . urlify($tag) . '/">' . $tag . '</a>';
            }
            
            $with = array($post['postTitle'], $post['postContent'], date('jS M Y', strtotime($post['postDate'])), $post['postCategory'], $tags, $post['postID'], $post_previous['postTitle'], date('jS M Y', strtotime($post_previous['postDate'])), $post_previous['postURI'], $post_next['postTitle'], date('jS M Y', strtotime($post_next['postDate'])), $post_next['postURI'], $hide_previous, $hide_next, $templateSidebar);
            
            $template = file_get_contents(__DIR__ . '/template/blogpost.html');
            
            echo str_replace($replace, $with, $template);
            
        // Category blogs page
        } else if (isset($blogCategory)) {
            
            $category = str_replace('-', ' ', $blogCategory);
            
            $sql = 'SELECT *
            FROM blog_posts
            WHERE postCategory = :category
            ORDER BY postDate DESC
            LIMIT 10';
            $sth = $db->prepare($sql);
            $sth->execute(array(':category' => $category));
            $posts = $sth->fetchAll();
            
            $template = file_get_contents(__DIR__ . '/template/blog.html');
            
            $templatePosts = '<div class="blog__footer--links"><a class="blog__readmore" href="/blog/">All Posts</a></div>';
            
            $replace = array('{{title}}', '{{content}}', '{{date}}', '{{id}}', '{{uri}}', '{{tags}}', '{{category}}');
            foreach ($posts as $post) {
                $tags = '';
                foreach (explode(',', $post['postTags']) as $tag) {
                    $tags .= '<a href="/blog/tagged/' . urlify($tag) . '/">' . $tag . '</a>';
                }
                $with = array($post['postTitle'], substrwords($post['postContent'], 255), date('jS M Y', strtotime($post['postDate'])), $post['postID'], $post['postURI'], $tags, $post['postCategory']);

                $templateFragment = file_get_contents(__DIR__ . '/template/blog-fragment.html');

                $templatePosts .= str_replace($replace, $with, $templateFragment);
            }
            
            echo str_replace(array('{{posts}}', '{{sidebar}}', '{{title_category}}'), array($templatePosts, $templateSidebar, ' - ' . ucwords($category)), $template);
            
        // Archives blog pages
        } else if (isset($blogDate)) {
            
            $format = "d F-Y H:i:s";
            $dateStart = DateTime::createFromFormat($format, '1 ' . $blogDate . ' 00:00:00');
            $dateEnd = DateTime::createFromFormat($format, '1 ' . $blogDate . ' 00:00:00');
            
            $dateEnd->modify('first day of next month');
            $dateEnd->modify('-1 second');
            
            
            $sql = 'SELECT * FROM blog_posts WHERE postDate between :dateStart and :dateEnd ORDER BY postDate DESC LIMIT 10';
            $sth = $db->prepare($sql);
            $sth->execute(array(':dateStart' => $dateStart->format('Y-m-d H:i:s'), ':dateEnd' => $dateEnd->format('Y-m-d H:i:s')));
            $posts = $sth->fetchAll();
            
            $template = file_get_contents(__DIR__ . '/template/blog.html');
            
            $templatePosts = '<h3 class="blog__description">Posts from ' . ucwords(explode('-', $blogDate)[0]) . ' ' . explode('-', $blogDate)[1] . '</h3><div class="blog__footer--links"><a class="blog__readmore" href="/blog/">All Posts</a></div>';
            
            $replace = array('{{title}}', '{{content}}', '{{date}}', '{{id}}', '{{uri}}', '{{tags}}', '{{category}}');
            foreach ($posts as $post) {
                $tags = '';
                foreach (explode(',', $post['postTags']) as $tag) {
                    $tags .= '<a href="/blog/tagged/' . urlify($tag) . '/">' . $tag . '</a>';
                }
                $with = array($post['postTitle'], substrwords($post['postContent'], 255), date('jS M Y', strtotime($post['postDate'])), $post['postID'], $post['postURI'], $tags, $post['postCategory']);

                $templateFragment = file_get_contents(__DIR__ . '/template/blog-fragment.html');

                $templatePosts .= str_replace($replace, $with, $templateFragment);
            }
            
            echo str_replace(array('{{posts}}', '{{sidebar}}', '{{title_category}}'), array($templatePosts, $templateSidebar, ''), $template);
        
        // Tagged blog pages
        } else if (isset($blogTagged)) {
            
            $blogTagged = str_replace('-', ' ', $blogTagged);
            
            $sql = 'SELECT *
            FROM blog_posts
            WHERE postTags LIKE concat("%", :tag, "%")
            ORDER BY postDate DESC
            LIMIT 10';
            $sth = $db->prepare($sql);
            $sth->execute(array(':tag' => $blogTagged));
            $posts = $sth->fetchAll();
            
            $template = file_get_contents(__DIR__ . '/template/blog.html');
            
            $templatePosts = '<h3 class="blog__description">Posts tagged: <div class="blog__tags"><a href="/blog/tagged/' . $blogTagged . '/">' . $blogTagged . '</a></div></h3><div class="blog__footer--links"><a class="blog__readmore" href="/blog/">All Posts</a></div>';
            
            $replace = array('{{title}}', '{{content}}', '{{date}}', '{{id}}', '{{uri}}', '{{tags}}', '{{category}}');
            foreach ($posts as $post) {
                $tags = '';
                foreach (explode(',', $post['postTags']) as $tag) {
                    $tags .= '<a href="/blog/tagged/' . urlify($tag) . '/">' . $tag . '</a>';
                }
                $with = array($post['postTitle'], substrwords($post['postContent'], 255), date('jS M Y', strtotime($post['postDate'])), $post['postID'], $post['postURI'], $tags, $post['postCategory']);

                $templateFragment = file_get_contents(__DIR__ . '/template/blog-fragment.html');

                $templatePosts .= str_replace($replace, $with, $templateFragment);
            }
            
            echo str_replace(array('{{posts}}', '{{sidebar}}', '{{title_category}}'), array($templatePosts, $templateSidebar, ''), $template);
            
        // Search blog pages
        } else if (isset($blogSearch)) {
            
            $blogSearch = str_replace('%20', ' ', $blogSearch);
            $blogSearch = preg_replace("/[^A-Za-z0-9 ]/", '', $blogSearch);
            
            $sql = 'SELECT *
            FROM blog_posts
            WHERE postTitle LIKE concat("%", :search, "%")
            ORDER BY postDate DESC
            LIMIT 10';
            $sth = $db->prepare($sql);
            $sth->execute(array(':search' => $blogSearch));
            $posts = $sth->fetchAll();
            
            $template = file_get_contents(__DIR__ . '/template/blog.html');
            
            $templatePosts = '<h3 class="blog__description">Posts containing: \'' . $blogSearch . '\'</h3><div class="blog__footer--links"><a class="blog__readmore" href="/blog/">All Posts</a></div>';
            
            $replace = array('{{title}}', '{{content}}', '{{date}}', '{{id}}', '{{uri}}', '{{tags}}', '{{category}}');
            foreach ($posts as $post) {
                $tags = '';
                foreach (explode(',', $post['postTags']) as $tag) {
                    $tags .= '<a href="/blog/tagged/' . urlify($tag) . '/">' . $tag . '</a>';
                }
                $with = array($post['postTitle'], substrwords($post['postContent'], 255), date('jS M Y', strtotime($post['postDate'])), $post['postID'], $post['postURI'], $tags, $post['postCategory']);

                $templateFragment = file_get_contents(__DIR__ . '/template/blog-fragment.html');

                $templatePosts .= str_replace($replace, $with, $templateFragment);
            }
            
            echo str_replace(array('{{posts}}', '{{sidebar}}', '{{title_category}}'), array($templatePosts, $templateSidebar, ''), $template);
            
        // All Blogs Page
        } else {
            $sql = 'SELECT *
            FROM blog_posts
            ORDER BY postDate DESC
            LIMIT 10';
            $sth = $db->prepare($sql);
            $sth->execute();
            $posts = $sth->fetchAll();
            
            $template = file_get_contents(__DIR__ . '/template/blog.html');
            
            $templatePosts = "";
            
            $replace = array('{{title}}', '{{content}}', '{{date}}', '{{id}}', '{{uri}}', '{{tags}}', '{{category}}');
            foreach ($posts as $post) {
                $tags = '';
                foreach (explode(',', $post['postTags']) as $tag) {
                    $tags .= '<a href="/blog/tagged/' . urlify($tag) . '/">' . $tag . '</a>';
                }
                $with = array($post['postTitle'], substrwords($post['postContent'], 255), date('jS M Y', strtotime($post['postDate'])), $post['postID'], $post['postURI'], $tags, $post['postCategory']);

                $templateFragment = file_get_contents(__DIR__ . '/template/blog-fragment.html');

                $templatePosts .= str_replace($replace, $with, $templateFragment);
            }
            
            echo str_replace(array('{{posts}}', '{{sidebar}}', '{{title_category}}'), array($templatePosts, $templateSidebar, ''), $template);
        }

    } catch(PDOException $e) {
        echo $e->getMessage();
    }
?>