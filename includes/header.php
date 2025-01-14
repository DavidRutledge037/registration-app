<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration App</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <nav class="main-nav">
            <div class="container nav-container">
                <a href="index.php" class="logo">Registration App</a>
                <ul class="nav-links">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li><a href="index.php">Dashboard</a></li>
                        <li><a href="course-registration.php">Courses</a></li>
                        <li><a href="profile.php">Profile</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>
    <main class="container"> 