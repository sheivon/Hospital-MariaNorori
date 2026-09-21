<?php $GLOBALS['PAGE_SCRIPTS'][] = 'datatables'; $GLOBALS['PAGE_SCRIPTS'][] = 'Appointments'; ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-0"><i class="fa-solid fa-calendar-check me-2"></i><span data-i18n="Appointments">Appointments</span></h2>
            <p class="text-muted mb-0" data-i18n="appointments_description">Manage patient appointments.</p>
        </div>
        <button type="button" class="btn btn-success" id="btnOpenAppointmentModal" data-bs-toggle="modal" data-bs-target="#appointmentCrudModal">
            <i class="fa-solid fa-plus me-1"></i><span data-i18n="appointment_add_btn">Add Appointment</span>
        </button>
    </div>

    <div id="appointmentListAlert" class="alert alert-danger d-none" role="alert"></div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="appointmentsTable" class="table table-bordered table-striped align-middle w-100 dt-container">
            <thead>
                <tr>
                    <th style="width:5%;" data-i18n="table_index">#</th>
                    <th data-i18n="appointment_table_patient">Patient</th>
                    <th data-i18n="appointment_table_provider">Provider</th>
                    <th data-i18n="appointment_table_datetime">Date &amp; Time</th>
                    <th data-i18n="appointment_table_reason">Reason</th>
                    <th data-i18n="appointment_table_status">Status</th>
                    <th style="width:12%;" data-i18n="actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr class="appointments-loading-row">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td class="text-center" data-i18n="loading">Loading...</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include APP_ROOT . '/app/Views/Shared/Modals/appointments_modal.php'; ?>

