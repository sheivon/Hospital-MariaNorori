<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Controllers\SetupController;

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $message = SetupController::handle($_POST);
}

include __DIR__ . '/../templates/header.php';
?>
<div class="container mt-4">
    <h3>Database Setup</h3>
    <p class="text-warning">This page is for initial setup only. Remove or protect it after use!</p>
    <p class="text-muted">Schema source: <code>migrations/init.sql</code> (single baseline migration).</p>

    <?php if ($message): ?>
        <div class="alert alert-info">
            <pre><?php echo htmlspecialchars($message); ?></pre>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <h5>Create Database</h5>
            <p>Creates the configured database if it does not exist.</p>
            <form method="post">
                <button type="submit" name="create_db" class="btn btn-primary">Create Database</button>
            </form>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <h5>Create Tables</h5>
            <p>Executes the full baseline schema from <code>migrations/init.sql</code>.</p>
            <form method="post" class="d-inline">
                <button type="submit" name="create_tables" class="btn btn-success">Create Tables</button>
            </form>
            <form method="post" class="d-inline ms-2">
                <button type="submit" name="drop_tables" class="btn btn-danger" onclick="return confirm('Are you sure you want to drop all tables? This will delete all data!')">Drop Tables</button>
            </form>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <h5>Create Default Users</h5>
            <p>Creates default admin and doctor users. Requires database and tables.</p>
            <form method="post">
                <button type="submit" name="create_users" class="btn btn-warning">Create Default Users</button>
            </form>
        </div>
    </div>

    <div class="card mt-3 mb-4">
        <div class="card-body">
            <h5>Initialize All (recommended)</h5>
            <p>Creates database, applies full schema, and seeds default users.</p>
            <form method="post">
                <button type="submit" name="initialize_db" class="btn btn-dark">Initialize Database</button>
            </form>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../templates/footer.php'; ?>