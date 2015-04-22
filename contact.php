<?php
    $send_to = "danielfoad@gmail.com";
    $send_subject = "Contact Message from site";

    $f_name = cleanupentries($_POST["name"]);
    $f_email = cleanupentries($_POST["email"]);
    $f_message = cleanupentries($_POST["message"]);
    $from_ip = $_SERVER["REMOTE_ADDR"];
    $from_browser = $_SERVER["HTTP_USER_AGENT"];

    function cleanupentries($entry) {
        $entry = trim($entry);
        $entry = stripslashes($entry);
        $entry = htmlspecialchars($entry);

        return $entry;
    }

    $message = "This email was submitted on " . date("d-m-Y") .
               "\n\nName: " . $f_name .
               "\n\nEmail: " . $f_email .
               "\n\nMessage: " . $f_message .
               "\n\nTechnical Details: " . $from_ip . "\n" . $from_browser;

    $send_subject .= " - {$f_name}";

    $headers = "From: " . $f_email . "\r\n" .
               "Reply-To: " . $f_email  ."\r\n" . 
               "X_Mailer: PHP/" . phpversion();

    if (!$f_email || !$f_name || !$f_message) {
        echo "Error: Invalid details in contact form";
        exit;
    } else  {
        if (filter_var($f_email, FILTER_VALIDATE_EMAIL)) {
            mail($send_to, $send_subject, $message, $headers);
            echo "Message sent successfully!";
        } else {
            echo "Error: Invalid email address specified";
        }
    }
?>