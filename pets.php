<?php
require "db.php";
require "functions/add.php";
require "functions/edit.php";
require "functions/delete.php";
require "functions/view.php";

if (isset($_POST['add_pet'])) {
    addPet($pdo, $_POST['pet_name'], $_POST['species'], $_POST['breed'], $_POST['grooming_date'], $_POST['status'], $_POST['owner_id']);
    header("Location: pets.php");
    exit;
}

if (isset($_POST['update_pet'])) {
    editPet($pdo, $_POST['pet_id'], $_POST['pet_name'], $_POST['species'], $_POST['breed'], $_POST['grooming_date'], $_POST['status'], $_POST['owner_id']);
    header("Location: pets.php");
    exit;
}

if (isset($_GET['delete'])) {
    deletePet($pdo, $_GET['delete']);
    header("Location: pets.php");
    exit;
}

$pets = viewPets($pdo);
$owners = viewOwners($pdo);

$editPet = null;
if (isset($_GET['edit'])) {
    $editPet = getPet($pdo, $_GET['edit']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pets</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Pets (Child)</h1>
    <div class="nav"><a href="index.php">Home</a> | <a href="owners.php">Owners</a></div>

    <form method="POST" action="pets.php">
        <h2><?php echo $editPet ? "Edit Pet" : "Add New Pet"; ?></h2>
        <?php if ($editPet): ?>
            <input type="hidden" name="pet_id" value="<?php echo $editPet['pet_id']; ?>">
        <?php endif; ?>
        <label>Pet Name:</label>
        <input type="text" name="pet_name" required value="<?php echo $editPet['pet_name'] ?? ''; ?>">
        <label>Species:</label>
        <input type="text" name="species" required value="<?php echo $editPet['species'] ?? ''; ?>">
        <label>Breed:</label>
        <input type="text" name="breed" value="<?php echo $editPet['breed'] ?? ''; ?>">
        <label>Grooming Date:</label>
        <input type="date" name="grooming_date" value="<?php echo $editPet['grooming_date'] ?? ''; ?>">
        <label>Status:</label>
        <select name="status">
            <?php $statuses = ['Pending','In Progress','Completed','Cancelled']; ?>
            <?php foreach ($statuses as $s): ?>
                <option value="<?php echo $s; ?>" <?php echo (isset($editPet['status']) && $editPet['status'] == $s) ? 'selected' : ''; ?>><?php echo $s; ?></option>
            <?php endforeach; ?>
        </select>
        <label>Owner (Parent):</label>
        <select name="owner_id" required>
            <option value="">-- Select Owner --</option>
            <?php foreach ($owners as $o): ?>
                <option value="<?php echo $o['owner_id']; ?>" <?php echo (isset($editPet['owner_id']) && $editPet['owner_id'] == $o['owner_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($o['owner_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if ($editPet): ?>
            <button type="submit" name="update_pet">Update Pet</button>
        <?php else: ?>
            <button type="submit" name="add_pet">Add Pet</button>
        <?php endif; ?>
    </form>

    <table>
        <tr><th>ID</th><th>Pet Name</th><th>Species</th><th>Breed</th><th>Grooming Date</th><th>Status</th><th>Owner</th><th>Actions</th></tr>
        <?php foreach ($pets as $p): ?>
        <tr>
            <td><?php echo $p['pet_id']; ?></td>
            <td><?php echo htmlspecialchars($p['pet_name']); ?></td>
            <td><?php echo htmlspecialchars($p['species']); ?></td>
            <td><?php echo htmlspecialchars($p['breed']); ?></td>
            <td><?php echo htmlspecialchars($p['grooming_date'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($p['status'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($p['owner_name']); ?></td>
            <td>
                <a href="pets.php?edit=<?php echo $p['pet_id']; ?>">Edit</a> |
                <a href="pets.php?delete=<?php echo $p['pet_id']; ?>" onclick="return confirm('Delete this pet?');">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>