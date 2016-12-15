<?php

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//performs a few security checks
function securityCheck($path_parts, $yourURL, $form = false) {
    $passed = true; // start off thinking everything is good until a test fails

    $debug = false;

    //not completly safe
    if (isset($_SERVER['HTTP_REFERER'])) {
        $link = htmlentities($_SERVER['HTTP_REFERER'], ENT_QUOTES, "UTF-8");
        $url = parse_url($link);
        $fromPage = "//" . $url['host'] . $url['path'];
    } else {
        $fromPage = "noplace";
    }

    // add all your page names to this array
    $whiteListPages = array();
    $whiteListPages[] = "index.php";
    $whiteListPages[] = 'form.php';
    $whiteListPages[] = 'confirmation.php';
    $whiteListPages[] = 'top.php';
    $whiteListPages[] = 'tables.php';
    $whiteListPages[] = 'pass.php';
    $whiteListPages[] = 'addPhysician.php';
    $whiteListPages[] = 'dbh.php';
    $whiteListPages[] = 'loggedin.php';
    $whiteListPages[] = 'chklogin.php';
    $whiteListPages[] = 'login.php';
    $whiteListPages[] = 'signup.php';
    $whiteListPages[] = 'style.css';
    $whiteListPages[] = "testForm.php";
    $whiteListPages[] = "viewCompliance.php";
    $whiteListPages[] = "patientSessions.php";
    $whiteListPages[] = "updatePatient.php";
    $whiteListPages[] = "managePatients.php";
    $whiteListPages[] = "managePhysicians.php";


    $whiteListPages[] = 'post.php';


    $whiteListPages[] = 'delete.php';

    $whiteListPages[] = 'footer.php';


    //add all the folders to this array
    $whiteListFolders = array();
    $whiteListFolders[] = "/CS275";
    $whiteListFolders[] = "/CS275/physicianPortal";




    // Check for valid page name
    if (!in_array($path_parts['basename'], $whiteListPages)) {
        $passed = false;
        $errorMsg[] = "<p>Failed white list pages check: " . $path_parts['basename'] . "</p>";
    }

    // Check for valid folder name
    if (!in_array($path_parts['dirname'], $whiteListFolders)) {
        $passed = false;
        $errorMsg[] = "<p>Failed white list folders check: " . $path_parts['dirname'] . "</p>";
    }

    // Check server
    $server = htmlentities($_SERVER['SERVER_NAME'], ENT_QUOTES, "UTF-8");
    if ($server != get_current_user() . ".w3.uvm.edu") {
        $passed = false;
        $errorMsg[] = "<p>Failed server check: " . $server . "</p>";
    }

    // when it is a form page check to make sure it submitted to itself
    if ($form) {
        $errorMsg[] = "<p>From: " . $fromPage . " should match your Url: " . $yourURL;
        if ($fromPage != $yourURL) {
            $passed = false;
            $errorMsg[] = "<p>Failed from page check" . $path_parts['dirname'] . "</p>";
        }
    }

    $errorMsg[] = "<p>returning: " . $passed;

    $message = join("\n", $errorMsg);
    if ($debug) {
        print $message;
        print "<p>path_parts<pre>";
        print_r($path_parts);
        print "</pre></p>";

        print "<p>white list pages<pre>";
        print_r($whiteListPages);
        print "</pre></p>";

        print "<p>white list folder<pre>";
        print_r($whiteListFolders);
        print "</pre></p>";
    }

    if (!$passed) {
        //send message to me
        $message = "<p>Login failed: " . date("F j, Y") . " at " . date("h:i:s") . "</p>\n" . $message;

        $to = ADMIN_EMAIL;
        $cc = "";
        $bcc = "";
        $from = "Site Login <security@uvm.edu>";
        $subject = "Login Status ";

        $mailed = sendMail($to, $cc, $bcc, $from, $subject, $message);
    }
    return $passed;
}
?>

