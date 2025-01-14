<?php
session_start();

// Redirect if already logged in
if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'config/database.php';
require_once 'classes/User.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $message = "Please fill in all fields";
        $messageType = "error";
    } else {
        if ($user->login($username, $password)) {
            // Set session variables
            $_SESSION['user_id'] = $user->user_id;
            $_SESSION['username'] = $user->username;
            $_SESSION['first_name'] = $user->first_name;
            $_SESSION['last_name'] = $user->last_name;

            // Redirect to dashboard/home page
            header("Location: index.php");
            exit();
        } else {
            $message = "Invalid username or password";
            $messageType = "error";
        }
    }
}

include 'includes/header.php';
?>

<div class="form-container">
    <h2>Login</h2>
    
    <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" id="username" class="form-input" 
                   value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" id="password" class="form-input" required>
        </div>

        <button type="submit" class="btn btn-primary">Login</button>
    </form>
    
    <p class="form-footer">
        Don't have an account? <a href="register.php">Register here</a><br>
        <a href="forgot-password.php">Forgot Password?</a>
    </p>
</div>

<?php include 'includes/footer.php'; ?> 