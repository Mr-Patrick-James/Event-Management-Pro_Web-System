<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_id'])) {
    header("Location: /dashboard.php?page=events/categories");
    exit();
}

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'event_management';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SHOW TABLES LIKE 'categories'");
if ($result->num_rows === 0) {
    die("Error: Table 'categories' does not exist.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $name = trim($_POST['category_name']);
        if ($name !== '') {
            $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->bind_param("s", $name);
            $stmt->execute();
            $stmt->close();
        }
    } elseif (isset($_POST['edit'])) {
        $id = intval($_POST['id']);
        $name = trim($_POST['category_name']);
        if ($name !== '') {
            $stmt = $conn->prepare("UPDATE categories SET name = ? WHERE id = ?");
            $stmt->bind_param("si", $name, $id);
            $stmt->execute();
            $stmt->close();
        }
    }
    header("Location: /event-management/includes/dashboard.php?page=categories");
    exit();
}

if (isset($_GET['page']) && $_GET['page'] === 'categories' && isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: dashboard.php?page=categories");
    exit();
}


$categories = $conn->query("SELECT * FROM categories ORDER BY name ASC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - EventManager Pro</title>
    <link rel="stylesheet" href="../assets/styler/categories.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
</head>

<body>
    <div class="container">
        <header>
            <div class="logo">
                <i class="fas fa-calendar-alt"></i>
                <h1>EventManager Pro</h1>
            </div>
            <div class="user-controls">
                <button><i class="fas fa-user"></i> Admin Dashboard</button>
            </div>
        </header>

        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-tags"></i> Manage Event Categories</h2>
                <button class="btn btn-primary" id="addCategoryBtn"><i class="fas fa-plus"></i> Add Category</button>
            </div>
            <div class="card-body">
                <table class="categories-table">
                    <thead>
                        <tr>
                            <th>Category Name</th>
                            <th>Events Count</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $categories->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td>
                                    <?php
                                    $cat_id = $row['id'];
                                    $count = $conn->query("SELECT COUNT(*) as total FROM events WHERE category_id = $cat_id");
                                    echo $count->fetch_assoc()['total'];
                                    ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="action-btn edit-btn"
                                            data-id="<?= $row['id'] ?>"
                                            data-name="<?= htmlspecialchars($row['name']) ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="?page=categories&delete=<?= $row['id'] ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this category?');">
                                            <i class="fas fa-trash"></i>
                                        </a>

                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <footer>
            <p>EventManager Pro &copy; 2025 - All rights reserved</p>
        </footer>
    </div>

    <!-- Add Category Modal -->
    <div id="addCategoryModal" class="modal">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fas fa-plus-circle"></i> Add Category</h3>
                <span class="close-btn">&times;</span>
            </div>
            <div class="modal-body">
                <label>Category Name</label>
                <input type="text" name="category_name" required placeholder="Enter category name">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline close-btn">Cancel</button>
                <button type="submit" name="add" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
            </div>
        </form>
    </div>

    <!-- Edit Category Modal -->
    <div id="editCategoryModal" class="modal">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fas fa-edit"></i> Edit Category</h3>
                <span class="close-btn">&times;</span>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="editCategoryId">
                <label>Category Name</label>
                <input type="text" name="category_name" id="editCategoryName" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline close-btn">Cancel</button>
                <button type="submit" name="edit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
            </div>
        </form>
    </div>

    <script>
        const addBtn = document.getElementById('addCategoryBtn');
        const addModal = document.getElementById('addCategoryModal');
        const editModal = document.getElementById('editCategoryModal');
        const closeBtns = document.querySelectorAll('.close-btn');
        const editBtns = document.querySelectorAll('.edit-btn');

        addBtn.onclick = () => {
            addModal.classList.add('show');
            document.body.style.overflow = 'hidden';
        };

        editBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                const name = btn.dataset.name;
                document.getElementById('editCategoryId').value = id;
                document.getElementById('editCategoryName').value = name;
                editModal.classList.add('show');
                document.body.style.overflow = 'hidden';
            });
        });

        closeBtns.forEach(btn => {
            btn.onclick = () => {
                addModal.classList.remove('show');
                editModal.classList.remove('show');
                document.body.style.overflow = 'auto';
            };
        });

        window.onclick = (e) => {
            if (e.target === addModal || e.target === editModal) {
                addModal.classList.remove('show');
                editModal.classList.remove('show');
                document.body.style.overflow = 'auto';
            }
        };
    </script>
</body>

</html>