<?php
require 'config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$id         = (int)($_POST['id'] ?? 0);
$Firstname  = trim($_POST['Firstname'] ?? '');
$Lastname   = trim($_POST['Lastname'] ?? '');
$Middlename = trim($_POST['Middlename'] ?? '');
$Birthdate  = trim($_POST['Birthdate'] ?? '');
$Gender     = trim($_POST['Gender'] ?? '');
$Email      = trim($_POST['Email'] ?? '');
$courses    = $_POST['courses'] ?? [];

$error_msg = "";

if ($Firstname == '') $error_msg .= "First name is required. ";
if ($Lastname == '')  $error_msg .= "Last name is required. ";
if ($Birthdate == '') $error_msg .= "Birthdate is required. ";
if ($Gender == '')    $error_msg .= "Gender is required. ";
if ($Email == '')     $error_msg .= "Email is required. ";

if ($error_msg == "") {
    $email_e = mysqli_real_escape_string($mysqli, $Email);
    
    if ($id > 0) {
        $check_sql = "SELECT user_ID FROM users WHERE LOWER(Email)=LOWER('$email_e') AND user_ID != $id";
    } else {
        $check_sql = "SELECT user_ID FROM users WHERE LOWER(Email)=LOWER('$email_e')";
    }
    
    $check_res = mysqli_query($mysqli, $check_sql);
    if (mysqli_num_rows($check_res) > 0) {
        $error_msg .= "Email already exists. ";
    }
}

if ($error_msg != "") {
    $url = "form.php?error=" . urlencode($error_msg);
    if ($id > 0) $url .= "&id=$id";
    header("Location: $url");
    exit;
}

$Firstname_e  = mysqli_real_escape_string($mysqli, $Firstname);
$Lastname_e   = mysqli_real_escape_string($mysqli, $Lastname);
$Middlename_e = mysqli_real_escape_string($mysqli, $Middlename);
$Birthdate_e  = mysqli_real_escape_string($mysqli, $Birthdate);
$Gender_e     = mysqli_real_escape_string($mysqli, $Gender);
$Email_e      = mysqli_real_escape_string($mysqli, $Email);

if ($id > 0) {
    $sql = "UPDATE users SET 
            Firstname='$Firstname_e', 
            Lastname='$Lastname_e', 
            Middlename='$Middlename_e', 
            Birthdate='$Birthdate_e', 
            Gender='$Gender_e', 
            Email='$Email_e' 
            WHERE user_ID = $id";
    mysqli_query($mysqli, $sql);
} else {
    $sql = "INSERT INTO users (Firstname, Lastname, Middlename, Birthdate, Gender, Email) 
            VALUES ('$Firstname_e', '$Lastname_e', '$Middlename_e', '$Birthdate_e', '$Gender_e', '$Email_e')";
    mysqli_query($mysqli, $sql);
    $id = mysqli_insert_id($mysqli);
}

$del_sql = "DELETE FROM user_course WHERE user_ID = $id";
mysqli_query($mysqli, $del_sql);

if (!empty($courses)) {
    foreach ($courses as $cid) {
        $cid = (int)$cid;
        if ($cid > 0) {
            $ins_sql = "INSERT INTO user_course (user_ID, course_ID) VALUES ($id, $cid)";
            mysqli_query($mysqli, $ins_sql);
        }
    }
}

header("Location: index.php");
exit;
?>