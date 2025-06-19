<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
  header("Location: index.php");
  exit();
}

$page = $_GET['page'] ?? 'home';

// Add all valid pages here exactly as you expect 'page' param to be
$allowed_pages = [
  'home',
  'events',
  'events/categories',  // <-- note 'events/categories' included
  'categories',                                 // <-- added 'categories'
  'users',
  'users/add',
  'users/roles',
  'reports',
  'reports/attendance',
  'reports/feedback',
  'reports/performance',
  'settings'
];

if (!in_array($page, $allowed_pages)) {
  $page = 'home';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard - Colegio de Naujan</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/styler/dashboard.css" />

  <script>
    function toggleDropdown(button) {
      const dropdown = button.parentElement;
      dropdown.classList.toggle('show');
    }
  </script>
</head>

<body>
  <div class="d-flex dashboard-container">

    <!-- Sidebar -->
    <aside class="sidebar bg-light p-3">
      <h2 class="logo text-primary mb-4"><i class="fa fa-chart-line"></i> Dashboard</h2>
      <nav class="nav-links">
        <a href="dashboard.php?page=home" class="nav-link <?= $page === 'home' ? 'active' : '' ?>">
          <i class="fa fa-home"></i> Home
        </a>

        <!-- Events Dropdown -->
        <div class="dropdown mb-3">
          <button class="btn btn-link w-100 text-start dropdown-toggle <?= in_array($page, ['events', 'events/categories', 'categories']) ? 'active' : '' ?>" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fa fa-calendar"></i> Events
          </button>
          <ul class="dropdown-menu">
            <li><a href="dashboard.php?page=events" class="dropdown-item <?= $page === 'events' ? 'active' : '' ?>">All Events</a></li>
            <li><a href="dashboard.php?page=categories" class="dropdown-item <?= $page === 'categories' ? 'active' : '' ?>">Categories</a></li>
          </ul>
        </div>

        <!-- Users Dropdown -->
        <div class="dropdown mb-3">
          <button class="btn btn-link w-100 text-start dropdown-toggle <?= in_array($page, ['users', 'users/add', 'users/roles']) ? 'active' : '' ?>" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fa fa-users"></i> Users
          </button>
          <ul class="dropdown-menu">
            <li><a href="dashboard.php?page=users" class="dropdown-item <?= $page === 'users' ? 'active' : '' ?>">All Users</a></li>
            <li><a href="dashboard.php?page=users/add" class="dropdown-item <?= $page === 'users/add' ? 'active' : '' ?>">Add User</a></li>
            <li><a href="dashboard.php?page=users/roles" class="dropdown-item <?= $page === 'users/roles' ? 'active' : '' ?>">Roles</a></li>
          </ul>
        </div>

        <!-- Reports Dropdown -->
        <div class="dropdown mb-3">
          <button class="btn btn-link w-100 text-start dropdown-toggle <?= in_array($page, ['reports', 'reports/attendance', 'reports/feedback', 'reports/performance']) ? 'active' : '' ?>" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fa fa-file-alt"></i> Reports
          </button>
          <ul class="dropdown-menu">
            <li><a href="dashboard.php?page=reports" class="dropdown-item <?= $page === 'reports' ? 'active' : '' ?>">Overview</a></li>
            <li><a href="dashboard.php?page=reports/attendance" class="dropdown-item <?= $page === 'reports/attendance' ? 'active' : '' ?>">Attendance</a></li>
            <li><a href="dashboard.php?page=reports/feedback" class="dropdown-item <?= $page === 'reports/feedback' ? 'active' : '' ?>">Feedback</a></li>
            <li><a href="dashboard.php?page=reports/performance" class="dropdown-item <?= $page === 'reports/performance' ? 'active' : '' ?>">Performance</a></li>
          </ul>
        </div>

        <!-- Settings -->
        <a href="dashboard.php?page=settings" class="nav-link <?= $page === 'settings' ? 'active' : '' ?>">
          <i class="fa fa-cog"></i> Settings
        </a>

        <!-- Logout -->
        <a href="logout.php" class="nav-link text-danger">
          <i class="fa fa-sign-out-alt"></i> Logout
        </a>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content p-4 w-100">

      <!-- Top Bar -->
      <header class="topbar d-flex justify-content-between align-items-center bg-white p-3 shadow-sm mb-4 rounded">
        <div class="topbar-left">
          <h3>Event Management System</h3>
        </div>
        <div class="topbar-right d-flex align-items-center">
          <div class="notif-icon position-relative">
            <i class="fa fa-bell"></i><span class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill">3</span>
          </div>
          <div class="user-dropdown ms-3">
            <span><?= htmlspecialchars($_SESSION['name'] ?? 'User'); ?></span>
            <i class="fa fa-user-circle"></i>
          </div>
        </div>
      </header>

      <?php
      include 'config.php'; // adjust path if needed

      // Count total categories
      $category_result = $conn->query("SELECT COUNT(*) AS total_categories FROM categories");
      $category_data = $category_result->fetch_assoc();
      $total_categories = $category_data['total_categories'];

      // Count total events
      $event_result = $conn->query("SELECT COUNT(*) AS total_events FROM events");
      $event_data = $event_result->fetch_assoc();
      $total_events = $event_data['total_events'];
      ?>

      <!-- Page Content -->
      <?php if ($page === 'home'): ?>
        <section class="stats-grid mb-4 d-grid gap-3">
          <div class="card stat p-3 shadow-sm rounded">
            <h4>Total Visits</h4>
            <p>1</p><span class="badge bg-primary">+1% this week</span>
          </div>

          <div class="card stat p-3 shadow-sm rounded">
            <h4>Total Categories</h4>
            <p><?= $total_categories ?></p><span class="badge bg-danger">-1% today</span>
          </div>

          <div class="card stat p-3 shadow-sm rounded">
            <h4>Total Events</h4>
            <p><?= $total_events ?></p><span class="badge bg-success">+1% week</span>
          </div>

          <div class="card stat p-3 shadow-sm rounded">
            <h4>Users</h4>
            <p>1</p><span class="badge bg-info">+1% week</span>
          </div>
        </section>

        <section class="bottom-grid d-flex gap-3">
          <!-- Modern Bootstrap Chart Card -->
          <div class="card shadow-sm mb-4 p-4 w-75">
            <div class="card-header bg-primary text-white">
              <h6 class="m-0">Team Performance Curve</h6>
            </div>
            <div class="card-body">
              <div class="chart-container" style="position: relative; height: 300px;">
                <canvas id="teamChart"></canvas>
              </div>
            </div>
          </div>

          <div class="card goals shadow-sm p-4 w-25">
            <h4>Team Goals</h4>
            <ul class="list-unstyled">
              <li>Visits <span class="bar bg-primary" style="width: 75%"></span></li>
              <li>Likes <span class="bar bg-danger" style="width: 50%"></span></li>
              <li>Comments <span class="bar bg-warning" style="width: 35%"></span></li>
            </ul>
          </div>
        </section>
      <?php else:
        // Base path to pages folder
        $basePath = realpath(__DIR__ . '/../pages');

        // Sanitize page to avoid directory traversal attacks
        $safePage = str_replace(['..', './', '../'], '', $page);

        // Try to load file.php first, else folder/index.php
        $pageFile = "{$basePath}/{$safePage}.php";
        $folderIndex = "{$basePath}/{$safePage}/index.php";

        if (file_exists($pageFile)) {
          include $pageFile;
        } elseif (file_exists($folderIndex)) {
          include $folderIndex;
        } else {
          echo "<section class='card' style='margin: 2rem;'><h2>Page not found</h2></section>";
        }
      endif;
      ?>

    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const ctx = document.getElementById('teamChart').getContext('2d');

      if (window.teamChart instanceof Chart) {
        window.teamChart.destroy();
      }

      const gradient = ctx.createLinearGradient(0, 0, 0, 300);
      gradient.addColorStop(0, 'rgba(54, 162, 235, 0.6)');
      gradient.addColorStop(1, 'rgba(255, 255, 255, 0)');

      window.teamChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
          datasets: [{
            label: 'Performance',
            data: [12, 19, 3, 5, 2, 10],
            backgroundColor: gradient,
            borderColor: '#007bff',
            pointBackgroundColor: '#007bff',
            pointRadius: 5,
            tension: 0.4,
            fill: true
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: true,
              labels: {
                color: '#333',
                font: { size: 14 }
              }
            },
            tooltip: {
              mode: 'index',
              intersect: false
            }
          },
          scales: {
            x: {
              ticks: {
                color: '#555'
              },
              grid: {
                color: '#eee'
              }
            },
            y: {
              ticks: {
                color: '#555'
              },
              grid: {
                color: '#eee'
              }
            }
          }
        }
      });
    });
  </script>

</body>

</html>
