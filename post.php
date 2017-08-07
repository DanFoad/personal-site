<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once __DIR__ . "/includes/config.php";
    require_once __DIR__ . "/includes/postconfig.php";

    function urlify($text) {
        $text = strtolower($text);
        $text = preg_replace("/[^A-Za-z0-9 ]/", '', $text);
        $text = preg_replace("/[ ]/", '-', $text);
        return $text;
    }


    if (isset($_POST['user']) && isset($_POST['password'])) {
        if ($_POST['user'] == ADMINUSER && hash('sha256', $_POST['password']) == ADMINHASH) {
            $_SESSION['login'] = ADMINUSER;
            unset($_POST['user']);
            unset($_POST['password']);
        }
    }

    if (isset($_POST['title']) && isset($_POST['content'])) {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $category = $_POST['category'];
        $tags = $_POST['tags'];
        $date = new DateTime();
        $date = $date->format('Y-m-d H:i:s');
        $uri = urlify($title);
        
        $sql = 'INSERT INTO blog_posts (postTitle, postContent, postCategory, postTags, postDate, postURI)
        VALUES (:title, :content, :category, :tags, :date, :uri)';
        $sth = $dbwriter->prepare($sql);
        $sth->execute(array(':title' => $title, ':content' => $content, ':category' => $category, ':tags' => $tags, ':date' => $date, ':uri' => $uri));
    }

    if (isset($_SESSION['login']) && $_SESSION['login'] == ADMINUSER) {
        showPostForm();
    } else {
        showLoginForm();
    }

    function showPostForm() {
        ?>
        <section id="contact">
            <div class="container">
                <form id="contact__form" action="" method="post">
                    <div class="contact__input">
                        <input id="contact__title" name="title" type="text" required="">
                        <span class="contact__input--title">Title</span>
                    </div>
                    <div id="contact__message--container">
                        <textarea name="content" id="contact__message" cols="30" rows="5" required=""></textarea>
                        <span class="contact__input--title">Post Content</span>
                    </div>
                    <div class="contact__input">
                        <input id="contact__tags" name="tags" type="text" required="">
                        <span class="contact__input--title">Tags (comma seperated)</span>
                    </div>
                    <div class="contact__input">
                        <input id="contact__category" name="category" type="text" required="">
                        <span class="contact__input--title">Category</span>
                    </div>
                    <button class="button__contact" name="contact" type="submit">
                        <span>Send</span>
                    </button>
                </form>
            </div>
        </section>
        
        <script>
            $(".contact__input input, #contact__message").blur(function() {
                if ($(this).val().length > 0) {
                    $(this).next().addClass("contact__input--title-entered");
                } else {
                    $(this).next().removeClass("contact__input--title-entered");
                }
            });
        </script>
        <?php
    }

    function showLoginForm() {
        ?>
        <section id="contact">
            <div class="container" id="contact">
                <form id="contact__form" action="" method="post">
                    <div class="contact__input">
                        <input id="contact__user" name="user" type="text" required="">
                        <span class="contact__input--title">Username</span>
                    </div>
                    <div class="contact__input">
                        <input id="contact__password" name="password" type="password" required="">
                        <span class="contact__input--title">Password</span>
                    </div>
                    <button class="button__contact" name="contact" type="submit">
                        <span>Send</span>
                    </button>
                </form>
            </div>
        </section>
        
        <script>
            $(".contact__input input, #contact__message").blur(function() {
                if ($(this).val().length > 0) {
                    $(this).next().addClass("contact__input--title-entered");
                } else {
                    $(this).next().removeClass("contact__input--title-entered");
                }
            });
        </script>
        <?php
    }
    
?>