<?php

// Delete one owner; the schema safely cascades deletion to that owner's pets.
function deleteOwner(PDO $pdo, $ownerId): void
{
    $id = validateRecordId($ownerId, 'owner');
    $stmt = $pdo->prepare('DELETE FROM pet_owners WHERE owner_id = :owner_id');
    $stmt->execute(['owner_id' => $id]);

    if ($stmt->rowCount() === 0) {
        throw new OutOfBoundsException('Owner not found.');
    }
}

// Delete one pet using a prepared statement.
function deletePet(PDO $pdo, $petId): void
{
    $id = validateRecordId($petId, 'pet');
    $stmt = $pdo->prepare('DELETE FROM pets WHERE pet_id = :pet_id');
    $stmt->execute(['pet_id' => $id]);

    if ($stmt->rowCount() === 0) {
        throw new OutOfBoundsException('Pet not found.');
    }
}
