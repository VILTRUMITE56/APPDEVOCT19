<?php
require_once __DIR__ . '/add.php';

// Update an existing owner after validating the submitted fields and ID.
function editOwner(PDO $pdo, $ownerId, $ownerName, $phone, $email): void
{
    $id = validateRecordId($ownerId, 'owner');
    [$name, $phoneValue, $emailValue] = salonOwnerInput($ownerName, $phone, $email);

    $stmt = $pdo->prepare(
        'UPDATE pet_owners SET owner_name = :owner_name, phone = :phone, email = :email WHERE owner_id = :owner_id'
    );
    $stmt->execute([
        'owner_name' => $name,
        'phone' => $phoneValue,
        'email' => $emailValue,
        'owner_id' => $id,
    ]);

    if ($stmt->rowCount() === 0) {
        $check = $pdo->prepare('SELECT 1 FROM pet_owners WHERE owner_id = :owner_id');
        $check->execute(['owner_id' => $id]);
        if ($check->fetchColumn() === false) {
            throw new OutOfBoundsException('Owner not found.');
        }
    }
}

// Update a pet and its owner link after validating every field.
function editPet(PDO $pdo, $petId, $petName, $species, $breed, $groomingDate, $status, $ownerId): void
{
    $id = validateRecordId($petId, 'pet');
    [$name, $speciesValue, $breedValue, $dateValue, $statusValue, $ownerIdValue] =
        salonPetInput($pdo, $petName, $species, $breed, $groomingDate, $status, $ownerId);

    $stmt = $pdo->prepare(
        'UPDATE pets SET pet_name = :pet_name, species = :species, breed = :breed, '
        . 'grooming_date = :grooming_date, status = :status, owner_id = :owner_id WHERE pet_id = :pet_id'
    );
    $stmt->execute([
        'pet_name' => $name,
        'species' => $speciesValue,
        'breed' => $breedValue,
        'grooming_date' => $dateValue,
        'status' => $statusValue,
        'owner_id' => $ownerIdValue,
        'pet_id' => $id,
    ]);

    if ($stmt->rowCount() === 0) {
        $check = $pdo->prepare('SELECT 1 FROM pets WHERE pet_id = :pet_id');
        $check->execute(['pet_id' => $id]);
        if ($check->fetchColumn() === false) {
            throw new OutOfBoundsException('Pet not found.');
        }
    }
}
