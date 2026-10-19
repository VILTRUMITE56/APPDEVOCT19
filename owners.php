<?php
require "db.php";
require "functions/add.php";
require "functions/edit.php";
require "functions/delete.php";
require "functions/view.php";

if (isset($_POST['add_owner'])) {
    addOwner($pdo, $_POST['owner_name'], $_POST['phone'], $_POST['email']);
    header("Location: owners.php");
    exit;
}

if (isset($_POST['update_owner'])) {
    editOwner($pdo, $_POST['owner_id'], $_POST['owner_name'], $_POST['phone'], $_POST['email']);
    header("Location: owners.php");
    exit;
}

if (isset($_GET['delete'])) {
    deleteOwner($pdo, $_GET['delete']);
    header("Location: owners.php");
    exit;
}

$owners = viewOwners($pdo);

$editOwner = null;
if (isset($_GET['edit'])) {
    $editOwner = getOwner($pdo, $_GET['edit']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pet Owners</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Pet Owners (Parent)</h1>
    <div class="nav"><a href="index.php">Home</a> | <a href="pets.php">Pets</a></div>

    <form method="POST" action="owners.php">
        <h2><?php echo $editOwner ? "Edit Owner" : "Add New Owner"; ?></h2>
        <?php if ($editOwner): ?>
            <input type="hidden" name="owner_id" value="<?php echo $editOwner['owner_id']; ?>">
        <?php endif; ?>
        <label>Owner Name:</label>
        <input type="text" name="owner_name" required value="<?php echo $editOwner['owner_name'] ?? ''; ?>">
        <label>Phone:</label>
        <input type="text" name="phone" required value="<?php echo $editOwner['phone'] ?? ''; ?>">
        <label>Email:</label>
        <input type="email" name="email" value="<?php echo $editOwner['email'] ?? ''; ?>">
        <?php if ($editOwner): ?>
            <button type="submit" name="update_owner">Update Owner</button>
        <?php else: ?>
            <button type="submit" name="add_owner">Add Owner</button>
        <?php endif; ?>
    </form>

    <table>
        <tr><th>ID</th><th>Name</th><th>Phone</th><th>Email</th><th>Actions</th></tr>
        <?php foreach ($owners as $o): ?>
        <tr>
            <td><?php echo $o['owner_id']; ?></td>
            <td><?php echo htmlspecialchars($o['owner_name']); ?></td>
            <td><?php echo htmlspecialchars($o['phone']); ?></td>
            <td><?php echo htmlspecialchars($o['email']); ?></td>
            <td>
                <a href="owners.php?edit=<?php echo $o['owner_id']; ?>">Edit</a> |
                <a href="owners.php?delete=<?php echo $o['owner_id']; ?>" onclick="return confirm('Delete this owner?');">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>