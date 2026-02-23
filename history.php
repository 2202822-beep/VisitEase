<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
include 'db.php';

// FETCH HISTORY (Confirmed or Rejected, not Pending)
$sql = "SELECT * FROM bookings WHERE status != 'Pending' ORDER BY date DESC, time DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>History | VisitEase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #64748b;
            --sidebar-bg: #1e1b4b;
            --bg-light: #f1f5f9;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg-light); overflow-x: hidden; }

        /* ANIMATION */
        @keyframes fadeInSlide {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .main-content { padding: 40px; animation: fadeInSlide 0.6s ease-out forwards; }

        /* SIDEBAR */
        .sidebar { background: linear-gradient(180deg, var(--sidebar-bg) 0%, #312e81 100%); min-height: 100vh; padding: 30px 20px; color: white; }
        .brand-title { font-weight: 800; letter-spacing: 1px; margin-bottom: 50px; }
        .nav-link { color: rgba(255,255,255,0.7); padding: 14px 20px; border-radius: 12px; margin-bottom: 8px; font-weight: 600; transition: all 0.3s; display: flex; align-items: center; }
        .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.1); color: white; transform: translateX(5px); }
        .nav-link i { width: 24px; font-size: 1.1rem; }

        /* TABLE */
        .table-card { background: white; border-radius: 20px; box-shadow: 0 10px 30px -10px rgba(0,0,0,0.08); overflow: hidden; border: none; }
        .table-header { padding: 25px; border-bottom: 2px solid #f1f5f9; }
        .table thead th { background: #f8fafc; color: var(--secondary-color); font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; padding: 18px; border: none; }
        .table td { padding: 18px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
        
        /* STATUS BADGES */
        .badge-confirmed { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; padding: 6px 12px; border-radius: 20px; }
        .badge-rejected { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; padding: 6px 12px; border-radius: 20px; }
        .token-text { font-family: monospace; background: #f1f5f9; padding: 4px 8px; border-radius: 6px; color: #334155; font-weight: 600; }
    </style>
</head>
<body>

<div class="container-fluid p-0">
    <div class="row g-0">
        <div class="col-md-2 sidebar d-none d-lg-block">
            <div class="brand-title h4 text-center">VISIT<span>EASE</span></div>
            <div class="nav flex-column">
                <a href="admin.php" class="nav-link"><i class="fas fa-th-large"></i> Dashboard</a>
                <a href="manage_schedule.php" class="nav-link"><i class="fas fa-calendar-alt"></i> Schedule</a>
                <a href="visitors.php" class="nav-link"><i class="fas fa-users"></i> Visitors</a>
                <a href="history.php" class="nav-link active"><i class="fas fa-history"></i> History</a>
                <a href="logout.php" class="nav-link text-danger mt-5"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>

        <div class="col-md-10 main-content">
            <div class="mb-5">
                <h2 class="fw-bold" style="color: #1e293b;">Booking History</h2>
                <p class="text-secondary">Archives of past, confirmed, and rejected visits.</p>
            </div>

            <div class="table-card">
                <div class="table-header">
                    <h5 class="m-0 fw-bold text-dark"><i class="fas fa-archive me-2 text-primary"></i> Records</h5>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Token</th>
                                <th>Visitor</th>
                                <th>Date Visited</th>
                                <th>Pax</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><span class="token-text">#<?php echo $row['token']; ?></span></td>
                                        <td>
                                            <div class="fw-bold text-dark"><?php echo $row['name']; ?></div>
                                            <div class="small text-muted"><?php echo $row['email']; ?></div>
                                        </td>
                                        <td>
                                            <div class="fw-bold"><?php echo date('M d, Y', strtotime($row['date'])); ?></div>
                                            <div class="small text-muted"><?php echo date('h:i A', strtotime($row['time'])); ?></div>
                                        </td>
                                        <td class="fw-bold"><?php echo $row['guests']; ?></td>
                                        <td>
                                            <?php if ($row['status'] == 'Confirmed'): ?>
                                                <span class="badge-confirmed"><i class="fas fa-check-circle me-1"></i> Confirmed</span>
                                            <?php else: ?>
                                                <span class="badge-rejected"><i class="fas fa-times-circle me-1"></i> Rejected</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center p-5 text-muted">No history records found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>