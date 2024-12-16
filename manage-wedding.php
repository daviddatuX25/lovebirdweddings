<?php
include_once 'config/init.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the input values
    $weddingKey = $_POST['wedding_key'];
    $password = $_POST['password'];

    // Validate the credentials
    $weddingsDB = new Weddings();
    $wedding = $weddingsDB->getWeddingByKeyAndPassword($weddingKey, $password);

    if ($wedding) {
        // Store wedding ID in session
        $_SESSION['wedding_id'] = $wedding->wedding_id;
        $_SESSION['wedding_key'] = $weddingKey;

        // Redirect to manage wedding page
        header("Location: manage-wedding.php");
        exit();
    } else {
        // Invalid credentials, clear the session
        endSession();
        $error = "Invalid wedding key or password.";
    }
} else {
    // Clear session on direct access to the page
    if(isset($_SESSION["wedding_id"])){
        header("Location: manage-weddingDetails.php");
    } else {
        endSession();
    }
}

// Render the manage wedding login page
$manageWeddingPage = new Template("templates/manage-wedding_.php");
$manageWeddingPage->pageTitle = "Manage Wedding | Log-in";
$manageWeddingPage->cssFileName = "create_wedding.css";
$manageWeddingPage->weddingsDB = new Weddings();

echo $manageWeddingPage;

// Function to properly end the session
function endSession() {
    // Clear session variables
    $_SESSION = [];

    // Destroy the session
    session_destroy();

    // Clear the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
}
?>