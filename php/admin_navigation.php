<!-- navbar.php -->
<nav>
  <div class="navbar-container">
    <img src="logo.jpg" alt="Logo" class="logo">
    <ul>
    <li><a href="recipient_requests.php">Recipient Requests</a></li>
      <li><a href="claim_requests.php">Claim Requests</a></li>  
       <li><a href="manage_users.php">Manage Users</a></li> 
        <li><a href="View_complaints.php">View Complaints</a></li>
        <li><a href="add_notice.php">Add Notice</a></li>
    </ul>
  </div>
</nav>

<style>
  /* General navbar styling */
  nav {
    background: rgba(0, 0, 0, 0.8); /* Black background with transparency */
    backdrop-filter: blur(12px); /* Glass effect */
    padding: 25px 50px; /* Wide navbar padding */
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); /* Subtle shadow for depth */
    display: flex;
    justify-content: center;
    align-items: center;
    position: fixed; /* Fix the navbar at the top */
    width: 100%;
    top: 0;
    left: 0;
    z-index: 1000; /* Ensure the navbar stays on top */
  }

  /* Container for logo and navigation links */
  .navbar-container {
    display: flex;
    align-items: center;
    width: 100%;
    max-width: 1400px; /* Wider container for accommodating the links */
    justify-content: space-between;
  }

  /* Logo styling */
  .logo {
    width: 70px; /* Logo size */
    height: 70px;
    border-radius: 50%;
    object-fit: cover; /* Ensures proper cropping */
    border: 2px solid orange; /* Orange border for the logo */
  }

  /* Navigation list */
  nav ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
    display: flex;
  }

  /* Navigation items */
  nav ul li {
    margin: 0 10px; /* Reduced spacing for compact fit */
  }

  /* Link styles */
  nav ul li a {
    text-decoration: none;
    color: white;
    font-weight: bold;
    font-size: 14px; /* Smaller font size */
    padding: 6px 12px; /* Adjusted padding for better alignment */
    border-radius: 6px;
    transition: all 0.3s ease;
  }

  /* Hover effect for links */
  nav ul li a:hover {
    background: orange; /* Orange highlight on hover */
    color: black; /* Black text for contrast */
  }

  /* Ensure the body content does not overlap the navbar */
  body {
    margin-top: 100px; /* Adjust this value to ensure content is below the navbar */
  }
</style>
