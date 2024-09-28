jQuery(document).ready(function ($) {
  // Replace the #dashboard-widgets-wrap element with custom HTML

  $("#dashboard-widgets-wrap").html(`
    <h2>Buna, ${dashboardData.username}</h2>
    <p>Momentan, această pagină este în construcție</p>
    `);
  //   $("#dashboard-widgets-wrap").html(`
  //     <div class="custom-dashboard-content">
  //         <h2>${dashboardData.username}</h2>
  //         <h2>${dashboardData.website}</h2>
  //     </div>
  // `).append(`
  //     <iframe src="https://webpath.ro/clients-dashboard/${dashboardData.website}?username=${dashboardData.username}&key=abc" width="100%" height="800" frameborder="0" allowfullscreen></iframe>
  // `);
});
