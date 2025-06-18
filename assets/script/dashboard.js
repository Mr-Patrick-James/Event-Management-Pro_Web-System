// Toggle Sidebar
document.getElementById("menu-toggle").addEventListener("click", function(e) {
    e.preventDefault();
    document.getElementById("wrapper").classList.toggle("toggled");
  });
  
  // Chart.js - Attendance Chart
  const ctx = document.getElementById('attendanceChart').getContext('2d');
  const attendanceChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['January', 'February', 'March', 'April', 'May'],
      datasets: [{
        label: 'Attendance',
        data: [65, 59, 80, 81, 56],
        backgroundColor: 'rgba(54, 162, 235, 0.2)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 2,
        fill: true,
        tension: 0.4
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'top',
        },
        title: {
          display: true,
          text: 'Monthly Attendance'
        }
      }
    }
  });

  function toggleDropdown(button) {
  const parent = button.closest('.dropdown');
  parent.classList.toggle('open');
}

  

function initMap() {
  // Only run if the map container exists (i.e., on events/add page)
  const mapElement = document.getElementById("map");
  const locationInput = document.getElementById("location");

  if (!mapElement || !locationInput) return;

  const defaultLocation = { lat: 13.407, lng: 122.560 }; // Naujan

  const map = new google.maps.Map(mapElement, {
    zoom: 14,
    center: defaultLocation,
  });

  const marker = new google.maps.Marker({
    position: defaultLocation,
    map: map,
    draggable: true,
  });

  map.addListener("click", function (e) {
    marker.setPosition(e.latLng);
    locationInput.value = `${e.latLng.lat()},${e.latLng.lng()}`;
  });
}


