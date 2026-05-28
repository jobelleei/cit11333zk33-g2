<?php
require 'auth.php';

if (!isset($_SESSION['subjects'])) {
    $_SESSION['subjects'] = [
        ["id" => 1, "code" => "MATH101", "name" => "General Mathematics",    "teacher" => "Mr. Batumbakal",    "units" => 4, "schedule" => "MWF 7:30–8:30"],
        ["id" => 2, "code" => "ENG101",  "name" => "Oral Communication",     "teacher" => "Ms. Flores",        "units" => 2, "schedule" => "TTH 9:00–10:00"],
        ["id" => 3, "code" => "SCI101",  "name" => "Earth and Life Science", "teacher" => "Ms. Lim",           "units" => 4, "schedule" => "MWF 10:00–11:00"],
        ["id" => 4, "code" => "FIL101",  "name" => "Komunikasyon",           "teacher" => "Mr. Ramos",         "units" => 2, "schedule" => "TTH 1:00–2:00"],
        ["id" => 5, "code" => "PE101",   "name" => "Physical Education",     "teacher" => "Coach Delos Reyes", "units" => 2, "schedule" => "WF 2:00–3:00"],
        ["id" => 6, "code" => "HIST101", "name" => "Philippine History",     "teacher" => "Ms. Bautista",      "units" => 3, "schedule" => "MWF 1:00–2:00"],
    ];
}

$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'add';

    if ($action === 'add') {
        // --- ADD ---
        $new_code     = strtoupper(trim($_POST['code']));
        $new_name     = trim($_POST['name']);
        $new_teacher  = trim($_POST['teacher']);
        $new_units    = (int) $_POST['units'];
        $new_schedule = trim($_POST['schedule']);

        $last_id = count($_SESSION['subjects']) > 0
                   ? max(array_column($_SESSION['subjects'], 'id'))
                   : 0;

        $_SESSION['subjects'][] = [
            "id"       => $last_id + 1,
            "code"     => $new_code,
            "name"     => $new_name,
            "teacher"  => $new_teacher,
            "units"    => $new_units,
            "schedule" => $new_schedule,
        ];

        $_SESSION['flash'] = "\"$new_name\" has been added to your subjects.";
        header('Location: subjects.php');
        exit;
    }

    if ($action === 'edit') {
        // --- EDIT ---
        $edit_id       = (int) $_POST['subject_id'];
        $edit_code     = strtoupper(trim($_POST['code']));
        $edit_name     = trim($_POST['name']);
        $edit_teacher  = trim($_POST['teacher']);
        $edit_units    = (int) $_POST['units'];
        $edit_schedule = trim($_POST['schedule']);

        foreach ($_SESSION['subjects'] as &$subject) {
            if ($subject['id'] === $edit_id) {
                $subject['code']     = $edit_code;
                $subject['name']     = $edit_name;
                $subject['teacher']  = $edit_teacher;
                $subject['units']    = $edit_units;
                $subject['schedule'] = $edit_schedule;
                break;
            }
        }
        unset($subject);

        $_SESSION['flash'] = "\"$edit_name\" has been updated.";
        header('Location: subjects.php');
        exit;
    }

    if ($action === 'delete') {
        // --- DELETE ---
        $delete_id = (int) $_POST['subject_id'];

        foreach ($_SESSION['subjects'] as $key => $subject) {
            if ($subject['id'] === $delete_id) {
                unset($_SESSION['subjects'][$key]);
                break;
            }
        }

        $_SESSION['subjects'] = array_values($_SESSION['subjects']);

        $_SESSION['flash'] = "Subject has been deleted.";
        header('Location: subjects.php');
        exit;
    }
}

