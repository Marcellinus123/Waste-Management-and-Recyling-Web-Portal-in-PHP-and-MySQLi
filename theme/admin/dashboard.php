<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
include_once('db.php');

// Check if user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['usertype'] !== 'admin_user') {
    header("Location: userlogin");
    exit();
}

// Initialize variables
$toastMessage = '';
$toastType = '';
$adminStats = [
    'total_users' => 0,
    'active_bookings' => 0,
    'completed_bookings' => 0,
    'vehicles_available' => 0
];
$recentBookings = [];
$recentUsers = [];

// Get admin statistics
try {
    // Total users count
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users");
    $stmt->execute();
    $adminStats['total_users'] = $stmt->fetchColumn();
    
    // Active bookings count
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE status IN ('not_approved', 'approved')");
    $stmt->execute();
    $adminStats['active_bookings'] = $stmt->fetchColumn();
    
    // Completed bookings count
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE status = 'completed'");
    $stmt->execute();
    $adminStats['completed_bookings'] = $stmt->fetchColumn();
    
    // Available vehicles count
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM vehicles WHERE vehicle_status = 'Available'");
    $stmt->execute();
    $adminStats['vehicles_available'] = $stmt->fetchColumn();
    
    // Get monthly booking data for the chart
    $currentYear = date('Y');
    $monthlyData = [];
    for ($i = 1; $i <= 12; $i++) {
        $month = str_pad($i, 2, '0', STR_PAD_LEFT);
        $stmt = $pdo->prepare("SELECT SUM(amount) FROM bookings 
                              WHERE YEAR(collection_date) = ? 
                              AND MONTH(collection_date) = ?");
        $stmt->execute([$currentYear, $i]);
        $monthlyData[] = (float)$stmt->fetchColumn();
    }
    
    // Get booking status distribution for pie chart
    $statusData = [];
    $stmt = $pdo->prepare("SELECT status, COUNT(*) as count FROM bookings GROUP BY status");
    $stmt->execute();
    $statusResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($statusResults as $row) {
        $statusData[] = [
            'value' => $row['count'],
            'name' => ucfirst(str_replace('_', ' ', $row['status']))
        ];
    }
    
    // Get recent bookings
    $stmt = $pdo->prepare("SELECT b.*, u.first_name, u.last_name 
                          FROM bookings b
                          JOIN users u ON b.user_id = u.user_id
                          ORDER BY b.created_at DESC 
                          LIMIT 5");
    $stmt->execute();
    $recentBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get recent users
    $stmt = $pdo->prepare("SELECT * FROM users 
                          ORDER BY created_at DESC 
                          LIMIT 5");
    $stmt->execute();
    $recentUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $toastMessage = "Error loading statistics: " . $e->getMessage();
    $toastType = "error";
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include("head.php")?>
<body class="app sidebar-mini">
    <!-- Navbar-->
    <?php include("header.php")?>
    <!-- Sidebar menu-->
    <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
    <?php include("aside.php")?>
    <main class="app-content">
      <div class="app-title">
        <div>
          <h1><i class="bi bi-speedometer"></i> Admin Dashboard</h1>
          <p>Overview of Waste Management System</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
          <li class="breadcrumb-item"><i class="bi bi-house-door fs-6"></i></li>
          <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
        </ul>
      </div>
      
      <!-- Toast Notification -->
      <?php if ($toastMessage): ?>
      <div class="alert alert-<?php echo $toastType === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
        <?php echo $toastMessage; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <?php endif; ?>
      
      <div class="row">
        <!-- 1. Total Users -->
        <div class="col-md-6 col-lg-3">
          <div class="widget-small primary coloured-icon">
            <i class="icon bi bi-people-fill fs-1"></i>
            <div class="info">
              <h4>Users</h4>
              <p><b><?php echo $adminStats['total_users']; ?></b> Total</p>
            </div>
          </div>
        </div>

        <!-- 2. Active Bookings -->
        <div class="col-md-6 col-lg-3">
          <div class="widget-small info coloured-icon">
            <i class="icon bi bi-calendar-check fs-1"></i>
            <div class="info">
              <h4>Bookings</h4>
              <p><b><?php echo $adminStats['active_bookings']; ?></b> Active</p>
            </div>
          </div>
        </div>

        <!-- 3. Completed Bookings -->
        <div class="col-md-6 col-lg-3">
          <div class="widget-small warning coloured-icon">
            <i class="icon bi bi-check-circle-fill fs-1"></i>
            <div class="info">
              <h4>Completed</h4>
              <p><b><?php echo $adminStats['completed_bookings']; ?></b> Bookings</p>
            </div>
          </div>
        </div>

        <!-- 4. Available Vehicles -->
        <div class="col-md-6 col-lg-3">
          <div class="widget-small danger coloured-icon">
            <i class="icon bi bi-truck fs-1"></i>
            <div class="info">
              <h4>Vehicles</h4>
              <p><b><?php echo $adminStats['vehicles_available']; ?></b> Available</p>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="tile">
            <h3 class="tile-title">Monthly Revenue - <?php echo date('Y'); ?></h3>
            <div class="ratio ratio-16x9">
              <div id="revenueChart"></div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="tile">
            <h3 class="tile-title">Booking Status Distribution</h3>
            <div class="ratio ratio-16x9">
              <div id="bookingStatusChart"></div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Recent Bookings Table -->
      <div class="row">
        <div class="col-md-6">
          <div class="tile">
            <h3 class="tile-title">Recent Bookings</h3>
            <div class="table-responsive">
              <table class="table table-hover table-bordered">
                <thead>
                  <tr>
                    <th>Code</th>
                    <th>User</th>
                    <th>Service</th>
                    <th>Amount</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($recentBookings as $booking): ?>
                  <tr>
                    <td><?php echo $booking['booking_code']; ?></td>
                    <td><?php echo $booking['first_name'] . ' ' . $booking['last_name']; ?></td>
                    <td><?php echo ucfirst($booking['service_type']); ?></td>
                    <td>GHS <?php echo number_format($booking['amount'], 2); ?></td>
                    <td>
                      <span class="badge bg-<?php 
                        switch($booking['status']) {
                          case 'completed': echo 'success'; break;
                          case 'approved': echo 'primary'; break;
                          case 'not_approved': echo 'warning'; break;
                          case 'rejected': echo 'danger'; break;
                          default: echo 'secondary';
                        }
                      ?>">
                        <?php echo ucfirst(str_replace('_', ' ', $booking['status'])); ?>
                      </span>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                  <?php if (empty($recentBookings)): ?>
                  <tr>
                    <td colspan="5" class="text-center">No recent bookings</td>
                  </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        
        <!-- Recent Users Table -->
        <div class="col-md-6">
          <div class="tile">
            <h3 class="tile-title">Recent Users</h3>
            <div class="table-responsive">
              <table class="table table-hover table-bordered">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Type</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($recentUsers as $user): ?>
                  <tr>
                    <td><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></td>
                    <td><?php echo $user['email']; ?></td>
                    <td><?php echo $user['phone']; ?></td>
                    <td><?php echo ucfirst(str_replace('_', ' ', $user['usertype'])); ?></td>
                    <td>
                      <span class="badge bg-<?php echo $user['account_status'] === 'active' ? 'success' : 'danger'; ?>">
                        <?php echo ucfirst($user['account_status']); ?>
                      </span>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                  <?php if (empty($recentUsers)): ?>
                  <tr>
                    <td colspan="5" class="text-center">No recent users</td>
                  </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </main>
    
    <!-- Essential javascripts for application to work-->
    <script src="../js/jquery-3.7.0.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/main.js"></script>
    <!-- Page specific javascripts-->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
    <script type="text/javascript">
      // Monthly Revenue Chart
      const revenueData = {
        xAxis: {
          type: 'category',
          data: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
        },
        yAxis: {
          type: 'value',
          axisLabel: {
            formatter: '₵{value}'
          }
        },
        series: [{
          data: <?php echo json_encode($monthlyData); ?>,
          type: 'line',
          smooth: true,
          itemStyle: {
            color: '#007bff'
          },
          areaStyle: {
            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
              { offset: 0, color: 'rgba(0, 123, 255, 0.5)' },
              { offset: 1, color: 'rgba(0, 123, 255, 0.1)' }
            ])
          }
        }],
        tooltip: {
          trigger: 'axis',
          formatter: function(params) {
            return `<b>${params[0].name}:</b> Ghc${params[0].value.toFixed(2)}`;
          }
        }
      };
      
      // Booking Status Pie Chart
      const bookingStatusData = {
        tooltip: {
          trigger: 'item',
          formatter: '{b}: {c} ({d}%)'
        },
        legend: {
          orient: 'vertical',
          left: 'left'
        },
        series: [{
          name: 'Booking Status',
          type: 'pie',
          radius: ['40%', '70%'],
          avoidLabelOverlap: false,
          itemStyle: {
            borderRadius: 10,
            borderColor: '#fff',
            borderWidth: 2
          },
          label: {
            show: false,
            position: 'center'
          },
          emphasis: {
            label: {
              show: true,
              fontSize: '18',
              fontWeight: 'bold'
            }
          },
          labelLine: {
            show: false
          },
          data: <?php echo json_encode($statusData); ?>
        }]
      };
      
      // Initialize charts
      document.addEventListener('DOMContentLoaded', function() {
        const revenueChartElement = document.getElementById('revenueChart');
        const revenueChart = echarts.init(revenueChartElement, null, { renderer: 'svg' });
        revenueChart.setOption(revenueData);
        
        const bookingStatusChartElement = document.getElementById("bookingStatusChart");
        const bookingStatusChart = echarts.init(bookingStatusChartElement, null, { renderer: 'svg' });
        bookingStatusChart.setOption(bookingStatusData);
        
        // Handle window resize
        window.addEventListener('resize', function() {
          revenueChart.resize();
          bookingStatusChart.resize();
        });
      });
    </script>
</body>
</html>