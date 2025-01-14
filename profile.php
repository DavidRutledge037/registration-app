<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'includes/auth_check.php';
require_once 'config/database.php';
require_once 'classes/User.php';

checkAuth();

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$message = '';
$messageType = '';
$passwordMessage = '';
$passwordMessageType = '';

// Get current user profile
$profile = $user->getUserProfile($_SESSION['user_id']);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $updateData = [
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone']
        ];

        if ($user->updateProfile($_SESSION['user_id'], $updateData)) {
            $message = "Profile updated successfully!";
            $messageType = "success";
            $profile = $user->getUserProfile($_SESSION['user_id']); // Refresh profile data
        } else {
            $message = "Error updating profile. Please try again.";
            $messageType = "error";
        }
    }

    // Handle password update
    if (isset($_POST['update_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password !== $confirm_password) {
            $passwordMessage = "New passwords do not match.";
            $passwordMessageType = "error";
        } elseif (strlen($new_password) < 6) {
            $passwordMessage = "Password must be at least 6 characters.";
            $passwordMessageType = "error";
        } else {
            if ($user->updatePassword($_SESSION['user_id'], $current_password, $new_password)) {
                $passwordMessage = "Password updated successfully!";
                $passwordMessageType = "success";
            } else {
                $passwordMessage = "Current password is incorrect.";
                $passwordMessageType = "error";
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="profile-container">
    <h2>My Profile</h2>

    <div class="profile-grid">
        <!-- Profile Information Section -->
        <div class="profile-section">
            <h3>Profile Information</h3>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="profile-form">
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" class="form-input" 
                           value="<?php echo htmlspecialchars($profile['username']); ?>" disabled>
                </div>

                <div class="form-group">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" name="first_name" id="first_name" class="form-input" 
                           value="<?php echo htmlspecialchars($profile['first_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" name="last_name" id="last_name" class="form-input" 
                           value="<?php echo htmlspecialchars($profile['last_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-input" 
                           value="<?php echo htmlspecialchars($profile['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="tel" name="phone" id="phone" class="form-input" 
                           value="<?php echo htmlspecialchars($profile['phone']); ?>">
                </div>

                <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
            </form>
        </div>

        <!-- Password Change Section -->
        <div class="profile-section">
            <h3>Change Password</h3>
            
            <?php if ($passwordMessage): ?>
                <div class="alert alert-<?php echo $passwordMessageType; ?>">
                    <?php echo $passwordMessage; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="password-form">
                <div class="form-group">
                    <label for="current_password" class="form-label">Current Password</label>
                    <input type="password" name="current_password" id="current_password" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" name="new_password" id="new_password" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-input" required>
                </div>

                <button type="submit" name="update_password" class="btn btn-primary">Change Password</button>
            </form>
        </div>

        <!-- Account Information -->
        <div class="profile-section account-info">
            <h3>Account Information</h3>
            <div class="info-item">
                <span class="label">Member Since:</span>
                <span class="value"><?php echo date('F d, Y', strtotime($profile['created_at'])); ?></span>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 