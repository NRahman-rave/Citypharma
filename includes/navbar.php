<?php if (session_status() === PHP_SESSION_NONE) {
    session_start();
}?>

<nav class="navbar navbar-expand-lg shadow-sm">
    <div class="container-fluid px-4">

        <!-- Logo -->
        <a class="navbar-brand fw-bold d-flex align-items-center" href="manager_dashboard.php">
             <span class="ms-2">CityPharma</span>
        </a>

        <!-- Mobile toggle -->
        <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#topNav">
            ☰
        </button>

        <!-- Navbar links -->
        <div class="collapse navbar-collapse" id="topNav">

            <ul class="navbar-nav ms-auto align-items-center">

                <li class="nav-item">
                    <a class="nav-link active-link" href="manager_dashboard.php"> Dashboard</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="medicines.php"> Medicines</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="suppliers.php"> Suppliers</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="batches.php"> Batches</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="purchase_orders.php"> Orders</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="reports.php"> Reports</a>
                </li>

                <li class="nav-item">
    <a class="nav-link" href="audit_log.php">
         Audit Log
    </a>
</li>


                <!-- User Dropdown -->
                <li class="nav-item dropdown ms-3">
                    <a class="nav-link dropdown-toggle user-menu" href="#" role="button" data-bs-toggle="dropdown">
                        👤 <?php echo $_SESSION['name']; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                    </ul>
                </li>

            </ul>

        </div>
    </div>
</nav>