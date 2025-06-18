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
  <link rel="stylesheet" href="../assets/styler/dashboard.css" />
  <script>
    function toggleDropdown(button) {
      const dropdown = button.parentElement;
      dropdown.classList.toggle('open');
    }
  </script>
</head>

<body>
  <div class="dashboard-container">

    <!-- Sidebar -->
    <aside class="sidebar">
      <h2 class="logo"><i class="fa fa-chart-line"></i> Dashboard</h2>
      <nav class="nav-links">

        <a href="dashboard.php?page=home" class="<?= $page === 'home' ? 'active' : '' ?>">
          <i class="fa fa-home"></i> Home
        </a>

        <!-- Events Dropdown -->
        <?php
        // group pages for opening dropdown and active state
        $events_pages = ['events', 'events/add', 'events/categories', 'categories'];
        ?>
        <div class="dropdown <?= in_array($page, $events_pages) ? 'open' : '' ?>">
          <button class="dropdown-toggle <?= in_array($page, $events_pages) ? 'active' : '' ?>" onclick="toggleDropdown(this)">
            <i class="fa fa-calendar"></i> Events <i class="fa fa-caret-down"></i>
          </button>
          <div class="dropdown-menu">
            <a href="dashboard.php?page=events" class="<?= $page === 'events' ? 'active' : '' ?>">All Events</a>
            <!-- <a href="dashboard.php?page=events/add" class="<?= $page === 'events/add' ? 'active' : '' ?>">Add Event</a>-->
            <a href="dashboard.php?page=categories" class="<?= $page === 'categories' ? 'active' : '' ?>">Categories</a>
          </div>
        </div>

        <!-- Users Dropdown -->
        <?php $users_pages = ['users', 'users/add', 'users/roles']; ?>
        <div class="dropdown <?= in_array($page, $users_pages) ? 'open' : '' ?>">
          <button class="dropdown-toggle <?= in_array($page, $users_pages) ? 'active' : '' ?>" onclick="toggleDropdown(this)">
            <i class="fa fa-users"></i> Users <i class="fa fa-caret-down"></i>
          </button>
          <div class="dropdown-menu">
            <a href="dashboard.php?page=users" class="<?= $page === 'users' ? 'active' : '' ?>">All Users</a>
            <a href="dashboard.php?page=users/add" class="<?= $page === 'users/add' ? 'active' : '' ?>">Add User</a>
            <a href="dashboard.php?page=users/roles" class="<?= $page === 'users/roles' ? 'active' : '' ?>">Roles</a>
          </div>
        </div>

        <!-- Reports Dropdown -->
        <?php $reports_pages = ['reports', 'reports/attendance', 'reports/feedback', 'reports/performance']; ?>
        <div class="dropdown <?= in_array($page, $reports_pages) ? 'open' : '' ?>">
          <button class="dropdown-toggle <?= in_array($page, $reports_pages) ? 'active' : '' ?>" onclick="toggleDropdown(this)">
            <i class="fa fa-file-alt"></i> Reports <i class="fa fa-caret-down"></i>
          </button>
          <div class="dropdown-menu">
            <a href="dashboard.php?page=reports" class="<?= $page === 'reports' ? 'active' : '' ?>">Overview</a>
            <a href="dashboard.php?page=reports/attendance" class="<?= $page === 'reports/attendance' ? 'active' : '' ?>">Attendance</a>
            <a href="dashboard.php?page=reports/feedback" class="<?= $page === 'reports/feedback' ? 'active' : '' ?>">Feedback</a>
            <a href="dashboard.php?page=reports/performance" class="<?= $page === 'reports/performance' ? 'active' : '' ?>">Performance</a>
          </div>
        </div>

        <!-- Settings -->
        <a href="dashboard.php?page=settings" class="<?= $page === 'settings' ? 'active' : '' ?>">
          <i class="fa fa-cog"></i> Settings
        </a>

        <!-- Logout -->
        <a href="logout.php" class="logout"><i class="fa fa-sign-out-alt"></i> Logout</a>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">

      <!-- Top Bar -->
      <header class="topbar">
        <div class="topbar-left">
          <h3>Event Management System</h3>
        </div>
        <div class="topbar-right">
          <div class="notif-icon">
            <i class="fa fa-bell"></i><span class="badge">3</span>
          </div>
          <div class="user-dropdown">
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
        <section class="stats-grid">
          <div class="card stat">
            <h4>Total Visits</h4>
            <p>1</p><span class="label blue">+1% this week</span>
          </div>

          <div class="card stat">
            <h4>Total Categories</h4>
            <p><?= $total_categories ?></p><span class="label red">-1% today</span>
          </div>

          <div class="card stat">
            <h4>Total Events</h4>
            <p><?= $total_events ?></p><span class="label green">+1% week</span>
          </div>

          <div class="card stat">
            <h4>Users</h4>
            <p>1</p><span class="label blue">+1% week</span>
          </div>
        </section>


        <section class="bottom-grid">
          <div class="card chart">
            <h4>Team Curve</h4>
            <canvas id="teamChart" width="100%" height="100"></canvas>
          </div>
          <div class="card goals">
            <h4>Team Goals</h4>
            <ul>
              <li>Visits <span class="bar blue" style="width: 75%"></span></li>
              <li>Likes <span class="bar red" style="width: 50%"></span></li>
              <li>Comments <span class="bar yellow" style="width: 35%"></span></li>
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

  <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap" async defer></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="../assets/script/dashboard.js"></script>

<style>
  .chart-container {
    position: relative;
    height: 300px;
    width: 100%;
  }
  
  .chart-container canvas {
    width: 100% !important;
    height: 100% !important;
  }
</style>

<div class="card">
  <h4>Team Curve</h4>
  <div class="chart-container">
    <canvas id="teamChart"></canvas>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const initChart = () => {
    const ctx = document.getElementById('teamChart');
    if (!ctx) return;
    
    // Destroy previous instance if exists
    if (window.teamChart instanceof Chart) {
      window.teamChart.destroy();
    }
    
    window.teamChart = new Chart(ctx.getContext('2d'), {
      type: 'line',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
          label: 'Performance',
          data: [12, 19, 3, 5, 2, 3],
          borderColor: 'rgb(75, 192, 192)',
          tension: 0.1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false }
        }
      }
    });
  };
  
  initChart();
});
</script>
</body>

</html>