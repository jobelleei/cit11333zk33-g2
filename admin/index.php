<?php
require 'auth.php';
require_once '../config.php';

// Fetch the full user row from the database using the logged-in user ID
$student = null;

if (isset($logged_in_user['id'])) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
    $stmt->execute([
        ':id' => $logged_in_user['id']
    ]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$student && isset($logged_in_user['username'])) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
    $stmt->execute([
        ':username' => $logged_in_user['username']
    ]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
}

function displayDate($date)
{
    if (empty($date) || $date === '0000-00-00') {
        return '';
    }

    return date('F d, Y', strtotime($date));
}

// ----------------------------------------------------------
// PAGE TITLES
// ----------------------------------------------------------
$active_page = 'profile';
$page_title = 'Student Profile';
$page_icon = '<i class="bi bi-person-fill"></i>';

// Include header
include 'header.php'; 
?>

<div class="profile-header table-card" style="margin-bottom: 24px;">
    <div class="profile-banner">
        <img src="../src/assets/images/hiro-avatar.png" alt="Avatar">
    </div>
    <div class="profile-info-header">
        <div>
            <div class="profile-name"><?= htmlspecialchars($student['name']) ?></div>
            <div class="profile-id"><?= htmlspecialchars($student['student_no']) ?></div>
            <span class="badge badge-active"><?= htmlspecialchars($student['status']) ?></span>
        </div>
    </div>
</div>

<div class="table-card" style="margin-bottom: 24px;">
    <div class="table-card-header">
        <div class="table-card-title">Account Information</div>
    </div>
    <?php
    $account = [
        "Database ID" => $student['id'],
        "Student No." => $student['student_no'],
        "Username" => $student['username'],
        "Status" => $student['status'],
    ];
    foreach ($account as $label => $value): ?>
    <div class="info-row">
        <div class="info-row-label"><?= htmlspecialchars($label) ?></div>
        <div class="info-row-value"><?= htmlspecialchars($value) ?></div>
    </div>
    <?php endforeach; ?>
</div>

<div class="table-card" style="margin-bottom: 24px;">
    <div class="table-card-header">
        <div class="table-card-title">Personal Information</div>
    </div>
    <?php
    $personal = [
        "Full Name"  => $student['name'],
        "Birthdate"  => displayDate($student['birthdate']),
        "Age"        => !empty($student['age']) ? $student['age'] . " years old" : '',
        "Gender"     => $student['gender'],
        "Email"      => $student['email'],
        "Phone"      => $student['phone'],
        "Address"    => $student['address'],
    ];
    foreach ($personal as $label => $value): ?>
    <div class="info-row">
        <div class="info-row-label"><?= htmlspecialchars($label) ?></div>
        <div class="info-row-value"><?= htmlspecialchars($value) ?></div>
    </div>
    <?php endforeach; ?>
</div>

<div class="table-card" style="margin-bottom: 24px;">
    <div class="table-card-header">
        <div class="table-card-title">Guardian Information</div>
    </div>
    <?php
    $guardian = [
        "Guardian"     => $student['guardian'],
        "Relationship" => $student['guardian_rel'],
        "Contact No."  => $student['guardian_contact'],
    ];
    foreach ($guardian as $label => $value): ?>
    <div class="info-row">
        <div class="info-row-label"><?= htmlspecialchars($label) ?></div>
        <div class="info-row-value"><?= htmlspecialchars($value) ?></div>
    </div>
    <?php endforeach; ?>
</div>

<div class="table-card" style="margin-bottom: 0;">
    <div class="table-card-header">
        <div class="table-card-title">Academic Information</div>
    </div>
    <?php
    $academic = [
        "Grade"       => $student['grade'],
        "Section"     => $student['section'],
        "Track"       => $student['track'],
        "Strand"      => $student['strand'],
        "GPA"         => $student['gpa'],
        "Enrolled At" => displayDate($student['enrolled_at']),
    ];
    foreach ($academic as $label => $value): ?>
    <div class="info-row">
        <div class="info-row-label"><?= htmlspecialchars($label) ?></div>
        <div class="info-row-value"><?= htmlspecialchars($value) ?></div>
    </div>
    <?php endforeach; ?>
</div>

<?php include 'footer.php'; ?>