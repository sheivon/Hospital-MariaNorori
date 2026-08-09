<?php
// scripts/create_tables.php
// Script to automatically create and update hospital tables

require_once __DIR__ . '/../config/db.php';

$tables = [
    'Paciente' => [
        'Cedula VARCHAR(20) PRIMARY KEY',
        'Nombre VARCHAR(100)',
        'Apellidos VARCHAR(100)',
        'Sexo VARCHAR(10)',
        'FechaNacimiento DATE',
        'Direccion VARCHAR(255)',
        'Telefono VARCHAR(20)',
        'NoINSS VARCHAR(20)',
        'EstadoCivil VARCHAR(50)',
        'Escolaridad VARCHAR(50)',
        'NombrePadre VARCHAR(100)',
        'NombreMadre VARCHAR(100)',
        'Ocupacion VARCHAR(100)',
        'Empleador VARCHAR(100)',
        'NoExpediente VARCHAR(20)',
        'Procedencia VARCHAR(100)',
        'Edad INT'
    ],
    'Diagnostico' => [
        'ID INT AUTO_INCREMENT PRIMARY KEY',
        'NombreUnidad VARCHAR(100)',
        'Nombres VARCHAR(100)',
        'Apellidos VARCHAR(100)',
        'Sala VARCHAR(50)',
        'NoExpediente VARCHAR(20)',
        'NoCedula VARCHAR(20)',
        'NoINSS VARCHAR(20)',
        'Fecha DATE',
        'Hora TIME',
        'Planes TEXT',
        'Peso DECIMAL(5,2)',
        'Talla DECIMAL(5,2)',
        'Edad INT',
        'Sexo VARCHAR(10)'
    ],
    'Medico' => [
        'IDMedico INT AUTO_INCREMENT PRIMARY KEY',
        'Nombre VARCHAR(100)',
        'Especialidad VARCHAR(100)',
        'IDDepartamento INT'
    ],
    'Consulta' => [
        'IDConsulta INT AUTO_INCREMENT PRIMARY KEY',
        'Fecha DATE',
        'IDPaciente VARCHAR(20)',
        'IDMedico INT',
        'TipoConsulta VARCHAR(50)'
    ],
    'Tratamiento' => [
        'IDConsulta INT',
        'Descripcion TEXT',
        'Medicamentos TEXT',
        'Examenes TEXT',
        'Alimentacion TEXT',
        'Normas TEXT',
        'Seguimiento TEXT',
        'Traslado TEXT'
    ],
    'IngresosHospitalarios' => [
        'IDIngreso INT AUTO_INCREMENT PRIMARY KEY',
        'FechaIngreso DATE',
        'FechaAlta DATE',
        'IDDepartamento INT',
        'IDPaciente VARCHAR(20)',
        'DiagnosticoIngreso TEXT',
        'HoraIngreso TIME'
    ],
    'HistorialClinicas' => [
        'IDHistorial INT AUTO_INCREMENT PRIMARY KEY',
        'Nombres VARCHAR(100)',
        'PrimerApellido VARCHAR(100)',
        'SegundoApellido VARCHAR(100)',
        'Direccion VARCHAR(255)',
        'NoINSS VARCHAR(20)',
        'SILAS VARCHAR(50)',
        'Municipio VARCHAR(100)',
        'UnidadSalud VARCHAR(100)',
        'NoExpediente VARCHAR(20)'
    ],
    'Medicamentos' => [
        'IDMedicamento INT AUTO_INCREMENT PRIMARY KEY',
        'Nombres VARCHAR(100)',
        'Apellidos VARCHAR(100)',
        'Fecha DATE',
        'Hora TIME',
        'Sala VARCHAR(50)',
        'NombreUnidadSalud VARCHAR(100)',
        'NoExpediente VARCHAR(20)',
        'NoCedula VARCHAR(20)',
        'NoCama VARCHAR(20)',
        'NombreMedicamentos VARCHAR(100)',
        'NombreEnfermera VARCHAR(100)',
        'Codigo VARCHAR(50)'
    ],
    'Examenes' => [
        'IDExamen INT AUTO_INCREMENT PRIMARY KEY',
        'Unidad VARCHAR(100)',
        'Nombres VARCHAR(100)',
        'Apellidos VARCHAR(100)',
        'Edad INT',
        'Sexo VARCHAR(10)',
        'Fecha DATE',
        'NoExpediente VARCHAR(20)',
        'Localidad VARCHAR(100)',
        'Codigo VARCHAR(50)',
        'NoCama VARCHAR(20)',
        'Servicio VARCHAR(100)'
    ],
    'ExamRequests' => [
        'ID INT AUTO_INCREMENT PRIMARY KEY',
        'PatientID INT NOT NULL',
        'RequestDate DATE NOT NULL',
        'ExamType VARCHAR(255) NOT NULL',
        'Notes TEXT',
        'Status VARCHAR(50) DEFAULT "pending"',
        'CreatedBy INT',
        'CreatedAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP'
    ],
    'radiology_requests' => [
        'id INT AUTO_INCREMENT PRIMARY KEY',
        'patient_id INT NOT NULL',
        'unit VARCHAR(120)',
        'first_last_name VARCHAR(100)',
        'second_last_name VARCHAR(100)',
        'names VARCHAR(200)',
        'insured VARCHAR(10)',
        'gender VARCHAR(10)',
        'age VARCHAR(20)',
        'request_date DATE',
        'clinic_bed VARCHAR(100)',
        'service VARCHAR(100)',
        'code VARCHAR(50)',
        'prior_radiograph VARCHAR(50)',
        'prior_radiograph_code VARCHAR(50)',
        'exam_requested TEXT',
        'clinical_data TEXT',
        'evolution_time VARCHAR(100)',
        'presumptive_diagnosis TEXT',
        'observations TEXT',
        'doctor_code VARCHAR(100)',
        'technician VARCHAR(100)',
        'plates_used VARCHAR(50)',
        'findings TEXT',
        'conclusions TEXT',
        'radiology_date DATE',
        'radiographs_archived VARCHAR(100)',
        'radiograph_count VARCHAR(50)',
        'dictating_doctor_code VARCHAR(100)',
        'status VARCHAR(50) DEFAULT "pending"',
        'created_by INT',
        'created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP'
    ],
    'emergency_encounters' => [
        'id INT AUTO_INCREMENT PRIMARY KEY',
        'patient_id INT NOT NULL',
        'encounter_id INT DEFAULT NULL',
        'admission_date DATE NOT NULL',
        'discharge_date DATE DEFAULT NULL',
        'status VARCHAR(50) DEFAULT "Activo"',
        'form_data TEXT',
        'created_by INT',
        'created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP'
    ],
    'appointments' => [
        'id INT AUTO_INCREMENT PRIMARY KEY',
        'patient_id INT',
        'provider_user_id INT',
        'encounter_id INT DEFAULT NULL',
        'appointment_at DATETIME',
        'reason VARCHAR(255)',
        'status VARCHAR(50)',
        'notes TEXT',
        'created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP'
    ],
    'Anos' => [
        'IDAnos INT AUTO_INCREMENT PRIMARY KEY'
    ]
];

function createOrUpdateTable($pdo, $table, $fields) {
    $fieldsSql = implode(", ", $fields);
    $sql = "CREATE TABLE IF NOT EXISTS $table ($fieldsSql)";
    $pdo->exec($sql);
    echo "Table '$table' created or updated.\n";
}

try {
    $pdo = new PDO($dsn, $db_user, $db_pass);
    foreach ($tables as $table => $fields) {
        createOrUpdateTable($pdo, $table, $fields);
    }
    echo "All tables created or updated successfully.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
