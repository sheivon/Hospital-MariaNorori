<?php

require_once __DIR__ . '/../app/bootstrap.php';

use App\Models\PatientModel;

function get_patients() {
    return (new PatientModel())->all();
}

function get_patient($id) {
    return (new PatientModel())->find((int)$id);
}

function create_patient($data) {
    return (new PatientModel())->create((array)$data);
}

function update_patient($id, $data) {
    return (new PatientModel())->update((int)$id, (array)$data);
}

function delete_patient($id) {
    return (new PatientModel())->delete((int)$id);
}
