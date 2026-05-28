<?php
require 'auth.php';
require_once '../config.php';

$gradeModel = new Grade($conn);
$subjectModel = new Subject($conn);

$success_message = '';
$error_message = '';

$user_id = $logged_in_user['id'] ?? null;

if (!$user_id && isset($logged_in_user['username'])) {
    $userModel = new User($conn);
    $user = $userModel->findByUsername($logged_in_user['username']);
    $user_id = $user['id'] ?? null;
}

if (!$user_id) {
    header('Location: ../index.php');
    exit;
}

$subjects = $subjectModel->getAllByUser($user_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'add';

    if ($action === 'add') {
        $new_subject = trim($_POST['subject']);
        $new_prelim  = (int) $_POST['prelim'];
        $new_midterm = (int) $_POST['midterm'];
        $new_final   = (int) $_POST['final'];
        $new_grade   = round(($new_prelim + $new_midterm + $new_final) / 3);

        try {
            $gradeModel->create([
                'user_id' => $user_id,
                'subject' => $new_subject,
                'prelim'  => $new_prelim,
                'midterm' => $new_midterm,
                'final'   => $new_final,
                'grade'   => $new_grade,
            ]);

            $_SESSION['flash'] = "Grade for \"$new_subject\" added. Final grade: $new_grade";
            header('Location: grades.php');
            exit;
        } catch (PDOException $e) {
            $error_message = "Add failed: " . $e->getMessage();
        }
    }

    if ($action === 'edit') {
        $edit_id      = (int) $_POST['grade_id'];
        $edit_subject = trim($_POST['subject']);
        $edit_prelim  = (int) $_POST['prelim'];
        $edit_midterm = (int) $_POST['midterm'];
        $edit_final   = (int) $_POST['final'];
        $edit_grade   = round(($edit_prelim + $edit_midterm + $edit_final) / 3);

        try {
            $gradeModel->updateByUser($edit_id, $user_id, [
                'subject' => $edit_subject,
                'prelim'  => $edit_prelim,
                'midterm' => $edit_midterm,
                'final'   => $edit_final,
                'grade'   => $edit_grade,
            ]);

            $_SESSION['flash'] = "Grade for \"$edit_subject\" updated. Final grade: $edit_grade";
            header('Location: grades.php');
            exit;
        } catch (PDOException $e) {
            $error_message = "Update failed: " . $e->getMessage();
        }
    }

    if ($action === 'delete') {
        $delete_id = (int) $_POST['grade_id'];

        try {
            $gradeModel->deleteByUser($delete_id, $user_id);

            $_SESSION['flash'] = "Grade record has been deleted.";
            header('Location: grades.php');
            exit;
        } catch (PDOException $e) {
            $error_message = "Delete failed: " . $e->getMessage();
        }
    }
}

