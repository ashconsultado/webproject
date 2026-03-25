<?php
include 'dbconn.php';

// ADD PRODUCT
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    $stmt = $conn->prepare("INSERT INTO products (name, quantity, price) VALUES (?, ?, ?)");
    $stmt->bind_param("sid", $name, $quantity, $price);
    $stmt->execute();

    header("Location: index.php");
    exit();
}

// DELETE PRODUCT
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: index.php");
    exit();
}

// FETCH PRODUCTS
$result = $conn->query("SELECT * FROM products ORDER BY id DESC");

// CALCULATE TOTAL INVENTORY VALUE
$totalValue = 0;
$products = [];

while ($row = $result->fetch_assoc()) {
    $products[] = $row;
    $totalValue += $row['quantity'] * $row['price'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inventory Dashboard</title>
    <style>
        body { font-family: Arial; margin: 40px; background: #f9f9f9; }
        h2 { margin-bottom: 10px; }
        form input { padding: 8px; margin-right: 5px; }
        button { padding: 8px 12px; cursor: pointer; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; background: #fff; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: center; }
        th { background: #333; color: white; }
        .delete { color: red; text-decoration: none; }
        .summary { margin-top: 15px; font-weight: bold; }
    </style>
</head>
<body>

<h2>📊 Inventory Dashboard</h2>

<!-- ADD FORM -->
<form method="POST">
    <input type="text" name="name" placeholder="Product Name" required>
    <input type="number" name="quantity" placeholder="Qty" required>
    <input type="number" step="0.01" name="price" placeholder="Price" required>
    <button type="submit" name="add">Add</button>
</form>

<!-- TABLE -->
<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Qty</th>
        <th>Price</th>
        <th>Total</th>
        <th>Action</th>
    </tr>

    <?php foreach ($products as $row): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= $row['quantity'] ?></td>
            <td><?= number_format($row['price'], 2) ?></td>
            <td><?= number_format($row['quantity'] * $row['price'], 2) ?></td>
            <td>
                <a class="delete" href="?delete=<?= $row['id'] ?>" 
                   onclick="return confirm('Delete this item?')">Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>

</table>

<!-- SUMMARY -->
<div class="summary">
    Total Inventory Value: ₱<?= number_format($totalValue, 2) ?>
</div>

</body>
</html>