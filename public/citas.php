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
		<div class="d-flex gap-2">
			<a href="/solicitud_de_examen.php" class="btn btn-success"><i class="fa-solid fa-plus me-1"></i><span data-i18n="cita_request_button">Solicitud de examen</span></a>
		</div>
	</div>

	<div id="examListAlert" class="alert alert-danger d-none" role="alert"></div>

	<div class="table-responsive">
		<table id="examsTable" class="table table-bordered table-striped align-middle">
			<thead>
			<tr>
				<th style="width:10%;" data-i18n="table_index">#</th>
				<th data-i18n="exam_table_patient">Paciente</th>
				<th data-i18n="exam_table_cedula">Cédula</th>
				<th data-i18n="exam_table_date">Fecha</th>
				<th data-i18n="exam_table_type">Examen</th>
				<th data-i18n="exam_table_notes">Notas</th>
				<th data-i18n="exam_table_status">Estado</th>
				<th data-i18n="actions">Actions</th>
			</tr>
			</thead>
			<tbody>
			<tr><td colspan="7" class="text-center">Cargando...</td></tr>
			</tbody>
		</table>
	</div>
</div>