if (isset($_SESSION['flash'])) {
    $success_message = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

$grades = $gradeModel->getAllByUser($user_id);

$count      = count($grades);
$all_grades = array_column($grades, 'grade');
$avg_grade  = $count > 0 ? round(array_sum($all_grades) / $count, 1) : 0;
$highest    = $count > 0 ? max($all_grades) : 0;
$lowest     = $count > 0 ? min($all_grades) : 0;

$active_page = 'grades';
$page_title  = 'My Grades';
$page_icon   = '<i class="bi bi-trophy-fill"></i>';

include 'header.php';
?>

<main class="content">
    <?php if ($success_message): ?>
    <div class="alert-success">✅ <?= htmlspecialchars($success_message) ?></div>
    <?php endif; ?>

    <?php if ($error_message): ?>
    <div class="alert-success" style="background:#fee2e2; color:#991b1b;">
        <?= htmlspecialchars($error_message) ?>
    </div>
    <?php endif; ?>

    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-label">Avg Grade</div>
            <div class="stat-value blue"><?= htmlspecialchars($avg_grade) ?></div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Highest</div>
            <div class="stat-value green"><?= htmlspecialchars($highest) ?></div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Lowest</div>
            <div class="stat-value red"><?= htmlspecialchars($lowest) ?></div>
        </div>
    </div>

    <div class="form-card">
        <div class="form-card-header">
            <div class="form-card-title">Add Grade Record</div>
        </div>

        <div class="form-body">
            <p class="form-hint">Final Grade is auto-computed: (Prelim + Midterm + Final Exam) ÷ 3</p>

            <form method="POST" action="">
                <input type="hidden" name="action" value="add">

                <div class="form-grid">
                    <div class="form-group" style="grid-column: span 2;">
                        <label for="subject">Subject Name</label>

                        <?php if (count($subjects) > 0): ?>
                        <select id="subject" name="subject" required>
                            <option value="">— Select Subject —</option>
                            <?php foreach ($subjects as $subject): ?>
                            <option value="<?= htmlspecialchars($subject['name']) ?>">
                                <?= htmlspecialchars($subject['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <?php else: ?>
                        <input type="text" id="subject" name="subject"
                            placeholder="No subjects found. Add subjects first." required>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="prelim">Prelim Score</label>
                        <input type="number" id="prelim" name="prelim" min="0" max="100" placeholder="0 – 100" required>
                    </div>

                    <div class="form-group">
                        <label for="midterm">Midterm Score</label>
                        <input type="number" id="midterm" name="midterm" min="0" max="100" placeholder="0 – 100"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="final">Final Exam Score</label>
                        <input type="number" id="final" name="final" min="0" max="100" placeholder="0 – 100" required>
                    </div>
                </div>

                <button type="submit" class="btn-submit"><i class="bi bi-plus-square"></i> Add Grade Record</button>
            </form>
        </div>
    </div>

    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">Grade Report – 1st Semester</div>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Subject</th>
                    <th>Prelim</th>
                    <th>Midterm</th>
                    <th>Final Exam</th>
                    <th>Final Grade</th>
                    <th>Remarks</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($count === 0): ?>
                <tr>
                    <td colspan="8" style="text-align:center; padding:24px; color:var(--text-muted);">
                        No grades yet. Use the form above to add one.
                    </td>
                </tr>
                <?php endif; ?>

                <?php foreach ($grades as $i => $g): ?>
                <tr>
                    <td class="id-cell"><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($g['subject']) ?></td>
                    <td class="id-cell"><?= htmlspecialchars($g['prelim']) ?></td>
                    <td class="id-cell"><?= htmlspecialchars($g['midterm']) ?></td>
                    <td class="id-cell"><?= htmlspecialchars($g['final']) ?></td>
                    <td>
                        <?php
                        $fg = $g['grade'];
                        $gc = $fg >= 90 ? 'grade-high' : ($fg >= 85 ? 'grade-mid' : 'grade-low');
                        ?>
                        <span class="<?= $gc ?>"><?= htmlspecialchars($fg) ?></span>
                    </td>
                    <td>
                        <span class="badge <?= $fg >= 75 ? 'badge-active' : 'badge-probation' ?>">
                            <?= $fg >= 75 ? 'Passed' : 'Failed' ?>
                        </span>
                    </td>
                    <td>
                        <button type="button" class="btn-submit" style="width:auto; padding:8px 14px; margin-right:5px;"
                            onclick='openEditGradeModal(<?= json_encode($g) ?>)'>
                            Edit
                        </button>

                        <form method="POST" action="" style="display:inline;"
                            onsubmit="return confirm('Are you sure you want to delete this grade record?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="grade_id" value="<?= htmlspecialchars($g['id']) ?>">
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

    <div id="editGradeModal" class="custom-modal">
        <div class="custom-modal-content">
            <div class="custom-modal-header">
                <div class="form-card-title">Edit Grade Record</div>
                <button type="button" class="custom-modal-close" onclick="closeEditGradeModal()">&times;</button>
            </div>

            <form method="POST" action="">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" id="edit_grade_id" name="grade_id">

                <div class="form-grid">
                    <div class="form-group" style="grid-column: span 2;">
                        <label for="edit_subject">Subject Name</label>

                        <?php if (count($subjects) > 0): ?>
                        <select id="edit_subject" name="subject" required>
                            <option value="">— Select Subject —</option>
                            <?php foreach ($subjects as $subject): ?>
                            <option value="<?= htmlspecialchars($subject['name']) ?>">
                                <?= htmlspecialchars($subject['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <?php else: ?>
                        <input type="text" id="edit_subject" name="subject" required>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="edit_prelim">Prelim Score</label>
                        <input type="number" id="edit_prelim" name="prelim" min="0" max="100" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_midterm">Midterm Score</label>
                        <input type="number" id="edit_midterm" name="midterm" min="0" max="100" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_final">Final Exam Score</label>
                        <input type="number" id="edit_final" name="final" min="0" max="100" required>
                    </div>
                </div>

                <button type="submit" class="btn-submit"><i class="bi bi-pencil-square"></i> Update Grade
                    Record</button>
            </form>
        </div>
    </div>
</main>

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
function openEditGradeModal(grade) {
    document.getElementById('edit_grade_id').value = grade.id;
    document.getElementById('edit_subject').value = grade.subject;
    document.getElementById('edit_prelim').value = grade.prelim;
    document.getElementById('edit_midterm').value = grade.midterm;
    document.getElementById('edit_final').value = grade.final;

    document.getElementById('editGradeModal').style.display = 'flex';
}

function closeEditGradeModal() {
    document.getElementById('editGradeModal').style.display = 'none';
}

window.addEventListener('click', function(event) {
    const modal = document.getElementById('editGradeModal');

    if (event.target === modal) {
        closeEditGradeModal();
    }
});
</script>

<?php include 'footer.php'; ?>