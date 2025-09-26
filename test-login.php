<?php
session_start();

// Force a login session for testing
$_SESSION['user_logged_in'] = true;
$_SESSION['username'] = 'admin';
$_SESSION['user_id'] = 'admin';
$_SESSION['user_role'] = 'admin';
$_SESSION['user_name'] = 'Administrator';

echo "Session created successfully!<br>";
echo "User logged in: " . ($_SESSION['user_logged_in'] ? 'YES' : 'NO') . "<br>";
echo "Username: " . $_SESSION['username'] . "<br>";
echo "Role: " . $_SESSION['user_role'] . "<br>";
echo "<br>";
echo '<a href="admin/dashboard.php">Go to Admin Dashboard</a><br>';
echo '<a href="admin/edit-section.php?section=hero">Edit Hero Section</a><br>';
echo '<a href="index-cms.html">View CMS Website</a><br>';
?>