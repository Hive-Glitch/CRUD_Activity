<?php
require 'config/config.php';

$search = $_GET['search'] ?? '';
$gender = $_GET['gender'] ?? '';
$date   = $_GET['date'] ?? '';

$sql = "SELECT u.user_ID, u.Firstname, u.Lastname, u.Middlename,
               u.Birthdate, u.Gender, u.Email, u.date_created,
               GROUP_CONCAT(DISTINCT c.course_code ORDER BY c.course_code SEPARATOR ', ') AS course_codes
        FROM users u
        LEFT JOIN user_course uc ON uc.user_ID = u.user_ID
        LEFT JOIN course c ON c.Course_ID = uc.course_ID
        WHERE 1=1";

if ($search !== '') {
    $search_e = mysqli_real_escape_string($mysqli, $search);
    $sql .= " AND (u.Firstname LIKE '%$search_e%' OR u.Lastname LIKE '%$search_e%' 
              OR u.Middlename LIKE '%$search_e%' OR c.description LIKE '%$search_e%' 
              OR c.course_code LIKE '%$search_e%')";
}

if ($gender !== '') {
    $gender_e = mysqli_real_escape_string($mysqli, $gender);
    $sql .= " AND u.Gender = '$gender_e'";
}

if ($date !== '') {
    $date_e = mysqli_real_escape_string($mysqli, $date);
    $sql .= " AND DATE(u.date_created) = '$date_e'";
}

$sql .= " GROUP BY u.user_ID ORDER BY u.user_ID DESC";
$result = mysqli_query($mysqli, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Users</title>
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
.filters input[type=text] {
    width: 180px;
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
.header-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
.header-row h1 {
    margin: 0;
}
.add-btn {
    display: inline-block;
    background: #0056b3;
    color: #fff;
    padding: 9px 20px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
}
.add-btn:hover {
    background: #004494;
    text-decoration: none;
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
.empty {
    text-align: center;
    color: #888;
    padding: 25px;
}
.actions {
    white-space: nowrap;
}
.actions a {
    margin: 0 4px;
    font-size: 13px;
    padding: 4px 10px;
    border-radius: 6px;
}
.actions a:hover {
    background: #0056b3;
    color: #fff;
    text-decoration: none;
}
</style>
</head>
<body>

<div class="wrap">
    <div class="topnav">
        <a href="index.php"><strong>Users</strong></a>
        <a href="course.php">Courses</a>
    </div>

    <div class="header-row">
        <h1>Registered Users</h1>
        <a href="form.php" class="add-btn">Add User</a>
    </div>

    <form method="get" action="index.php" class="filters">
        <input type="text" name="search" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
        <select name="gender">
            <option value="">Gender</option>
            <option value="M" <?= $gender == 'M' ? 'selected' : '' ?>>Male</option>
            <option value="F" <?= $gender == 'F' ? 'selected' : '' ?>>Female</option>
        </select>
        <input type="date" name="date" value="<?= htmlspecialchars($date) ?>">
        <button type="submit">Filter</button>
        <a href="index.php">Reset</a>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th><th>Name</th><th>Birthdate</th><th>Gender</th>
                <th>Email</th><th>Courses</th><th>Date Created</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['user_ID']) ?></td>
                    <td><?= htmlspecialchars($row['Lastname'] . ', ' . $row['Firstname']) ?>
                        <?= $row['Middlename'] ? ' ' . htmlspecialchars($row['Middlename']) : '' ?></td>
                    <td><?= htmlspecialchars($row['Birthdate']) ?></td>
                    <td><?= $row['Gender'] == 'M' ? 'Male' : 'Female' ?></td>
                    <td><?= htmlspecialchars($row['Email']) ?></td>
                    <td><?= $row['course_codes'] ? htmlspecialchars($row['course_codes']) : '-' ?></td>
                    <td><?= htmlspecialchars($row['date_created']) ?></td>
                    <td class="actions">
                        <a href="form.php?id=<?= $row['user_ID'] ?>">Edit</a>
                        <a href="delete.php?id=<?= $row['user_ID'] ?>" onclick="return confirm('Delete this user?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="8" class="empty">No users found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>