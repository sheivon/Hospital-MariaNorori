<?php

namespace App\Domain;

class Patient
{
    public function __construct(
        public ?int $id = null,
        public ?string $firstName = null,
        public ?string $lastName = null,
        public ?string $email = null,
        public ?string $cedula = null,
        public ?string $dob = null,
        public ?string $gender = null,
        public ?string $phone = null,
        public ?string $address = null,
        public ?string $maritalStatus = null,
        public ?string $insuranceProvider = null,
        public ?string $insurancePolicyNo = null,
        public ?string $fatherName = null,
        public ?string $motherName = null,
        public ?string $expedienteNo = null,
        public ?string $procedencia = null,
        public ?string $educationLevel = null,
        public ?string $employer = null,
        public ?string $notes = null
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'cedula' => $this->cedula,
            'dob' => $this->dob,
            'gender' => $this->gender,
            'phone' => $this->phone,
            'address' => $this->address,
            'marital_status' => $this->maritalStatus,
            'insurance_provider' => $this->insuranceProvider,
            'insurance_policy_no' => $this->insurancePolicyNo,
            'father_name' => $this->fatherName,
            'mother_name' => $this->motherName,
            'expediente_no' => $this->expedienteNo,
            'procedencia' => $this->procedencia,
            'education_level' => $this->educationLevel,
            'employer' => $this->employer,
            'notes' => $this->notes,
        ];
    }
}
