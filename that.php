<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Admin Panel</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
       /* Base styles */
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen,
    Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif;
  background-color: #f5f5f5;
}

/* Layout */
.sidebar {
  width: 256px;
  background-color: #fff;
  padding: 16px;
  height: 100vh;
  position: fixed;
  left: 0;
  top: 0;
}

.main-content {
  margin-left: 256px;
  padding: 24px;
  min-height: 100vh;
}

/* Sidebar styles */
.sidebar-header {
  display: flex;
  align-items: center;
  margin-bottom: 24px;
}

.user-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background-color: #e0e0e0;
  margin-right: 12px;
}

.user-name {
  font-size: 14px;
  color: #333;
  font-weight: normal;
}

.nav-menu {
  list-style: none;
}

.nav-item {
  display: flex;
  align-items: center;
  padding: 8px 24px;
  cursor: pointer;
  border-radius: 4px;
  margin-bottom: 8px;
}

.nav-item svg {
  margin-right: 24px;
}

.nav-item span {
  font-size: 14px;
  color: rgba(0, 0, 0, 0.87);
}

.nav-item.active {
  background-color: rgba(47, 128, 237, 0.1);
}

.nav-item.active svg path {
  fill: #2f80ed;
}

/* Search bar */
.search-bar {
  background-color: #fff;
  padding: 8px 16px;
  border-radius: 4px;
  display: flex;
  align-items: center;
  margin-bottom: 16px;
}

.search-bar svg {
  margin-right: 8px;
}

.search-bar span {
  color: #8e8e93;
  font-size: 14px;
}

/* Quick actions */
.quick-actions {
  display: flex;
  gap: 14px;
  margin-bottom: 16px;
}

