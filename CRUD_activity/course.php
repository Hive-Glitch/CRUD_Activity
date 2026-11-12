<?php
require 'config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $code = strtoupper(trim($_POST['course_code'] ?? ''));
    $stat = ($_POST['status'] ?? 'on') === 'off' ? 'off' : 'on';

    if ($name && $code) {
        $name_e = mysqli_real_escape_string($mysqli, $name);
        $desc_e = mysqli_real_escape_string($mysqli, $desc);
        $code_e = mysqli_real_escape_string($mysqli, $code);
        $stat_e = mysqli_real_escape_string($mysqli, $stat);

        $sql = "INSERT INTO course (name, description, course_code, status) 
                VALUES ('$name_e', '$desc_e', '$code_e', '$stat_e')";
        mysqli_query($mysqli, $sql);
        
        header("Location: course.php");
        exit;
    }
}
$sql_courses = "SELECT * FROM course ORDER BY course_code";
$courses = mysqli_query($mysqli, $sql_courses);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Courses</title>
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
    max-width: 1100px;
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
.filters {
    display: inline-block;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 12px 16px;
    margin: 0 auto 20px;
    text-align: center;
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
}
.filters input,
.filters select {
    padding: 8px 12px;
    margin: 3px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-family: inherit;
    font-size: 13px;
    outline: none;
}
.filters button {
    padding: 8px 16px;
    margin: 3px;
    background: #0056b3;
    color: #fff;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-family: inherit;
    font-size: 13px;
    font-weight: 600;
}
.filters button:hover {
    background: #004494;
}
#statusSelect {
    font-weight: 600;
    cursor: pointer;
}
table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
}
th, td {
    padding: 12px 14px;
    border-bottom: 1px solid #eee;
    text-align: center;
    font-size: 13px;
}
th {
    background: #0056b3;
    font-weight: bold;
    color: #fff;
    text-transform: uppercase;
    font-size: 12px;
}
tr:last-child td {
    border-bottom: none;
}
tr:hover td {
    background: #fcfcfc;
}
.status-on {
    background: #d4edda;
    color: #155724;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
}
.status-off {
    background: #f8d7da;
    color: #721c24;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
}
</style>
</head>
<body>

<div class="wrap">
    <div class="topnav">
        <a href="index.php">Users</a>
        <a href="course.php"><strong>Courses</strong></a>
    </div>

    <h1>Courses</h1>

    <form method="post" class="filters">
        <input type="text" name="name" placeholder="Name" required>
        <input type="text" name="description" placeholder="Description">
        <input type="text" name="course_code" placeholder="Code" required>
        
        <select name="status" id="statusSelect" onchange="updateStatusColor(this)">
            <option value="on">on</option>
            <option value="off">off</option>
        </select>
        
        <button type="submit">Add</button>
    </form>

    <table>
        <thead>
            <tr><th>ID</th><th>Code</th><th>Name</th><th>Description</th>
                <th>Status</th><th>Date Created</th></tr>
        </thead>
        <tbody>
        <?php while ($c = mysqli_fetch_assoc($courses)): ?>
            <tr>
                <td><?= htmlspecialchars($c['Course_ID']) ?></td>
                <td><?= htmlspecialchars($c['course_code']) ?></td>
                <td><?= htmlspecialchars($c['name']) ?></td>
                <td><?= htmlspecialchars($c['description']) ?></td>
                <td>
                    <?php if ($c['status'] === 'on'): ?>
                        <span class="status-on">On</span>
                    <?php else: ?>
                        <span class="status-off">Off</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($c['date_created']) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
function updateStatusColor(select) {
    if (select.value === 'on') {
        select.style.backgroundColor = '#d4edda';
        select.style.color = '#155724';
    } else {
        select.style.backgroundColor = '#f8d7da';
        select.style.color = '#721c24';
    }
}
window.onload = function() {
    updateStatusColor(document.getElementById('statusSelect'));
};
</script>

</body>
</html>