<?php
require 'config/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$user = ['Firstname'=>'','Lastname'=>'','Middlename'=>'','Birthdate'=>'','Gender'=>'','Email'=>''];
$selectedCourses = [];

if ($id > 0) {
    $sql = "SELECT * FROM users WHERE user_ID = $id";
    $res = mysqli_query($mysqli, $sql);
    $found = mysqli_fetch_assoc($res);
    if (!$found) {
        header("Location: index.php");
        exit;
    }
    $user = $found;

    $sql_c = "SELECT course_ID FROM user_course WHERE user_ID = $id";
    $res_c = mysqli_query($mysqli, $sql_c);
    while ($row = mysqli_fetch_assoc($res_c)) {
        $selectedCourses[] = $row['course_ID'];
    }
}

$sql_courses = "SELECT * FROM course WHERE status='on' ORDER BY course_code";
$courses = mysqli_query($mysqli, $sql_courses);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= $id ? 'Edit' : 'Add' ?> User</title>
<style>
* {
    box-sizing: border-box;
}
body {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
    background: #f8f9fa; 
    color: #333; 
    margin: 0; 
    padding: 40px 20px; 
    font-size: 14px;
}
.wrap {
    max-width: 600px;
    margin: 0 auto;
    text-align: center;
}
h1 {
    font-size: 22px;
    margin: 0 0 20px;
    font-weight: 600;
    color: #2c3e50;
}
a {
    color: #0056b3;
    text-decoration: none;
    font-weight: 500;
}
a:hover {
    text-decoration: underline;
}
.topnav {
    margin-bottom: 25px;
}
.topnav a {
    margin: 0 12px;
}
.box {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 24px;
    text-align: left;
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
}
label {
    display: block;
    margin-bottom: 15px;
}
label span {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    font-size: 13px;
    color: #555;
}
label input, label select {
    width: 100%;
    padding: 9px 12px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-family: inherit;
    font-size: 13px;
    outline: none;
}
label input:focus, label select:focus {
    border-color: #0056b3;
}
fieldset {
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 15px;
    margin: 18px 0;
}
legend {
    font-weight: 600;
    font-size: 13px;
    padding: 0 6px;
    color: #555;
}
.checkitem {
    display: block;
    margin-bottom: 8px;
    font-weight: normal;
}
.checkitem input {
    width: auto;
    margin-right: 6px;
}
.buttons {
    margin-top: 20px;
    text-align: center;
}
.buttons button {
    padding: 9px 20px;
    background: #0056b3;
    color: #fff;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    margin: 0 4px;
    font-family: inherit;
    font-size: 13px;
    font-weight: 600;
}
.buttons button:hover {
    background: #004494;
}
.buttons a {
    padding: 9px 20px;
    border: 1px solid #ccc;
    color: #333;
    display: inline-block;
    border-radius: 8px;
    margin: 0 4px;
    font-size: 13px;
    font-weight: 600;
}
.buttons a:hover {
    background: #f0f0f0;
    text-decoration: none;
}
.error {
    background: #fff5f5;
    border: 1px solid #feb2b2;
    color: #c53030;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 18px;
    text-align: center;
    font-size: 13px;
}
</style>
</head>
<body>

<div class="wrap">
    <div class="topnav">
        <a href="index.php">Users</a>
        <a href="course.php">Courses</a>
    </div>

    <h1><?= $id ? 'Edit User' : 'Add User' ?></h1>

    <?php if (!empty($_GET['error'])): ?>
        <div class="error"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <form method="post" action="save.php" class="box">
        <input type="hidden" name="id" value="<?= $id ?>">

        <label><span>First Name *</span>
            <input type="text" name="Firstname" required value="<?= htmlspecialchars($user['Firstname']) ?>">
        </label>

        <label><span>Last Name *</span>
            <input type="text" name="Lastname" required value="<?= htmlspecialchars($user['Lastname']) ?>">
        </label>

        <label><span>Middle Name</span>
            <input type="text" name="Middlename" value="<?= htmlspecialchars($user['Middlename']) ?>">
        </label>

        <label><span>Birthdate *</span>
            <input type="date" name="Birthdate" required value="<?= htmlspecialchars($user['Birthdate']) ?>">
        </label>

        <label><span>Gender *</span>
            <select name="Gender" required>
                <option value="">-- Select --</option>
                <option value="M" <?= $user['Gender'] == 'M' ? 'selected' : '' ?>>Male</option>
                <option value="F" <?= $user['Gender'] == 'F' ? 'selected' : '' ?>>Female</option>
            </select>
        </label>

        <label><span>Email *</span>
            <input type="email" name="Email" required value="<?= htmlspecialchars($user['Email']) ?>">
        </label>

        <fieldset>
            <legend>Courses</legend>
            <?php if (mysqli_num_rows($courses) == 0): ?>
                <p>No active courses. <a href="course.php">Add one</a>.</p>
            <?php endif; ?>
            <?php while ($c = mysqli_fetch_assoc($courses)): ?>
                <label class="checkitem">
                    <input type="checkbox" name="courses[]" value="<?= $c['Course_ID'] ?>"
                        <?= in_array($c['Course_ID'], $selectedCourses) ? 'checked' : '' ?>>
                    <?= htmlspecialchars($c['course_code']) ?> - <?= htmlspecialchars($c['name']) ?>
                </label>
            <?php endwhile; ?>
        </fieldset>

        <div class="buttons">
            <button type="submit"><?= $id ? 'Update' : 'Save' ?></button>
            <a href="index.php">Cancel</a>
        </div>
    </form>
</div>

</body>
</html>