.action-card {
  width: 215px;
  height: 116px;
  background-color: #fff;
  border-radius: 14px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

.action-card.active {
  background-color: #2f80ed;
  color: #fff;
}

.action-card svg {
  margin-bottom: 16px;
}

.action-card h3 {
  font-size: 14px;
  font-weight: 500;
}

/* Active people section */
.active-people {
  background-color: #fff;
  border-radius: 14px;
  padding: 24px;
  margin-bottom: 16px;
}

.section-title {
  font-size: 20px;
  font-weight: 500;
  margin-bottom: 16px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(15, 1fr);
  gap: 16px;
  padding: 8px;
  background-color: #d1d1d6;
}

.stat-item {
  text-align: center;
}

.stat-item h4 {
  font-size: 12px;
  color: #000;
  margin-bottom: 4px;
  font-weight: normal;
}

.stat-item span {
  font-size: 14px;
  color: #000;
}

/* Content wrapper */
.content-wrapper {
  display: flex;
  gap: 16px;
}

/* Filters */
.filters {
  width: 248px;
  background-color: #fff;
  border-radius: 14px;
  padding: 24px;
}

.filter-group {
  margin-bottom: 24px;
}

.filter-group h3 {
  font-size: 12px;
  color: #000;
  margin-bottom: 8px;
  font-weight: normal;
}

.checkbox-label {
  display: flex;
  align-items: center;
  font-size: 12px;
  color: #4f4f4f;
  margin-bottom: 4px;
  cursor: pointer;
}

.checkbox-label input {
  margin-right: 8px;
}

.count {
  margin-left: 4px;
}

.clear-btn {
  width: 200px;
  height: 36px;
  border: 2px solid #4f4f4f;
  border-radius: 18px;
  background: none;
  color: #4f4f4f;
  font-size: 14px;
  cursor: pointer;
  margin-top: 24px;
}

/* Results section */
.results-section {
  flex: 1;
  background-color: #fff;
  border-radius: 14px;
  padding: 24px;
}

.results-header {
  margin-bottom: 24px;
}

.results-header h2 {
  font-size: 20px;
  color: #4f4f4f;
  font-weight: normal;
}

/* Data table */
.table-container {
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table th {
  background-color: #d1d1d6;
  padding: 8px;
  text-align: left;
  font-size: 12px;
  font-weight: normal;
}

.data-table td {
  padding: 8px;
  font-size: 12px;
}

/* Pagination */
.pagination {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-top: 24px;
  gap: 16px;
}

.page-numbers {
  display: flex;
  gap: 8px;
}

.page-btn {
  width: 24px;
  height: 27px;
  border: none;
  background: none;
  cursor: pointer;
  font-size: 14px;
}

.page-btn.active {
  color: #2f80ed;
}

.prev-btn {
  color: #c7c7cc;
  font-size: 12px;
  border: none;
  background: none;
  cursor: pointer;
}

.next-btn,
.show-all-btn {
  color: #2f80ed;
  font-size: 12px;
  border: none;
  background: none;
  cursor: pointer;
}

/* Responsive styles */
@media (max-width: 991px) {
  .stats-grid {
    grid-template-columns: repeat(8, 1fr);
  }

  .content-wrapper {
    flex-direction: column;
  }

  .filters {
    width: 100%;
  }
}

@media (max-width: 640px) {
  .sidebar {
    display: none;
  }

  .main-content {
    margin-left: 0;
  }

  .quick-actions {
    flex-direction: column;
  }

  .action-card {
    width: 100%;
  }

  .stats-grid {
    grid-template-columns: repeat(4, 1fr);
  }

  .data-table th,
  .data-table td {
    display: block;
  }

  .data-table thead {
    display: none;
  }

  .data-table tr {
    margin-bottom: 16px;
    display: block;
    border-bottom: 1px solid #e0e0e0;
  }
}
    </style>
</head>

<body>
    <div class="container">
        <aside class="sidebar">
            <div class="user-profile">
                <img src="Screenshot 2025-03-21 001353.png" alt="User Avatar" class="avatar">
                <div class="user-info">
                    <span class="user-name">Admin User</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
            </div>
            <nav>
                <ul>
                    <li data-page="dashboard" class="nav-item active">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </li>
                    <li data-page="accounts" class="nav-item">
                        <i class="fas fa-users"></i> Manage Accounts
                    </li>
                    <li data-page="applications" class="nav-item">
                        <i class="fas fa-file-alt"></i> Manage Applications
                    </li>
                    <li data-page="scholarships" class="nav-item">
                        <i class="fas fa-graduation-cap"></i> Manage Scholarships
                    </li>
                    <li data-page="settings" class="nav-item">
                        <i class="fas fa-cog"></i> Settings
                    </li>
                    <li data-page="profile" class="nav-item">
                        <i class="fas fa-user"></i> Profile
                    </li>
                </ul>
            </nav>
        </aside>

        <main class="content">
            <div id="dashboard" class="page active-page">
                <h2>Dashboard</h2>
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <div class="stat-title">Total Applications</div>
                        <div class="stat-value" id="total-applications">120</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-title">Approved Applications</div>
                        <div class="stat-value" id="approved-applications">80</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-title">Pending Applications</div>
                        <div class="stat-value" id="pending-applications">40</div>
                    </div>
                </div>
            </div>

            <div id="accounts" class="page">
                <h2>Manage Accounts</h2>
                <button class="add-btn" id="add-account-btn">Add Account</button>
                <div class="data-table">
                    <table id="accounts-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>user1</td>
                                <td>user1@example.com</td>
                                <td>Student</td>
                                <td><button class="action-btn">Edit</button> <button class="action-btn">Delete</button></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>admin1</td>
                                <td>admin1@example.com</td>
                                <td>Admin</td>
                                <td><button class="action-btn">Edit</button> <button class="action-btn">Delete</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="applications" class="page">
                <h2>Manage Applications</h2>
                <div class="data-table">
                    <table id="applications-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Student</th>
                                <th>Scholarship</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>John Doe</td>
                                <td>Merit Scholarship</td>
                                <td>Pending</td>
                                <td><button class="action-btn">View</button> <button class="action-btn">Approve</button> <button
                                        class="action-btn">Reject</button></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Jane Smith</td>
                                <td>Need-Based Scholarship</td>
                                <td>Approved</td>
                                <td><button class="action-btn">View</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="scholarships" class="page">
                <h2>Manage Scholarships</h2>
                <button class="add-btn" id="add-scholarship-btn">Add Scholarship</button>
                <div class="data-table">
                    <table id="scholarships-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Deadline</th>
                                <th>Amount</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Merit Scholarship</td>
                                <td>2024-12-31</td>
                                <td>$1000</td>
                                <td><button class="action-btn">Edit</button> <button class="action-btn">Delete</button></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Need-Based Scholarship</td>
                                <td>2024-11-15</td>
                                <td>$500</td>
                                <td><button class="action-btn">Edit</button> <button class="action-btn">Delete</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="settings" class="page">
                <h2>All Enquiries</h2>
                <div id="settings-content">
                    </div>
            </div>

            <div id="profile" class="page">
                <h2>Profile</h2>
                <div class="profile-card">
                    <img src="Screenshot 2025-03-21 001353.png" alt="Profile Picture" class="profile-picture">
                    <div class="profile-details">
                        <div class="profile-name">Admin User</div>
                        <div class="profile-email">admin@example.com</div>
                        <div class="profile-role">Administrator</div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script> document.addEventListener('DOMContentLoaded', function() {
    const navLinks = document.querySelectorAll('.nav-item');
    const pages = document.querySelectorAll('.page');
    const settingsContent = document.getElementById('settings-content');

    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            const pageId = this.getAttribute('data-page');

            navLinks.forEach(navLink => navLink.classList.remove('active'));
            pages.forEach(page => page.classList.remove('active-page'));

            this.classList.add('active');
            document.getElementById(pageId).classList.add('active-page');

            //  Load enquiries table when settings page is clicked
            if (pageId === 'settings') {
                loadEnquiriesTable();
            }
        });
    });

    document.getElementById('add-account-btn').addEventListener('click', function() {
        alert('Add account functionality goes here.');
    });

    document.getElementById('add-scholarship-btn').addEventListener('click', function() {
        alert('Add scholarship functionality goes here.');
    });

    document.getElementById('save-settings-btn').addEventListener('click', function() {
        const siteTitle = document.getElementById('site-title').value;
        alert(`Site title saved as: ${siteTitle}`);
    });

    //  Settings JS functions

    function loadEnquiriesTable() {
        const enquiriesHTML = `
            <div class="enquiries-container">
                <div class="enquiries-header">
                    <div class="enquiries-title">All Enquiries</div>
                    <div class="enquiries-filters">
                        <input type="text" placeholder="dd-mm-yyyy">
                        <select>
                            <option>Select Status</option>
                            <option>COMPLETED</option>
                            <option>PENDING</option>
                        </select>
                        <button class="filter-button">FILTER</button>
                        <button class="reset-button">RESET</button>
                    </div>
                </div>
                <table class="enquiries-table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Batch</th>
                            <th>Phone</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>ved</td>
                            <td>2514534412</td>
                            <td>Tony Stark</td>
                            <td><span class="status-completed">COMPLETED</span></td>
                            <td>
                                <button class="view-button" onclick="viewEnquiry(1)">VIEW</button>
                                <button class="delete-button" onclick="deleteEnquiry(1)">DELETE</button>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>om</td>
                            <td>321343412</td>
                            <td>Pepper Pots</td>
                            <td><span class="status-pending">PENDING</span></td>
                            <td>
                                <button class="view-button" onclick="viewEnquiry(2)">VIEW</button>
                                <button class="delete-button" onclick="deleteEnquiry(2)">DELETE</button>
                            </td>
                        </tr>
                        </tbody>
                </table>
            </div>
        `;
        settingsContent.innerHTML = enquiriesHTML;

        //  Fetch and populate table data (example)
        fetchEnquiriesData();
    }

    function fetchEnquiriesData() {
        //  Replace with your actual API endpoint
        fetch('get_enquiries.php')
            .then(response => response.json())
            .then(data => {
                populateEnquiriesTable(data);
            })
            .catch(error => console.error('Error fetching enquiries:', error));
    }

    function populateEnquiriesTable(enquiries) {
        const tableBody = document.querySelector('.enquiries-table tbody');
        tableBody.innerHTML = ''; // Clear existing rows

        enquiries.forEach(enquiry => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${enquiry.id}</td>
                <td>${enquiry.name}</td>
                <td>${enquiry.phone}</td>
                <td>${enquiry.service}</td>
                <td><span class="status-${enquiry.status.toLowerCase()}">${enquiry.status}</span></td>
                <td>
                    <button class="view-button" onclick="viewEnquiry(${enquiry.id})">VIEW</button>
                    <button class="delete-button" onclick="deleteEnquiry(${enquiry.id})">DELETE</button>
                </td>
            `;
            tableBody.appendChild(row);
        });
    }

    window.viewEnquiry = function(id) {
        alert(`View enquiry ${id}`);
        //  Implement view functionality (e.g., show details in a modal)
    };

    window.deleteEnquiry = function(id) {
        alert(`Delete enquiry ${id}`);
        //  Implement delete functionality (e.g., confirm and send a delete request)
    };
});</script>
</body>

</html>