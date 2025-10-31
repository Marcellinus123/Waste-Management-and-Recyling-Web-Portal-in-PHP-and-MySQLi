<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
include_once('db.php');
include("../libs/tcpdf-main/tcpdf.php");

// Check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    header("Location: userlogin");
    exit();
}

// Check if usertype is not 'waste_user'
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'waste_driver') {
    header('Location: userlogin');
    exit();
}

// Check if user_id is set and not null
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] === null) {
    header('Location: userlogin');
    exit();
}

  // Initialize variables
  $toastMessage = '';
  $toastType = '';
  $availableVehicles = [];
  $serviceCosts = [];
  $userStats = [
      'completed_bookings' => 0,
      'recycling_requests' => 0,
      'service_categories' => 0,
      'eco_points' => 0
  ];

// Get user statistics
try {
    // Completed bookings count
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE user_id = ? AND status = 'completed'");
    $stmt->execute([$_SESSION['user_id']]);
    $userStats['completed_bookings'] = $stmt->fetchColumn();
    
    // Recycling requests count
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE user_id = ? AND service_type = 'recycling'");
    $stmt->execute([$_SESSION['user_id']]);
    $userStats['recycling_requests'] = $stmt->fetchColumn();
    
    // Service categories count
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM services WHERE status = 'Active'");
    $stmt->execute();
    $userStats['service_categories'] = $stmt->fetchColumn();
    
    // Eco points
    $stmt = $pdo->prepare("SELECT eco_points FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $userStats['eco_points'] = $stmt->fetchColumn();
    
    // Get monthly booking data for the chart
    $currentYear = date('Y');
    $monthlyData = [];
    for ($i = 1; $i <= 12; $i++) {
        $month = str_pad($i, 2, '0', STR_PAD_LEFT);
        $stmt = $pdo->prepare("SELECT SUM(amount) FROM bookings 
                              WHERE user_id = ? 
                              AND YEAR(collection_date) = ? 
                              AND MONTH(collection_date) = ?");
        $stmt->execute([$_SESSION['user_id'], $currentYear, $i]);
        $monthlyData[] = (float)$stmt->fetchColumn();
    }
    
    // Get booking status distribution for pie chart
    $statusData = [];
    $stmt = $pdo->prepare("SELECT status, COUNT(*) as count FROM bookings 
                          WHERE user_id = ? 
                          GROUP BY status");
    $stmt->execute([$_SESSION['user_id']]);
    $statusResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    //get list of bookings

    $userBookings = [];
    $stmt = $pdo->prepare("SELECT * FROM bookings 
                          WHERE user_id = ? 
                          GROUP BY status");
    $stmt->execute([$_SESSION['user_id']]);
    $userBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);


    foreach ($statusResults as $row) {
        $statusData[] = [
            'value' => $row['count'],
            'name' => ucfirst(str_replace('_', ' ', $row['status']))
        ];
    }
    
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
          <h1><i class="bi bi-speedometer"></i> Driver's Dashboard</h1>
          <p>Book for Waste Collection and Recycle Your Waste with Ease</p>
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
        <!-- 1. Waste Collection Booking -->
        <div class="col-md-6 col-lg-3">
          <div class="widget-small primary coloured-icon">
            <i class="icon bi bi-car-front-fill fs-1"></i>
            <div class="info">
              <h4>Bookings</h4>
              <p><b><?php echo $userStats['completed_bookings']; ?></b> Completed</p>
            </div>
          </div>
        </div>

        <!-- 2. Recycling Requests -->
        <div class="col-md-6 col-lg-3">
          <div class="widget-small info coloured-icon">
            <i class="icon bi bi-recycle fs-1"></i>
            <div class="info">
              <h4>Recycling</h4>
              <p><b><?php echo $userStats['recycling_requests']; ?></b> Requests</p>
            </div>
          </div>
        </div>

        <!-- 3. Service Categories -->
        <div class="col-md-6 col-lg-3">
          <div class="widget-small warning coloured-icon">
            <i class="icon bi bi-trash3-fill fs-1"></i>
            <div class="info">
              <h4>Services</h4>
              <p><b><?php echo $userStats['service_categories']; ?></b> Categories</p>
            </div>
          </div>
        </div>

        <!-- 4. Rewards/Points -->
        <div class="col-md-6 col-lg-3">
          <div class="widget-small danger coloured-icon">
            <i class="icon bi bi-award-fill fs-1"></i>
            <div class="info">
              <h4>Eco-Points</h4>
              <p><b><?php echo $userStats['eco_points']; ?></b> Points</p>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="tile">
            <h3 class="tile-title">Monthly Spending - <?php echo date('Y'); ?></h3>
            <div class="ratio ratio-16x9">
              <div id="spendingChart"></div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="tile">
            <h3 class="tile-title">Booking Status</h3>
            <div class="ratio ratio-16x9">
              <div id="bookingStatusChart"></div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Recent Bookings Table -->
      <div class="row mt-4">
        <div class="col-md-12">
          <div class="tile">
            <h3 class="tile-title">Recent Bookings</h3>
            <div class="table-responsive">
              <table class="table table-hover table-bordered">
                <thead>
                  <tr>
                    <th>Booking Code</th>
                    <th>Service Type</th>
                    <th>Collection Date</th>
                    <th>Amount (GHS)</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($userBookings as $booking): ?>
                  <tr>
                    <td><?php echo $booking['booking_code']; ?></td>
                    <td><?php echo ucfirst(str_replace('_', ' ', $booking['service_type'])); ?></td>
                    <td><?php echo date('M j, Y', strtotime($booking['collection_date'])); ?></td>
                    <td><?php echo number_format($booking['amount'], 2); ?></td>
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
                    <td>
                        <a href="bookings/<?php echo $booking['booking_code']; ?>.pdf" class="btn btn-sm" Download>
                            <i class="bi bi-download"></i> Download
                        </a>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                  <?php if (empty($userBookings)): ?>
                  <tr>
                    <td colspan="6" class="text-center">No bookings found</td>
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
      // Monthly Spending Chart
      const spendingData = {
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
          type: 'bar',
          itemStyle: {
            color: '#28a745'
          },
          showBackground: true,
          backgroundStyle: {
            color: 'rgba(180, 180, 180, 0.2)'
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
        const spendingChartElement = document.getElementById('spendingChart');
        const spendingChart = echarts.init(spendingChartElement, null, { renderer: 'svg' });
        spendingChart.setOption(spendingData);
        
        const bookingStatusChartElement = document.getElementById("bookingStatusChart");
        const bookingStatusChart = echarts.init(bookingStatusChartElement, null, { renderer: 'svg' });
        bookingStatusChart.setOption(bookingStatusData);
        
        // Handle window resize
        window.addEventListener('resize', function() {
          spendingChart.resize();
          bookingStatusChart.resize();
        });
      });
    </script>
</body>
</html>