<?php
// Include Config Files
include_once 'config/init.php';
session_start();

// Load Database
$themesDB = new Themes();
$weddingsDB = new Weddings();
$participantsDB = new Participants();
$rolesDB = new Roles();
$roleAssignmentsDB = new RoleAssignments(); // Use the RoleAssignments class

// Load Create Wedding Page
$createWeddingPage = new Template("templates/create_wedding_.php");
$createWeddingPage->pageTitle = "Wedding | Create";
$createWeddingPage->cssFileName = "create_wedding.css";
$createWeddingPage->themes = $themesDB->getThemes(); // Fetch themes

if (isset($_SESSION['wedding_id'])){
    header("Location: manage-wedding.php");
    exit();
}

if (isset($_POST['submit'])) {
    // Prepare wedding participants data
    $wedding_participants = [
        "Groom" => [
            "first_name" => Sanitizer::test_input($_POST["groom_firstName"] ?? ""),
            "last_name" => Sanitizer::test_input($_POST["groom_lastName"] ?? "")
        ],
        "Bride" => [
            "first_name" => Sanitizer::test_input($_POST["bride_firstName"] ?? ""),
            "last_name" => Sanitizer::test_input($_POST["bride_lastName"] ?? "")
        ]
    ];

    // Prepare wedding data
    $wedding_data = [
        "email_address" => Sanitizer::validate_email($_POST["emailAddress"] ?? "") 
            ? Sanitizer::test_input($_POST["emailAddress"]) 
            : null, // Set null if email is invalid
        "mobile_number" => Sanitizer::test_input($_POST["mobileNumber"] ?? ""),
        "theme_id" => Sanitizer::test_input($_POST["theme_id"] ?? ""),
        "theme_color" => Sanitizer::test_input($_POST["theme_color"] ?? ""),
        "wedding_location" => Sanitizer::test_input($_POST["weddingLocation"] ?? ""),
        "prenup_location" => Sanitizer::test_input($_POST["prenupLocation"] ?? ""),
        "wedding_date" => Sanitizer::test_input($_POST["weddingDate"] ?? ""),
        "wedding_key" => Sanitizer::test_input($_POST["weddingKey"] ?? ""),
        "password" => Sanitizer::test_input($_POST["password"] ?? "")
    ];
    // Create wedding
    $wedding_id = $weddingsDB->createWedding($wedding_data);

    // Create participants and assign roles
    foreach ($wedding_participants as $role => $participant) {
        $participant_id = $participantsDB->createParticipant($participant); // Create participant
        $roleAssignmentsDB->createRoleAssignment($wedding_id, $participant_id, $rolesDB->getRoleIdByName($role)); // Assign role
    }

    // Redirect to the wedding event page or manage page after successful creation
    session_start();
    $_SESSION['wedding_id'] = $wedding_id;
    header("Location: manage-wedding.php");
    exit();
}

echo $createWeddingPage; // Display Create Wedding Page
?>