<?php

if (isset($_POST['btn-signup'])) {
    $DocID = strip_tags($_POST['DocID']);
    $fldEmail = strip_tags($_POST['fldEmail']);
    $fldPass = strip_tags($_POST['fldPass']);
    $fldAdmin = strip_tags($_POST['fldAdmin']);
    $DocID = $DBcon->real_escape_string($DocID);
    $fldEmail = $DBcon->real_escape_string($fldEmail);
    $fldPass = $DBcon->real_escape_string($fldPass);
    $hashed_password = password_hash($fldPass, PASSWORD_DEFAULT);
    $check_email = $DBcon->query("SELECT fldEmail FROM tblDoctor WHERE fldEmail='$fldEmail'");
    $count = $check_email->num_rows;
    if ($count == 0) {
        $query = "INSERT INTO tblDoctor(DocID,fldEmail,fldPass, fldAdmin) VALUES('$DocID','$fldEmail','$fldPass')";
        if ($DBcon->query($query)) {
            $msg = "<div class='alert alert-success'>
                <span class='glyphicon glyphicon-info-sign'></span> &nbsp; successfully registered !
                </div>";
        } else {
            $msg = "<div class='alert alert-danger'>
                <span class='glyphicon glyphicon-info-sign'></span> &nbsp; error while registering !
                </div>";
        }
    } else {
        $msg = "<div class='alert alert-danger>
            <span class='glyphicon glyphicon-info-sign'></span> &nbsp; sorry email already taken !
            </div>";
    }
    $DBcon->close();
}
?>

<form class="form-signin" method="post" id="register-form">

    <h2 class="form-signin-heading">Add New Physician</h2><hr />

    <?php
    if (isset($msg)) {
        echo $msg;
    }
    ?>

    <div class="form-group">
       Physician ID: <input type="text" class="form-control" placeholder="Doctor ID" name="DocID" required  />
    </div>

    <div class="form-group">
        Physician Email: <input type="email" class="form-control" placeholder="Email address" name="fldEmail" required  />
        <span id="check-e"></span>
    </div>

    <div class="form-group">
       Password: <input type="password" class="form-control" placeholder="Password" name="fldPass" required  />
    </div>

    <hr />

    <div class="form-group">
        <button type="submit" class="btn btn-default" name="btn-signup">
            <span class="glyphicon glyphicon-log-in"></span> &nbsp; Create Account
        </button> 
    </div> 

</form>