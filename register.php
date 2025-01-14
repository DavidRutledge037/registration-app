<?php
session_start();
require_once 'config/database.php';
require_once 'classes/User.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input
    $errors = [];
    
    if (empty($_POST['username'])) {
        $errors[] = "Username is required";
    } elseif ($user->username = $_POST['username'] && $user->usernameExists()) {
        $errors[] = "Username already exists";
    }

    if (empty($_POST['email'])) {
        $errors[] = "Email is required";
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    } elseif ($user->email = $_POST['email'] && $user->emailExists()) {
        $errors[] = "Email already exists";
    }

    if (empty($_POST['password'])) {
        $errors[] = "Password is required";
    } elseif (strlen($_POST['password']) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }

    if ($_POST['password'] !== $_POST['confirm_password']) {
        $errors[] = "Passwords do not match";
    }

    if (empty($_POST['first_name'])) {
        $errors[] = "First name is required";
    }

    if (empty($_POST['last_name'])) {
        $errors[] = "Last name is required";
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        $user->username = $_POST['username'];
        $user->password = $_POST['password'];
        $user->email = $_POST['email'];
        $user->first_name = $_POST['first_name'];
        $user->last_name = $_POST['last_name'];
        $user->phone = $_POST['phone'];

        if ($user->create()) {
            $message = "Registration successful! Please login.";
            $messageType = "success";
        } else {
            $message = "Registration failed. Please try again.";
            $messageType = "error";
        }
    } else {
        $message = implode("<br>", $errors);
        $messageType = "error";
    }
}

include 'includes/header.php';
?>

<div class="form-container">
    <h2>Create Account</h2>
    
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
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-input" 
                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" name="first_name" id="first_name" class="form-input" 
                   value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" name="last_name" id="last_name" class="form-input" 
                   value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="phone" class="form-label">Phone Number</label>
            <input type="tel" name="phone" id="phone" class="form-input" 
                   value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" id="password" class="form-input" required>
        </div>

        <div class="form-group">
            <label for="confirm_password" class="form-label">Confirm Password</label>
            <input type="password" name="confirm_password" id="confirm_password" class="form-input" required>
        </div>

        <button type="submit" class="btn btn-primary">Register</button>
    </form>
    
    <p class="form-footer">
        Already have an account? <a href="login.php">Login here</a>
    </p>
</div>

<?php include 'includes/footer.php'; ?> 