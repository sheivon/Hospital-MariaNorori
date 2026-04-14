<?php

require_once __DIR__ . '/../app/bootstrap.php';

use App\Helpers\PatientHelper;

function get_patients() {
    return PatientHelper::getPatients();
}

function get_patient($id) {
    return PatientHelper::getPatient((int)$id);
}

function create_patient($data) {
    return PatientHelper::createPatient((array)$data);
}

function update_patient($id, $data) {
    return PatientHelper::updatePatient((int)$id, (array)$data);
}

function delete_patient($id) {
    return PatientHelper::deletePatient((int)$id);
}