if (isset($_SESSION['flash'])) {
    $success_message = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

$subjects       = $_SESSION['subjects'];
$total_subjects = count($subjects);
$total_units    = array_sum(array_column($subjects, 'units'));

$active_page = 'subjects';
$page_title  = 'Subjects';
$page_icon   = '<i class="bi bi-journal-text"></i>';
include 'header.php';
?>

<?php if ($success_message): ?>
<div class="alert-success">✅ <?= htmlspecialchars($success_message) ?></div>
<?php endif; ?>

<div class="stats-row">
    <div class="stat-card">
        <div class="stat-label">Total Subjects</div>
        <div class="stat-value blue"><?= $total_subjects ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Units</div>
        <div class="stat-value green"><?= $total_units ?></div>
    </div>
</div>

<div class="form-card">
    <div class="form-card-header">
        <div class="form-card-title">Add New Subject</div>
    </div>
    <div class="form-body">
        <form method="POST" action="">
            <input type="hidden" name="action" value="add">
            <div class="form-grid">
                <div class="form-group">
                    <label for="code">Subject Code</label>
                    <input type="text" id="code" name="code" placeholder="e.g. MATH102" required maxlength="10">
                </div>
                <div class="form-group">
                    <label for="name">Subject Name</label>
                    <input type="text" id="name" name="name" placeholder="e.g. Statistics and Probability" required>
                </div>
                <div class="form-group">
                    <label for="teacher">Teacher</label>
                    <input type="text" id="teacher" name="teacher" placeholder="e.g. Ms. Cruz" required>
                </div>
                <div class="form-group">
                    <label for="units">Units</label>
                    <select id="units" name="units" required>
                        <option value="">— Select —</option>
                        <option value="1">1 unit</option>
                        <option value="2">2 units</option>
                        <option value="3">3 units</option>
                        <option value="4">4 units</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="schedule">Schedule</label>
                    <input type="text" id="schedule" name="schedule" placeholder="e.g. MWF 7:30–8:30" required>
                </div>
            </div>
            <button type="submit" class="btn-submit"><i class="bi bi-plus-square"></i> Add Subject</button>
        </form>
    </div>
</div>

<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">Enrolled Subjects</div>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Code</th>
                <th>Subject Name</th>
                <th>Teacher</th>
                <th>Units</th>
                <th>Schedule</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($total_subjects === 0): ?>
            <tr>
                <td colspan="7" style="text-align:center; padding:24px; color:var(--text-muted);">
                    No subjects yet. Use the form above to add one.
                </td>
            </tr>
            <?php endif; ?>

            <?php foreach ($subjects as $i => $subject): ?>
            <tr>
                <td class="id-cell"><?= $i + 1 ?></td>
                <td class="code-cell"><?= htmlspecialchars($subject['code']) ?></td>
                <td><?= htmlspecialchars($subject['name']) ?></td>
                <td><?= htmlspecialchars($subject['teacher']) ?></td>
                <td class="id-cell"><?= $subject['units'] ?> units</td>
                <td class="schedule-tag"><?= htmlspecialchars($subject['schedule']) ?></td>
                <td>
                    <button type="button" class="btn-submit" style="width:auto; padding:8px 14px; margin-right:5px;"
                        onclick='openEditSubjectModal(<?= json_encode($subject) ?>)'>
                        Edit
                    </button>

                    <form method="POST" action="" style="display:inline;"
                        onsubmit="return confirm('Are you sure you want to delete this subject?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="subject_id" value="<?= htmlspecialchars($subject['id']) ?>">
                        <button type="submit" class="btn-submit" style="width:auto; padding:8px 14px;">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="editSubjectModal" class="custom-modal">
    <div class="custom-modal-content">
        <div class="custom-modal-header">
            <div class="form-card-title">Edit Subject</div>
            <button type="button" class="custom-modal-close" onclick="closeEditSubjectModal()">&times;</button>
        </div>

        <form method="POST" action="">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" id="edit_subject_id" name="subject_id">

            <div class="form-grid">
                <div class="form-group">
                    <label for="edit_code">Subject Code</label>
                    <input type="text" id="edit_code" name="code" required maxlength="10">
                </div>
                <div class="form-group">
                    <label for="edit_name">Subject Name</label>
                    <input type="text" id="edit_name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="edit_teacher">Teacher</label>
                    <input type="text" id="edit_teacher" name="teacher" required>
                </div>
                <div class="form-group">
                    <label for="edit_units">Units</label>
                    <select id="edit_units" name="units" required>
                        <option value="">— Select —</option>
                        <option value="1">1 unit</option>
                        <option value="2">2 units</option>
                        <option value="3">3 units</option>
                        <option value="4">4 units</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_schedule">Schedule</label>
                    <input type="text" id="edit_schedule" name="schedule" required>
                </div>
            </div>

            <button type="submit" class="btn-submit"><i class="bi bi-pencil-square"></i> Update Subject</button>
        </form>
    </div>
</div>

<style>
.custom-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    inset: 0;
    background: rgba(0, 0, 0, 0.45);
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.custom-modal-content {
    background: #fff;
    width: 100%;
    max-width: 700px;
    border-radius: 16px;
    padding: 20px;
}

.custom-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}

.custom-modal-close {
    border: none;
    background: transparent;
    font-size: 28px;
    cursor: pointer;
}
</style>

<script>
function openEditSubjectModal(subject) {
    document.getElementById('edit_subject_id').value = subject.id;
    document.getElementById('edit_code').value = subject.code;
    document.getElementById('edit_name').value = subject.name;
    document.getElementById('edit_teacher').value = subject.teacher;
    document.getElementById('edit_units').value = subject.units;
    document.getElementById('edit_schedule').value = subject.schedule;

    document.getElementById('editSubjectModal').style.display = 'flex';
}

function closeEditSubjectModal() {
    document.getElementById('editSubjectModal').style.display = 'none';
}

window.addEventListener('click', function(event) {
    const modal = document.getElementById('editSubjectModal');
    if (event.target === modal) {
        closeEditSubjectModal();
    }
});
</script>

<?php include 'footer.php'; ?>