<?php

// Return all owners in display order.
function viewOwners(PDO $pdo): array
{
    $stmt = $pdo->query('SELECT owner_id, owner_name, phone, email FROM pet_owners ORDER BY owner_id');
    return $stmt->fetchAll();
}

// Find an owner for an edit form; return null for a well-formed missing ID.
function getOwner(PDO $pdo, $ownerId): ?array
{
    $id = validateRecordId($ownerId, 'owner');
    $stmt = $pdo->prepare('SELECT owner_id, owner_name, phone, email FROM pet_owners WHERE owner_id = :owner_id');
    $stmt->execute(['owner_id' => $id]);
    $owner = $stmt->fetch();
    return $owner === false ? null : $owner;
}

// Return pets with their associated owner names for the management page.
function viewPets(PDO $pdo): array
{
    $stmt = $pdo->query(
        'SELECT p.pet_id, p.pet_name, p.species, p.breed, p.grooming_date, p.status, '
        . 'p.owner_id, o.owner_name FROM pets p '
        . 'INNER JOIN pet_owners o ON o.owner_id = p.owner_id ORDER BY p.pet_id'
    );
    return $stmt->fetchAll();
}

// Find a pet for an edit form; return null for a well-formed missing ID.
function getPet(PDO $pdo, $petId): ?array
{
    $id = validateRecordId($petId, 'pet');
    $stmt = $pdo->prepare(
        'SELECT pet_id, pet_name, species, breed, grooming_date, status, owner_id FROM pets WHERE pet_id = :pet_id'
    );
    $stmt->execute(['pet_id' => $id]);
    $pet = $stmt->fetch();
    return $pet === false ? null : $pet;
}

// Retrieve owners and pets together while retaining owners with no pets.
function viewOwnersWithPets(PDO $pdo): array
{
    $stmt = $pdo->query(
        'SELECT o.owner_id, o.owner_name, o.phone, o.email, '
        . 'p.pet_id, p.pet_name, p.species, p.breed, p.grooming_date, p.status '
        . 'FROM pet_owners o LEFT JOIN pets p ON p.owner_id = o.owner_id '
        . 'ORDER BY o.owner_id, p.pet_id'
    );

    $owners = [];
    while ($row = $stmt->fetch()) {
        $ownerId = (int) $row['owner_id'];
        if (!isset($owners[$ownerId])) {
            $owners[$ownerId] = [
                'owner_id' => $ownerId,
                'owner_name' => $row['owner_name'],
                'phone' => $row['phone'],
                'email' => $row['email'],
                'pets' => [],
            ];
        }

        if ($row['pet_id'] !== null) {
            $owners[$ownerId]['pets'][] = [
                'pet_id' => $row['pet_id'],
                'pet_name' => $row['pet_name'],
                'species' => $row['species'],
                'breed' => $row['breed'],
                'grooming_date' => $row['grooming_date'],
                'status' => $row['status'],
            ];
        }
    }

    return array_values($owners);
}
