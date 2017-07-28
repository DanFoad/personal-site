<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

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
            
            $replace = array('{{title}}', '{{content}}', '{{date}}', '{{id}}');
            $with = array($post['postTitle'], $post['postContent'], date('jS M Y H:i:s', strtotime($post['postDate'])), $post['postID']);
            
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
            
            echo '<main role="main"><div class="container">';
            
            foreach ($posts as $post) {
                $replace = array('{{title}}', '{{content}}', '{{date}}', '{{id}}', '{{uri}}');
                $with = array($post['postTitle'], $post['postContent'], date('jS M Y H:i:s', strtotime($post['postDate'])), $post['postID'], $post['postURI']);

                $template = file_get_contents(__DIR__ . '/template/blog.html');

                echo str_replace($replace, $with, $template);
            }
            
            echo '</div></main>';
        }

    } catch(PDOException $e) {
        echo $e->getMessage();
    }
?>