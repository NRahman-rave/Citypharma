<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
<div class="container-fluid">

    <a class="navbar-brand" href="staff_dashboard.php"> Medico</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">

        <ul class="navbar-nav me-auto">

            <li class="nav-item">
                <a class="nav-link" href="staff_dashboard.php"> Dashboard</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="dispense.php"> Dispense Medicine</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="prescriptions.php"> Prescriptions</a>
            </li>

            <li class="nav-item">
<a class="nav-link" href="dispensing_history.php">
Dispensing History
</a>
</li>

        </ul>


        <ul class="navbar-nav">

            <li class="nav-item dropdown">

                <a class="nav-link dropdown-toggle" href="#" role="button"
                   data-bs-toggle="dropdown">

                   👤 <?php echo $_SESSION['name']; ?>

                </a>

                <ul class="dropdown-menu dropdown-menu-end">

                    <li>
                        <a class="dropdown-item" href="logout.php">
                            Logout
                        </a>
                    </li>

                </ul>

            </li>

        </ul>

    </div>

</div>
</nav>