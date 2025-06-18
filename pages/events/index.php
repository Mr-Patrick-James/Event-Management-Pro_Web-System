<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: /dashboard.php?page=events");
    exit();
}

// DB Connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'event_management';
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if table 'events' exists
$result = $conn->query("SHOW TABLES LIKE 'events'");
if ($result->num_rows === 0) {
    die("Error: Table 'events' does not exist.");
}

// Handle form POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Determine if it's an add or edit operation
    $isEdit = isset($_POST['id']) && !empty($_POST['id']);

    $name = trim($_POST['eventname']);
    $startdatetime = trim($_POST['startdatetime']);
    $enddatetime = trim($_POST['enddatetime']);
    $category = trim($_POST['category']);
    $location = trim($_POST['location']);
    $description = trim($_POST['description']);
    $status = trim($_POST['status']);

    // Placeholder for image handling
    $image = '';

    // Validation
    if ($name && $startdatetime && $enddatetime && $category && $location) {
        if ($isEdit) {
            $id = intval($_POST['id']);
            $stmt = $conn->prepare("UPDATE events 
                                    SET eventname=?, startdatetime=?, enddatetime=?, category=?, location=?, description=?, image=?, status=?, updated_at=NOW() 
                                    WHERE id=?");
            $stmt->bind_param("ssssssssi", $name, $startdatetime, $enddatetime, $category, $location, $description, $image, $status, $id);
        } else {
            $stmt = $conn->prepare("INSERT INTO events (eventname, startdatetime, enddatetime, category, location, description, image, status, created_at, updated_at) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
            $stmt->bind_param("ssssssss", $name, $startdatetime, $enddatetime, $category, $location, $description, $image, $status);
        }

        $stmt->execute();
        $stmt->close();
    }

    header("Location: dashboard.php?page=events");
    exit();
}

// Delete Event
if (isset($_GET['page']) && $_GET['page'] === 'events' && isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // Delete associated image
    $res = $conn->query("SELECT image FROM events WHERE id = $id");
    if ($res && $row = $res->fetch_assoc()) {
        if (!empty($row['image']) && file_exists("../uploads/" . $row['image'])) {
            unlink("../uploads/" . $row['image']);
        }
    }

    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: dashboard.php?page=events");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management System</title>
    <link rel="stylesheet" href="../assets/styler/addEvent.css" />
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
                <button>
                    <i class="fas fa-user"></i> Admin Panel
                </button>
            </div>
        </header>

        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-calendar-day"></i> Event List</h2>
                <button class="btn btn-primary" id="openModalBtn">
                    <i class="fas fa-plus"></i> Add New Event
                </button>
            </div>

            <div class="card-body">
                <table class="events-table">
                    <thead>
                        <tr>
                            <th>Event Title</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT * FROM events ORDER BY startdatetime DESC";
                        $result = $conn->query($query);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()):
                                $start = date("M d, Y", strtotime($row['startdatetime']));
                                $startTime = date("g:i A", strtotime($row['startdatetime']));
                                $endTime = date("g:i A", strtotime($row['enddatetime']));
                        ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['eventname']) ?></td>
                                    <td><?= $start ?></td>
                                    <td><?= $startTime ?> - <?= $endTime ?></td>
                                    <td><?= ucfirst(htmlspecialchars($row['category'])) ?></td>
                                    <td>
                                        <span class="status-badge <?= $row['status'] === 'active' ? 'status-scheduled' : 'status-completed' ?>">
                                            <?= ucfirst($row['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button type="button" class="action-btn edit-btn"
                                                data-id="<?= $row['id'] ?>"
                                                data-name="<?= htmlspecialchars($row['eventname'], ENT_QUOTES) ?>"
                                                data-start="<?= date('Y-m-d\TH:i', strtotime($row['startdatetime'])) ?>"
                                                data-end="<?= date('Y-m-d\TH:i', strtotime($row['enddatetime'])) ?>"
                                                data-category="<?= htmlspecialchars($row['category'], ENT_QUOTES) ?>"
                                                data-location="<?= htmlspecialchars($row['location'], ENT_QUOTES) ?>"
                                                data-description="<?= htmlspecialchars($row['description'], ENT_QUOTES) ?>"
                                                data-status="<?= $row['status'] ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <a href="dashboard.php?page=events&delete=<?= $row['id'] ?>"
                                                class="action-btn delete-btn"
                                                onclick="return confirm('Are you sure you want to delete this event?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                        <?php
                            endwhile;
                        } else {
                            echo '<tr><td colspan="6"><div class="empty-state">
                                <i class="fas fa-calendar-times"></i>
                                <h3>No Events Found</h3>
                                <p>Start by adding your first event</p>
                            </div></td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal for Add/Edit -->
        <div class="modal" id="eventModal">
            <form class="modal-content" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="eventId">

                <div class="modal-header">
                    <h3 class="modal-title"><i class="fas fa-calendar-plus"></i> <span id="modalTitle">Create New Event</span></h3>
                    <button class="close-btn" type="button" id="closeModalBtn">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="eventName"><i class="fas fa-heading"></i> Event Name</label>
                            <input type="text" id="eventName" name="eventname" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="startdatetime"><i class="fas fa-play-circle"></i> Start Date & Time</label>
                            <input type="datetime-local" id="startdatetime" name="startdatetime" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="enddatetime"><i class="fas fa-stop-circle"></i> End Date & Time</label>
                            <input type="datetime-local" id="enddatetime" name="enddatetime" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="category"><i class="fas fa-tag"></i> Category</label>
                            <select name="category" id="category" class="form-control" required>
                                <option value="">Select Category</option>
                                <?php
                                // MODIFIED: Fetch categories from database
                                $catQuery = "SELECT id, name FROM categories";
                                $catResult = $conn->query($catQuery);

                                if ($catResult->num_rows > 0) {
                                    while ($catRow = $catResult->fetch_assoc()) {
                                        $catId = htmlspecialchars($catRow['id']);
                                        $catName = htmlspecialchars($catRow['name']);
                                        echo "<option value='$catId'>$catName</option>";
                                    }
                                } else {
                                    echo '<option value="">No categories found</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="location"><i class="fas fa-map-marker-alt"></i> Location</label>
                            <input type="text" name="location" id="location" class="form-control" required>
                        </div>

                        <div class="form-group full-width">
                            <label for="description"><i class="fas fa-align-left"></i> Description</label>
                            <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="form-group full-width">
                            <label for="image"><i class="fas fa-image"></i> Event Image</label>
                            <input type="file" name="image" id="image" class="form-control" accept="image/*">
                        </div>

                        <div class="form-group">
                            <label for="status"><i class="fas fa-toggle-on"></i> Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-outline" type="button" id="cancelBtn">Cancel</button>
                    <button class="btn btn-primary" type="submit" name="saveEvent">Save Event</button>
                </div>
            </form>
        </div>

        <footer>
            <p>EventManager Pro &copy; 2025</p>
        </footer>
    </div>

    <script>
        // DOM Elements
        const modal = document.getElementById('eventModal');
        const openModalBtn = document.getElementById('openModalBtn');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        const modalTitle = document.getElementById('modalTitle');
        const eventForm = document.querySelector('.modal-content');

        // Open modal for new event
        openModalBtn.addEventListener('click', () => {
            modalTitle.textContent = 'Create New Event';
            eventForm.reset();
            document.getElementById('eventId').value = '';
            modal.classList.add('show');
        });

        // Close modal
        function closeModal() {
            modal.classList.remove('show');
        }

        closeModalBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);

        // Close modal when clicking outside
        window.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });

        // Edit event functionality
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                modalTitle.textContent = 'Edit Event';

                // Populate form with event data
                document.getElementById('eventId').value = this.dataset.id;
                document.getElementById('eventName').value = this.dataset.name;
                document.getElementById('startdatetime').value = this.dataset.start;
                document.getElementById('enddatetime').value = this.dataset.end;
                document.getElementById('category').value = this.dataset.category;
                document.getElementById('location').value = this.dataset.location;
                document.getElementById('description').value = this.dataset.description;
                document.getElementById('status').value = this.dataset.status;

                modal.classList.add('show');
            });
        });

        // Set min date to today for date pickers
        const today = new Date().toISOString().slice(0, 16);
        document.getElementById('startdatetime').min = today;
        document.getElementById('enddatetime').min = today;

        // Form validation
        eventForm.addEventListener('submit', function(e) {
            const start = document.getElementById('startdatetime').value;
            const end = document.getElementById('enddatetime').value;

            if (new Date(start) >= new Date(end)) {
                e.preventDefault();
                alert('End date/time must be after start date/time');
                document.getElementById('enddatetime').focus();
            }
        });
    </script>
</body>

</html>