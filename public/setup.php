<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Controllers\SetupController;

$message = '';
$setupRepository = new \App\Repositories\SetupRepository();
$config = $setupRepository->loadConfig();

// Persist posted values in the form (so users can test without losing inputs)
$posted = array_merge($config, $_POST);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = SetupController::handle($_POST);
}

include __DIR__ . '/../templates/header.php';
?>
<?php
    $mysqlExtensionsLoaded = extension_loaded('pdo_mysql') || extension_loaded('mysqli');
    ?>
    <div class="container mt-4">
        <h3>Database Setup</h3>
        <p class="text-warning">This page is for initial setup only. Remove or protect it after use!</p>
        <p class="text-muted">Schema source: <code>migrations/init.sql</code> (single baseline migration).</p>

        <?php if (!$mysqlExtensionsLoaded): ?>
            <div class="card border-danger mb-3">
                <div class="card-body">
                    <h5 class="card-title text-danger">MySQL extension not available</h5>
                    <p class="card-text">Your PHP installation is missing the MySQL driver (PDO MySQL or mysqli). Without it, this application cannot connect to the database.</p>
                    <p class="card-text"><strong>Next steps:</strong></p>
                    <ul>
                        <li>Install MySQL / MariaDB on your machine or server.</li>
                        <li>Enable the <code>pdo_mysql</code> or <code>mysqli</code> PHP extension.</li>
                    </ul>
                    <a class="btn btn-danger" href="https://dev.mysql.com/downloads/mysql/" target="_blank" rel="noopener noreferrer">Download MySQL</a>
                    <a class="btn btn-secondary ms-2" href="https://mariadb.org/download/" target="_blank" rel="noopener noreferrer">Download MariaDB</a>
                </div>
            </div>
        <?php endif; ?>

    <?php if ($message): ?>
        <div class="alert alert-info">
            <pre><?php echo htmlspecialchars($message); ?></pre>
        </div>
    <?php endif; ?>

    <div id="setupAlert" class="alert d-none" role="status"></div>

    <div id="setupOverlay" class="d-none" style="position:fixed;inset:0;background:rgba(255,255,255,0.82);z-index:1050;display:flex;align-items:center;justify-content:center;">
        <div class="text-center p-4 rounded shadow bg-white border">
            <div class="spinner-border text-primary" role="status" aria-hidden="true"></div>
            <div id="setupOverlayText" class="mt-3">Please wait while the action completes...</div>
        </div>
    </div>

    <form method="post" id="setupForm">
        <div class="card mb-3">
            <div class="card-body">
                <h5>Database Connection</h5>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Host</label>
                        <input name="db_host" class="form-control" value="<?php echo htmlspecialchars($posted['DB_HOST'] ?? ''); ?>">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Port</label>
                        <input name="db_port" class="form-control" value="<?php echo htmlspecialchars($posted['DB_PORT'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Database</label>
                        <input name="db_name" class="form-control" value="<?php echo htmlspecialchars($posted['DB_NAME'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">User</label>
                        <input name="db_user" class="form-control" value="<?php echo htmlspecialchars($posted['DB_USER'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password</label>
                        <input name="db_pass" type="password" class="form-control" value="<?php echo htmlspecialchars($posted['DB_PASS'] ?? ''); ?>">
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" name="test_connection" class="btn btn-outline-primary">Test connection</button>
                    <button type="submit" name="save_config" class="btn btn-outline-success">Save to .env</button>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5>Create Database</h5>
                <p>Creates the configured database if it does not exist.</p>
                <button type="submit" name="create_db" class="btn btn-primary">Create Database</button>
            </div>
        </div>

    <div class="card mt-3">
        <div class="card-body">
            <h5>Create Tables</h5>
            <p>Executes the full baseline schema from <code>migrations/init.sql</code>.</p>
            <button type="submit" name="create_tables" class="btn btn-success">Create Tables</button>
            <button type="submit" name="drop_tables" class="btn btn-danger ms-2" onclick="return confirm('Are you sure you want to drop all tables? This will delete all data!')">Drop Tables</button>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <h5>Create Default Users</h5>
            <p>Creates default admin and doctor users. Requires database and tables.</p>
            <button type="submit" name="create_users" class="btn btn-warning">Create Default Users</button>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <h5>Insert Sample Data</h5>
            <p>Inserts demo sample patients + diagnostics and additional test rows. Only runs when no patient data exists.</p>
            <button type="submit" name="seed_sample_data" class="btn btn-info">Seed Sample Data</button>
        </div>
    </div>

    <div class="card mt-3 mb-4">
        <div class="card-body">
            <h5>Initialize All (recommended)</h5>
            <p>Creates database, applies full schema, and seeds default users.</p>
            <button type="submit" name="initialize_db" class="btn btn-dark">Initialize Database</button>
        </div>
    </div>
</form>
</div>
<script>
(function() {
    const form = document.getElementById('setupForm');
    const alertBox = document.getElementById('setupAlert');
    const overlay = document.getElementById('setupOverlay');
    const overlayText = document.getElementById('setupOverlayText');
    const buttons = Array.from(form.querySelectorAll('button[type="submit"]'));
    let lastSubmitName = null;

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function setAlert(type, content) {
        alertBox.className = `alert alert-${type}`;
        alertBox.innerHTML = `<pre class="mb-0">${escapeHtml(content)}</pre>`;
        alertBox.classList.remove('d-none');
    }

    function clearAlert() {
        alertBox.classList.add('d-none');
        alertBox.innerHTML = '';
    }

    function setLoading(active, message) {
        if (active) {
            overlayText.textContent = message || 'Please wait while the action completes...';
            overlay.classList.remove('d-none');
            buttons.forEach(btn => btn.disabled = true);
        } else {
            overlay.classList.add('d-none');
            buttons.forEach(btn => btn.disabled = false);
        }
    }

    form.addEventListener('click', function(event) {
        const button = event.target.closest('button[type="submit"]');
        if (button) {
            lastSubmitName = button.name;
        }
    });

    form.addEventListener('submit', async function(event) {
        const submitter = event.submitter || (event.target && event.target.querySelector('button[type="submit"][name]'));
        const actionName = submitter && submitter.name ? submitter.name : lastSubmitName;
        if (!actionName) {
            return;
        }

        event.preventDefault();
        clearAlert();
        setLoading(true, `Running ${actionName.replace(/_/g, ' ')}...`);

        const formData = new FormData(form);
        formData.set(actionName, '1');

        const payload = {};
        formData.forEach((value, key) => {
            payload[key] = value;
        });

        try {
            const response = await fetch('/api/setup_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });

            const json = await response.json();
            if (!response.ok) {
                throw new Error(json.error || json.message || 'Server error');
            }

            if (json.success) {
                setAlert('success', json.messages.join('\n'));
            } else {
                setAlert('danger', json.messages ? json.messages.join('\n') : (json.error || 'Operation failed.'));
            }
        } catch (error) {
            setAlert('danger', error.message || 'Request failed');
        } finally {
            setLoading(false);
        }
    });
})();
</script>
<?php include __DIR__ . '/../templates/footer.php'; ?>