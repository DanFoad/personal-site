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

    require_once __DIR__ . "/includes/config.php";

    try {
        
        if (isset($blogURI)) {
            $sql = 'SELECT *
            FROM blog_posts
            WHERE postURI = :bloguri
            LIMIT 1';
            $sth = $db->prepare($sql);
            $sth->execute(array(':bloguri' => $blogURI));
            $post = $sth->fetchAll()[0];
            
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
            
            $replace = array('{{title}}', '{{content}}', '{{date}}', '{{id}}', '{{title_previous}}', '{{date_previous}}', '{{uri_previous}}', '{{title_next}}', '{{date_next}}', '{{uri_next}}', '{{hide_previous}}', '{{hide_next}}');
            $with = array($post['postTitle'], $post['postContent'], date('jS M Y H:i:s', strtotime($post['postDate'])), $post['postID'], $post_previous['postTitle'], date('jS M Y H:i:s', strtotime($post_previous['postDate'])), $post_previous['postURI'], $post_next['postTitle'], date('jS M Y H:i:s', strtotime($post_next['postDate'])), $post_next['postURI'], $hide_previous, $hide_next);
            
            $template = file_get_contents(__DIR__ . '/template/blogpost.html');
            
            echo str_replace($replace, $with, $template);
            
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
            
            $replace = array('{{title}}', '{{content}}', '{{date}}', '{{id}}', '{{uri}}');
            foreach ($posts as $post) {
                $with = array($post['postTitle'], substrwords($post['postContent'], 255), date('jS M Y H:i:s', strtotime($post['postDate'])), $post['postID'], $post['postURI']);

                $templateFragment = file_get_contents(__DIR__ . '/template/blog-fragment.html');

                $templatePosts .= str_replace($replace, $with, $templateFragment);
            }
            
            echo str_replace(array("{{posts}}"), array($templatePosts), $template);
        }

    } catch(PDOException $e) {
        echo $e->getMessage();
    }
?>