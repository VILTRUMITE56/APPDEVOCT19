<?php

// Validate a required or optional text field and enforce its schema length.
function salonTextField($value, string $label, int $maxLength, bool $required = true): ?string
{
    $text = is_string($value) ? trim($value) : '';
    if ($required && $text === '') {
        throw new InvalidArgumentException($label . ' is required.');
    }

    if ($text === '') {
        return null;
    }

    $length = function_exists('mb_strlen') ? mb_strlen($text, 'UTF-8') : preg_match_all('/./us', $text);
    if ($length === false || $length > $maxLength) {
        throw new InvalidArgumentException($label . ' must be no more than ' . $maxLength . ' characters.');
    }

    return $text;
}

// Normalize owner data once so inserts and updates follow the same rules.
function salonOwnerInput($ownerName, $phone, $email): array
{
    $name = salonTextField($ownerName, 'Owner name', 100);
    $phoneValue = salonTextField($phone, 'Phone', 20);
    $emailValue = salonTextField($email, 'Email', 100, false);

    if ($emailValue !== null && filter_var($emailValue, FILTER_VALIDATE_EMAIL) === false) {
        throw new InvalidArgumentException('Enter a valid email address.');
    }

    return [$name, $phoneValue, $emailValue];
}

// Validate pet fields and require a real owner for the one-to-many relationship.
function salonPetInput(PDO $pdo, $petName, $species, $breed, $groomingDate, $status, $ownerId): array
{
    $name = salonTextField($petName, 'Pet name', 100);
    $speciesValue = salonTextField($species, 'Species', 50);
    $breedValue = salonTextField($breed, 'Breed', 50, false);
    $dateValue = salonTextField($groomingDate, 'Grooming date', 10, false);
    $statusValue = salonTextField($status, 'Status', 50);
    $validStatuses = ['Pending', 'In Progress', 'Completed', 'Cancelled'];

    if (!in_array($statusValue, $validStatuses, true)) {
        throw new InvalidArgumentException('Select a valid grooming status.');
    }

    if ($dateValue !== null) {
        $date = DateTime::createFromFormat('!Y-m-d', $dateValue);
        if ($date === false || $date->format('Y-m-d') !== $dateValue) {
            throw new InvalidArgumentException('Enter a valid grooming date.');
        }
    }

    $ownerIdValue = validateRecordId($ownerId, 'owner');
    $ownerCheck = $pdo->prepare('SELECT 1 FROM pet_owners WHERE owner_id = :owner_id');
    $ownerCheck->execute(['owner_id' => $ownerIdValue]);
    if ($ownerCheck->fetchColumn() === false) {
        throw new InvalidArgumentException('Select an existing owner for this pet.');
    }

    return [$name, $speciesValue, $breedValue, $dateValue, $statusValue, $ownerIdValue];
}

// Insert a pet owner using a prepared statement.
function addOwner(PDO $pdo, $ownerName, $phone, $email): void
{
    [$name, $phoneValue, $emailValue] = salonOwnerInput($ownerName, $phone, $email);
    $stmt = $pdo->prepare(
        'INSERT INTO pet_owners (owner_name, phone, email) VALUES (:owner_name, :phone, :email)'
    );
    $stmt->execute([
        'owner_name' => $name,
        'phone' => $phoneValue,
        'email' => $emailValue,
    ]);
}

// Insert a pet linked to a verified owner using a prepared statement.
function addPet(PDO $pdo, $petName, $species, $breed, $groomingDate, $status, $ownerId): void
{
    [$name, $speciesValue, $breedValue, $dateValue, $statusValue, $ownerIdValue] =
        salonPetInput($pdo, $petName, $species, $breed, $groomingDate, $status, $ownerId);
    $stmt = $pdo->prepare(
        'INSERT INTO pets (pet_name, species, breed, grooming_date, status, owner_id) '
        . 'VALUES (:pet_name, :species, :breed, :grooming_date, :status, :owner_id)'
    );
    $stmt->execute([
        'pet_name' => $name,
        'species' => $speciesValue,
        'breed' => $breedValue,
        'grooming_date' => $dateValue,
        'status' => $statusValue,
        'owner_id' => $ownerIdValue,
    ]);
}
