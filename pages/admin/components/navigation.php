<!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <!-- Sidebar Start -->
    <aside class="left-sidebar">
      <!-- Sidebar scroll-->
      <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
          <a href="./dashboard.php" class="logo-img w-100">
            <?php include 'logo_display.php'; ?>
          </a>
          <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
            <i class="ti ti-x fs-8"></i>
          </div>
        </div>

        <!-- Sidebar navigation-->
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
          <ul id="sidebarnav">
            <li class="nav-small-cap">
              <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
              <span class="hide-menu">GENERAL</span>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="./dashboard.php" aria-expanded="false">
                <span>
                  <i class="ti ti-article"></i>
                </span>
                <span class="hide-menu">Dashboard</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="./recents.php" aria-expanded="false">
                <span>
                  <i class="ti ti-report-analytics"></i>
                </span>
                <span class="hide-menu">Recent Expense Records</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="./employees.php" aria-expanded="false">
                <span>
                  <i class="ti ti-users"></i>
                </span>
                <span class="hide-menu">Employee List</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="./expenses.php" aria-expanded="false">
                <span>
                  <i class="ti ti-category"></i>
                </span>
                <span class="hide-menu">Expenses</span>
              </a>
            </li>
            <?php 
              if (isset($_SESSION['loggedinasadmin'])){

            ?>
             <li class="sidebar-item">
              <a class="sidebar-link" href="./userlist.php" aria-expanded="false">
                <span>
                  <i class="ti ti-user"></i>
                </span>
                <span class="hide-menu">User List</span>
              </a>
            </li>
            <?php
              }
            ?>
           <!-- <li class="sidebar-item">
              <a class="sidebar-link" href="./supplier.php" aria-expanded="false">
                <span>
                    <i class="ti ti-chart-bar"></i>
                </span>
                <span class="hide-menu">Supplier</span>
              </a>
            </li> -->
            <li class="nav-small-cap">
              <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
              <span class="hide-menu">REPORTS</span>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="./records.php" aria-expanded="false">
                <span>
                  <i class="ti ti-report"></i>
                </span>
                <span class="hide-menu">All Expense Record</span>
              </a>
            </li>
          </ul>
        </nav>
        <!-- End Sidebar navigation -->
      </div>
      <!-- End Sidebar scroll-->
    </aside>
    <!--  Sidebar End -->