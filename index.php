<?php
include 'dbcon.php';

$alert = '';
$edit_user = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST['edit_id'])) {
    $name  = $_POST['name'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $email);
    $stmt->execute();
    $stmt->close();

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['edit_id'])) {
    $id    = $_POST['edit_id'];
    $name  = $_POST['name'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $email, $id);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Updated successfully!'); window.location='index.php?status=updated';</script>";
    exit;

} elseif (isset($_GET['status'])) {
    if ($_GET['status'] === 'updated') {
        $alert = '<div class="alert alert-success alert-dismissible fade show">User updated successfully!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    } elseif ($_GET['status'] === 'deleted') {
        $alert = '<div class="alert alert-success alert-dismissible fade show">User deleted successfully!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }
}

if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $_GET['edit']);
    $stmt->execute();
    $edit_user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$result = $conn->query("SELECT * FROM users ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <h4 class="text-center mb-4">User Management System</h4>

    <div class="row">
        <!-- Left: Form -->
        <div class="col-md-5">
            <div class="card p-4 shadow-sm">
                <h6 class="mb-3">Add New User</h6>
                <?= $alert ?>
                <form method="POST">
                    <input type="hidden" name="edit_id" value="<?= $edit_user ? $edit_user['id'] : '' ?>">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter Full Name"
                               value="<?= $edit_user ? htmlspecialchars($edit_user['name']) : '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter Email Address"
                               value="<?= $edit_user ? htmlspecialchars($edit_user['email']) : '' ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Submit</button>
                </form>
            </div>
        </div>

        <!-- Right: Table -->
        <div class="col-md-7">
            <div class="card p-4 shadow-sm">
                <h6 class="mb-3">Registered Users</h6>
                <table class="table table-bordered table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Joined</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows === 0): ?>
                        <tr><td colspan="5" class="text-center text-muted">No data found</td></tr>
                        <?php else: ?>
                        <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                            <td>
                                <a href="index.php?edit=<?= $row['id'] ?>" class="btn btn-success btn-sm">Edit</a>
                                <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                                   onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
