<?php
require "db.php";
require "functions/view.php";

$owners = viewOwnersWithPets($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Owners and Pets</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Owners and Their Pets (View)</h1>
    <div class="nav"><a href="index.php">Home</a> | <a href="owners.php">Owners</a> | <a href="pets.php">Pets</a></div>

    <?php foreach ($owners as $o): ?>
        <h2><?php echo htmlspecialchars($o['owner_name']); ?></h2>
        <p>Phone: <?php echo htmlspecialchars($o['phone']); ?> | Email: <?php echo htmlspecialchars($o['email']); ?></p>
        <?php if (empty($o['pets'])): ?>
            <p><em>No pets registered for this owner.</em></p>
        <?php else: ?>
            <table>
                <tr><th>Pet ID</th><th>Pet Name</th><th>Species</th><th>Breed</th><th>Grooming Date</th><th>Status</th></tr>
                <?php foreach ($o['pets'] as $p): ?>
                <tr>
                    <td><?php echo $p['pet_id']; ?></td>
                    <td><?php echo htmlspecialchars($p['pet_name']); ?></td>
                    <td><?php echo htmlspecialchars($p['species']); ?></td>
                    <td><?php echo htmlspecialchars($p['breed']); ?></td>
                    <td><?php echo htmlspecialchars($p['grooming_date'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($p['status'] ?? ''); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    <?php endforeach; ?>

    <?php if (empty($owners)): ?>
        <p>No owners found.</p>
    <?php endif; ?>
</body>
</html>