<?php
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Auth;

Auth::requireLogin();
include __DIR__ . '/../templates/header.php';
?>

<div class="container mt-4">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<div>
			<h2 data-i18n="Citas">Citas</h2>
		</div>
        <button type="button" class="btn btn-success" id="diagCrudModalCitas" data-bs-toggle="modal" data-bs-target="#diagCrudModalCitas">
            <i class="fa-solid fa-plus me-1"></i><span data-i18n="citas_add_btn">Add Citas</span>
        </button>
	</div>
<div id="appointmentListAlert" class="alert alert-danger d-none" role="alert"></div>

<div class="table-responsive">
    <table id="appointmentsTable" class="table table-bordered table-striped align-middle">
        <thead>
            <tr>
                <th style="width:5%;" data-i18n="table_index">#</th>
                <th data-i18n="appointment_table_patient">Patient</th>
                <th data-i18n="appointment_table_provider">Provider</th>
                <th data-i18n="appointment_table_datetime">Date & Time</th>
                <th data-i18n="appointment_table_reason">Reason</th>
                <th data-i18n="appointment_table_status">Status</th>
                <th style="width:12%;" data-i18n="actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="7" class="text-center" data-i18n="loading">
                    Loading...
                </td>
            </tr>
        </tbody>
    </table>
</div>le>
	</div>
</div>




<?php include __DIR__ . '/modal/citas_modal.php'; ?>

<?php include __DIR__ . '/../templates/footer.php'; ?>