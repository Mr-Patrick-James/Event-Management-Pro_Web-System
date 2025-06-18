<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Colegio de Naujan - Event Management System</title>
  <link rel="stylesheet" href="../assets/styler/landing.css" />
</head>
<body>

  <!-- NAVIGATION -->
  <header class="navbar">
    <div class="logo">🎓 Colegio de Naujan</div>
    <nav>
      <ul>
        <li><a href="#">Events</a></li>
        <li><a href="#">Gallery</a></li>
        <li><a href="#">About</a></li>
        <li><a href="#">Contact</a></li>
      </ul>
    </nav>
    <div class="buttons">
      <a href="index.php" class="btn-outline">Sign Up</a>
      <a href="index.php" class="btn-filled">Sign In</a>
    </div>
  </header>

  <!-- HERO SECTION -->
  <section class="hero">
    <div class="hero-left">
      <h1>LET'S PLAN!<br><span class="highlight">THE EVENT</span> YOU DESERVE</h1>
      <p>Take control of every campus celebration, seminar, and gathering with Colegio de Naujan’s official event management platform. From bookings to badges – everything is in one place.</p>

      <div class="event-search">
        <div class="field">
          <label>📍 Event Type</label>
          <input type="text" placeholder="e.g., Seminar, Festival" />
        </div>
        <div class="field">
          <label>📆 Date</label>
          <input type="date" />
        </div>
        <button class="search-btn">Search</button>
      </div>
    </div>

    <div class="hero-right">
      <div class="circle-bg"></div>
      <img src="../assets/images/illus.png" alt="Event Hero" class="hero-img float-animate" />

      <!-- Floating tags -->
      <div class="info-tag" style="top: 10%; left: 60%;">
        <img src="assets/icons/calendar.svg" alt="calendar" />
        <span>Schedule</span>
      </div>
      <div class="info-tag" style="top: 35%; left: 78%;">
        <img src="assets/icons/mic.svg" alt="mic" />
        <span>Event Host</span>
      </div>
      <div class="info-tag" style="top: 65%; left: 68%;">
        <img src="assets/icons/ticket.svg" alt="ticket" />
        <span>QR Tickets</span>
      </div>
    </div>
  </section>

  <footer>
    <p>&copy; 2025 Colegio de Naujan. All rights reserved.</p>
  </footer>

</body>
</html>
