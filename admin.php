<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
include 'db.php';

// TINAWAG NATIN ANG BAGONG EMAIL HELPER (Pinalit sa PHPMailer)
require_once 'email_helper.php';

// ==========================================
// --- DATABASE ACTIONS & EMAIL SENDING ---
// ==========================================
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];
    
    $get_booking = $conn->query("SELECT * FROM bookings WHERE id = $id");
    if ($get_booking->num_rows > 0) {
        $booking = $get_booking->fetch_assoc();
        
        $visitor_email = $booking['email'];
        $visitor_name = $booking['name'];
        $booking_token = $booking['token'];
        $visit_date = date('F d, Y', strtotime($booking['visit_date']));
        $visit_time = $booking['visit_time'];
        $guests = $booking['guests'];
        
        if ($action == 'approve') {
            $conn->query("UPDATE bookings SET status = 'Confirmed' WHERE id = $id");
            
            $subject = 'VisitEase: Your Booking is CONFIRMED!';
            $body = "
            <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;'>
                <div style='background-color: #10b981; color: #fff; padding: 20px; text-align: center;'>
                    <h2 style='margin: 0;'>Booking Confirmed! ✅</h2>
                </div>
                <div style='padding: 20px; background-color: #fafaf9;'>
                    <h3 style='color: #1a2035;'>Hello $visitor_name,</h3>
                    <p>Magandang balita! Ang iyong booking sa VisitEase ay na-approve na ng aming admin.</p>
                    <div style='background-color: #fff; border-left: 4px solid #10b981; padding: 15px; margin: 20px 0;'>
                        <p style='margin: 0 0 10px; font-size: 14px; color: #777;'>YOUR ENTRANCE TOKEN</p>
                        <h1 style='margin: 0; color: #1a2035; letter-spacing: 2px;'>$booking_token</h1>
                    </div>
                    <p>Ipakita ang token na ito sa entrance sa araw ng iyong pagbisita.</p>
                    <ul style='list-style: none; padding: 0;'>
                        <li style='margin-bottom: 8px;'><b>Date:</b> $visit_date</li>
                        <li style='margin-bottom: 8px;'><b>Time:</b> $visit_time</li>
                        <li style='margin-bottom: 8px;'><b>Guests:</b> $guests Pax</li>
                    </ul>
                </div>
            </div>";
            
            // I-send ang email kapag approved
            if (!empty($visitor_email)) {
                sendSystemEmail($visitor_email, $subject, $body);
            }

        } elseif ($action == 'reject') {
            $conn->query("UPDATE bookings SET status = 'Rejected' WHERE id = $id");
            $sched_id = $booking['schedule_id'];
            $conn->query("UPDATE schedule_settings SET slots = slots + $guests WHERE id = '$sched_id'");
            
            $subject = 'VisitEase: Booking Update';
            $body = "
            <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;'>
                <div style='background-color: #ef4444; color: #fff; padding: 20px; text-align: center;'>
                    <h2 style='margin: 0;'>Booking Notice</h2>
                </div>
                <div style='padding: 20px; background-color: #fafaf9;'>
                    <h3 style='color: #1a2035;'>Hello $visitor_name,</h3>
                    <p>Ikinalulungkot naming ipaalam na hindi na-approve ang iyong booking (Token: <b>$booking_token</b>).</p>
                    <p>Ito ay maaaring dahil sa invalid na GCash Reference number o full capacity na ang napiling schedule. Mangyaring mag-book na lamang muli gamit ang tamang detalye.</p>
                </div>
            </div>";
            
            // I-send ang email kapag rejected
            if (!empty($visitor_email)) {
                sendSystemEmail($visitor_email, $subject, $body);
            }

        } elseif ($action == 'delete') {
            $conn->query("DELETE FROM bookings WHERE id = $id");
        }
    }

    header("Location: admin.php");
    exit();
}

// ==========================================
// --- ANALYTICS (GLOBAL) ---
// ==========================================
$total_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings")->fetch_assoc()['count'];
$total_served = $conn->query("SELECT SUM(guests) as total FROM bookings WHERE status = 'Confirmed'")->fetch_assoc()['total'] ?? 0;

$today = date('Y-m-d');
$today_visits = $conn->query("
    SELECT SUM(b.guests) as total FROM bookings b 
    LEFT JOIN schedule_settings s ON b.schedule_id = s.id 
    WHERE s.date = '$today' AND b.status = 'Confirmed'
")->fetch_assoc()['total'] ?? 0;

$upcoming_visits = $conn->query("
    SELECT SUM(b.guests) as total FROM bookings b 
    LEFT JOIN schedule_settings s ON b.schedule_id = s.id 
    WHERE s.date > '$today' AND b.status = 'Confirmed'
")->fetch_assoc()['total'] ?? 0;

$pending_count = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'Pending'")->fetch_assoc()['count'];

$sched_stats = $conn->query("SELECT SUM(max_slots) as total_capacity, SUM(max_slots - slots) as total_booked FROM schedule_settings")->fetch_assoc();
$range_total_slots = $sched_stats['total_capacity'] ?? 0;
$range_total_booked = $sched_stats['total_booked'] ?? 0;
$range_available = $range_total_slots - $range_total_booked;

// ==========================================
// --- CHART: BOOKINGS PER DAY (LAST 7 DAYS) ---
// ==========================================
$chart_labels = [];
$chart_data   = [];
for ($i = 6; $i >= 0; $i--) {
    $day   = date('Y-m-d', strtotime("-$i days"));
    $label = date('M d', strtotime($day));
    $count = $conn->query("SELECT COUNT(*) as c FROM bookings b LEFT JOIN schedule_settings s ON b.schedule_id = s.id WHERE s.date = '$day'")->fetch_assoc()['c'];
    $chart_labels[] = $label;
    $chart_data[]   = (int)$count;
}
$chart_labels_json = json_encode($chart_labels);
$chart_data_json   = json_encode($chart_data);

// ==========================================
// --- NOTIFICATIONS (LATEST PENDING) ---
// ==========================================
$notif_res = $conn->query("
    SELECT b.name, b.token, s.date as visit_date, s.start_time, b.id
    FROM bookings b
    LEFT JOIN schedule_settings s ON b.schedule_id = s.id
    WHERE b.status = 'Pending'
    ORDER BY b.id DESC
    LIMIT 5
");
$notifs = [];
while ($n = $notif_res->fetch_assoc()) $notifs[] = $n;
$notif_count = count($notifs);

// ==========================================
// --- PENDING BOOKINGS ---
// ==========================================
$result = $conn->query("
    SELECT b.*, s.date as visit_date, s.start_time, s.end_time 
    FROM bookings b 
    LEFT JOIN schedule_settings s ON b.schedule_id = s.id 
    WHERE b.status = 'Pending' 
    ORDER BY b.created_at DESC
");

// ==========================================
// --- ACTIVITY LOG (PAGINATION) ---
// ==========================================
$act_per_page = 5;
$act_page = isset($_GET['act_page']) ? max(1, intval($_GET['act_page'])) : 1;
$act_offset = ($act_page - 1) * $act_per_page;

// Combine bookings + schedule_settings into one activity feed using UNION
$act_count_res = $conn->query("
    SELECT COUNT(*) as total FROM (
        SELECT id FROM bookings
        UNION ALL
        SELECT id FROM schedule_settings
    ) combined
");
$act_total = $act_count_res->fetch_assoc()['total'];
$act_total_pages = ceil($act_total / $act_per_page);

$activities_res = $conn->query("
    SELECT * FROM (
        SELECT 
            'booking' as type,
            b.id as row_id,
            b.name,
            s.date as visit_date,
            b.schedule_id,
            b.status,
            b.guests
        FROM bookings b
        LEFT JOIN schedule_settings s ON b.schedule_id = s.id

        UNION ALL

        SELECT 
            'slot' as type,
            s.id as row_id,
            CONCAT('Slot: ', s.date) as name,
            s.date as visit_date,
            s.id as schedule_id,
            '' as status,
            s.max_slots as guests
        FROM schedule_settings s
    ) combined
    ORDER BY row_id DESC
    LIMIT $act_per_page OFFSET $act_offset
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>VisitEase Admin | Analytics Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --success: #10b981;
            --bg-body: #f8fafc;
            --sidebar-blue: #1e1b4b;
        }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg-body); color: #1e293b; }
        .sidebar { background: var(--sidebar-blue); min-height: 100vh; padding: 25px 15px; color: white; }
        .nav-link { color: rgba(255,255,255,0.7); padding: 12px 15px; border-radius: 8px; margin-bottom: 5px; display: flex; align-items: center; gap: 10px; text-decoration: none; font-weight: 500; }
        .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.1); color: white; }
        .stat-card { background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); height: 100%; transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-2px); }
        .icon-box { width: 45px; height: 45px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }

        /* ── PENDING + ACTIVITY TWO-COLUMN WRAPPER ── */
        .bottom-section { display: grid; grid-template-columns: 1fr 340px; gap: 24px; margin-top: 30px; align-items: start; }

        /* ── PENDING TABLE ── */
        .table-wrap { background: white; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .table thead th { background: #f1f5f9; color: #64748b; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 15px 20px; border: none; }
        .table td { padding: 15px 20px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
        .btn-approve { background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .btn-reject { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .btn-view { background-color: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
        .main-content { padding: 40px; }

        /* ── ACTIVITY LOG PANEL ── */
        .activity-panel {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.08);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .activity-header {
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .activity-header h5 { margin: 0; font-weight: 700; font-size: .95rem; }
        .activity-header small { color: #94a3b8; font-size: .75rem; }
        .activity-list { padding: 8px 0; flex: 1; }
        .activity-item {
            display: flex;
            align-items: flex-start;
            gap: 11px;
            padding: 11px 18px;
            border-bottom: 1px solid #f8fafc;
            transition: background .15s;
        }
        .activity-item:last-child { border-bottom: none; }
        .activity-item:hover { background: #f8fafc; }
        .act-icon {
            width: 30px; height: 30px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: .7rem; flex-shrink: 0; margin-top: 2px;
        }
        .act-icon.booking  { background: #dcfce7; color: #16a34a; }
        .act-icon.slot     { background: #fef9c3; color: #ca8a04; }
        .act-icon.rejected { background: #fee2e2; color: #dc2626; }
        .act-icon.approved { background: #dbeafe; color: #2563eb; }
        .act-body { flex: 1; min-width: 0; }
        .act-title {
            font-size: .78rem; font-weight: 600; color: #1e293b;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            max-width: 240px;
        }
        .act-meta { font-size: .7rem; color: #94a3b8; margin-top: 2px; }
        .act-badge {
            font-size: .62rem; font-weight: 700; padding: 2px 7px;
            border-radius: 20px; white-space: nowrap; flex-shrink: 0;
            margin-top: 3px;
        }
        .act-badge.new    { background: #fef9c3; color: #92400e; }
        .act-badge.slot   { background: #ede9fe; color: #6d28d9; }
        .act-badge.ok     { background: #dcfce7; color: #15803d; }
        .act-badge.rej    { background: #fee2e2; color: #991b1b; }

        /* pagination */
        .act-pagination {
            padding: 12px 18px;
            border-top: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: .75rem;
            color: #64748b;
        }
        .act-pagination .pg-info { font-size:.72rem; }
        .act-pagination .pg-btns { display:flex; gap:6px; }
        .pg-btn {
            padding: 4px 12px; border-radius: 6px; border: 1px solid #e2e8f0;
            background: #fff; color: #475569; font-size: .72rem; font-weight: 600;
            cursor: pointer; text-decoration: none; transition: all .15s;
        }
        .pg-btn:hover:not(.disabled) { background: var(--primary); color: #fff; border-color: var(--primary); }
        .pg-btn.disabled { opacity: .4; pointer-events: none; }
        .pg-per { font-size: .72rem; color: #94a3b8; }

        /* receipt modal */
        .receipt-image-container {
            width: 100%; height: 250px; border-radius: 8px; overflow: hidden;
            border: 1px solid #cbd5e1; background: #f1f5f9;
            display: flex; align-items: center; justify-content: center;
        }
        .receipt-image-container img { max-width: 100%; max-height: 100%; object-fit: contain; cursor: pointer; }

        /* ── NOTIFICATION BELL ── */
        .notif-wrapper { position: relative; }
        .notif-btn {
            width: 40px; height: 40px; border-radius: 10px;
            background: #fff; border: 1px solid #e2e8f0;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: #475569; font-size: 1.1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,.08);
            position: relative; transition: all .2s;
        }
        .notif-btn:hover { background: #f1f5f9; color: var(--primary); }
        .notif-dot {
            position: absolute; top: 6px; right: 6px;
            width: 9px; height: 9px; background: #ef4444;
            border-radius: 50%; border: 2px solid #fff;
            animation: pulse-dot 1.8s ease-in-out infinite;
        }
        @keyframes pulse-dot {
            0%,100% { transform: scale(1); opacity: 1; }
            50%      { transform: scale(1.3); opacity: .75; }
        }
        .notif-dropdown {
            display: none; position: absolute; top: calc(100% + 10px); right: 0;
            width: 320px; background: #fff; border-radius: 12px;
            border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(0,0,0,.12);
            z-index: 9999; overflow: hidden;
        }
        .notif-dropdown.show { display: block; }
        .notif-drop-header {
            padding: 14px 18px; border-bottom: 1px solid #f1f5f9;
            display: flex; justify-content: space-between; align-items: center;
        }
        .notif-drop-header h6 { margin: 0; font-weight: 700; font-size: .85rem; }
        .notif-drop-header .badge { font-size: .7rem; }
        .notif-item {
            display: flex; align-items: flex-start; gap: 12px;
            padding: 12px 18px; border-bottom: 1px solid #f8fafc;
            transition: background .15s; text-decoration: none; color: inherit;
        }
        .notif-item:hover { background: #f8fafc; }
        .notif-item:last-child { border-bottom: none; }
        .notif-avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background: #eef2ff; color: var(--primary);
            display: flex; align-items: center; justify-content: center;
            font-size: .75rem; flex-shrink: 0; font-weight: 700;
        }
        .notif-item-body .notif-name { font-size: .8rem; font-weight: 600; color: #1e293b; }
        .notif-item-body .notif-sub  { font-size: .72rem; color: #94a3b8; margin-top: 2px; }
        .notif-drop-footer {
            padding: 10px 18px; text-align: center;
            border-top: 1px solid #f1f5f9; font-size: .78rem;
            color: var(--primary); font-weight: 600; cursor: pointer;
        }
        .notif-drop-footer:hover { background: #f8fafc; }
        .notif-empty { padding: 24px; text-align: center; color: #94a3b8; font-size: .82rem; }

        /* ── CHART CARD ── */
        .chart-card {
            background: #fff; border-radius: 12px; border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,.08); padding: 24px;
            margin-bottom: 28px;
        }
        .chart-card-header {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;
        }
        .chart-card-header h5 { margin: 0; font-weight: 700; font-size: .95rem; }
        .chart-card-header small { color: #94a3b8; font-size: .75rem; }
        .chart-container { position: relative; height: 220px; }

        @media(max-width:992px) {
            .bottom-section { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="container-fluid p-0">
    <div class="row g-0">
        <div class="col-md-2 sidebar d-none d-lg-block">
            <h4 class="fw-bold mb-5 px-3">VISITEASE</h4>
            <nav>
                <a href="admin.php" class="nav-link active"><i class="fas fa-grid-2"></i> Dashboard</a>
                <a href="manage_schedule.php" class="nav-link"><i class="fas fa-calendar"></i> Schedule</a>
                <a href="visitors.php" class="nav-link"><i class="fas fa-users"></i> Visitors</a>
                <a href="history.php" class="nav-link"><i class="fas fa-history"></i> History</a>
                <a href="logout.php" class="nav-link text-danger mt-5"><i class="fas fa-power-off"></i> Logout</a>
            </nav>
        </div>

        <div class="col-md-10 main-content">
            
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h2 class="fw-bold m-0">Overview</h2>
                    <small class="text-muted">Global Analytics</small>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="notif-wrapper" id="notifWrapper">
                        <div class="notif-btn" id="notifBtn" onclick="toggleNotif(event)">
                            <i class="fas fa-bell"></i>
                            <?php if ($notif_count > 0): ?>
                                <span class="notif-dot"></span>
                            <?php endif; ?>
                        </div>
                        <div class="notif-dropdown" id="notifDropdown">
                            <div class="notif-drop-header">
                                <h6>Notifications</h6>
                                <?php if ($notif_count > 0): ?>
                                    <span class="badge bg-danger"><?php echo $notif_count; ?> Pending</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">No new</span>
                                <?php endif; ?>
                            </div>
                            <?php if ($notif_count > 0): ?>
                                <?php foreach ($notifs as $n):
                                    $n_date = $n['visit_date'] ? date('M d, Y', strtotime($n['visit_date'])) : 'No date';
                                    $n_time = $n['start_time'] ? date('h:i A', strtotime($n['start_time'])) : '';
                                    $initials = strtoupper(substr($n['name'], 0, 1));
                                ?>
                                <div class="notif-item">
                                    <div class="notif-avatar"><?php echo $initials; ?></div>
                                    <div class="notif-item-body">
                                        <div class="notif-name">📋 <?php echo htmlspecialchars($n['name']); ?> booked a visit</div>
                                        <div class="notif-sub">📅 <?php echo $n_date; ?> <?php echo $n_time ? '· ' . $n_time : ''; ?> &nbsp;·&nbsp; Token: <strong>#<?php echo $n['token']; ?></strong></div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <div class="notif-drop-footer" onclick="window.location='admin.php'">
                                    View all pending requests →
                                </div>
                            <?php else: ?>
                                <div class="notif-empty">
                                    <i class="fas fa-check-circle text-success mb-2" style="font-size:1.5rem;display:block"></i>
                                    No pending notifications
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <button class="btn btn-primary px-4 fw-bold shadow-sm" style="border-radius:8px;" data-bs-toggle="modal" data-bs-target="#addSlotModal">
                        <i class="fas fa-plus me-2"></i>NEW SLOT
                    </button>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="stat-card" style="border-left:5px solid var(--primary);">
                        <div class="d-flex justify-content-between">
                            <div><p class="text-muted small mb-1 fw-bold text-uppercase">Total Capacity</p><h3 class="fw-bold text-dark"><?php echo number_format($range_total_slots); ?></h3></div>
                            <div class="icon-box bg-light text-primary"><i class="fas fa-layer-group"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card" style="border-left:5px solid #0dcaf0;">
                        <div class="d-flex justify-content-between">
                            <div><p class="text-muted small mb-1 fw-bold text-uppercase">Total Booked</p><h3 class="fw-bold text-info"><?php echo number_format($range_total_booked); ?></h3></div>
                            <div class="icon-box bg-light text-info"><i class="fas fa-users"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card" style="border-left:5px solid var(--success);">
                        <div class="d-flex justify-content-between">
                            <div><p class="text-muted small mb-1 fw-bold text-uppercase">Available Slots</p><h3 class="fw-bold text-success"><?php echo number_format($range_available); ?></h3></div>
                            <div class="icon-box bg-light text-success"><i class="fas fa-ticket-alt"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between">
                            <div><p class="text-muted small mb-1 fw-bold">BOOKINGS</p><h3 class="fw-bold"><?php echo $total_bookings; ?></h3></div>
                            <div class="icon-box" style="background:#eef2ff;color:var(--primary);"><i class="fas fa-book"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between">
                            <div><p class="text-muted small mb-1 fw-bold">SERVED</p><h3 class="fw-bold text-success"><?php echo $total_served; ?></h3></div>
                            <div class="icon-box" style="background:#ecfdf5;color:var(--success);"><i class="fas fa-check-circle"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card bg-light border-0">
                        <div class="d-flex justify-content-between">
                            <div><p class="text-muted small mb-1 fw-bold">TODAY'S VISITS</p><h3 class="fw-bold text-dark"><?php echo $today_visits; ?></h3></div>
                            <div class="icon-box bg-white text-dark shadow-sm"><i class="fas fa-walking"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card bg-light border-0">
                        <div class="d-flex justify-content-between">
                            <div><p class="text-muted small mb-1 fw-bold">UPCOMING</p><h3 class="fw-bold text-primary"><?php echo $upcoming_visits; ?></h3></div>
                            <div class="icon-box bg-white text-primary shadow-sm"><i class="fas fa-calendar-alt"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="stat-card bg-warning bg-opacity-10 border-warning">
                        <p class="text-warning small mb-1 fw-bold">PENDING REQUESTS</p>
                        <h3 class="fw-bold text-warning"><?php echo $pending_count; ?></h3>
                        <span class="small fw-bold">Requires Attention</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card bg-success bg-opacity-10 border-success">
                        <p class="text-success small mb-1 fw-bold">SYSTEM STATUS</p>
                        <h3 class="fw-bold text-success">ONLINE</h3>
                        <span class="small fw-bold">All systems normal</span>
                    </div>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-card-header">
                    <div>
                        <h5><i class="fas fa-chart-bar text-primary me-2"></i>Bookings Overview</h5>
                        <small>Total bookings per day — last 7 days</small>
                    </div>
                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold" style="font-size:.72rem">Last 7 Days</span>
                </div>
                <div class="chart-container">
                    <canvas id="bookingsChart"></canvas>
                </div>
            </div>

            <div class="bottom-section">

                <div class="table-wrap">
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="m-0 fw-bold">Pending Requests</h5>
                        <span class="badge bg-primary">Showing All</span>
                    </div>
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Token</th>
                                <th>Visitor</th>
                                <th>Schedule</th>
                                <th>Pax</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): 
                                    $v_date = $row['visit_date'] ? date('M d, Y', strtotime($row['visit_date'])) : '<span class="text-danger">No Date Set</span>';
                                    $v_time = $row['start_time'] ? date('h:i A', strtotime($row['start_time'])) : '';
                                    $receipt_path = !empty($row['gcash_receipt']) ? 'uploads/receipts/' . $row['gcash_receipt'] : '';
                                ?>
                                <tr>
                                    <td><span class="badge bg-light text-primary border px-2 py-2">#<?php echo $row['token']; ?></span></td>
                                    <td>
                                        <div class="fw-bold"><?php echo htmlspecialchars($row['name']); ?></div>
                                        <div class="small text-muted"><?php echo htmlspecialchars($row['email']); ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?php echo $v_date; ?></div>
                                        <div class="small text-primary fw-bold"><?php echo $v_time; ?></div>
                                    </td>
                                    <td class="fw-bold fs-5"><?php echo $row['guests']; ?></td>
                                    <td><span class="badge bg-warning text-dark fw-bold">PENDING</span></td>
                                    <td class="text-end">
                                        <button class="btn btn-view btn-sm me-1" onclick="viewDetails(
                                            '<?php echo addslashes($row['name']); ?>', 
                                            '<?php echo addslashes($row['gcash_name']); ?>', 
                                            '<?php echo addslashes($row['gcash_ref']); ?>', 
                                            '<?php echo addslashes($row['special_request'] ?? ''); ?>',
                                            '<?php echo $v_date; ?>',
                                            '<?php echo $v_time; ?>',
                                            '<?php echo addslashes($receipt_path); ?>'
                                        )">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        <a href="admin.php?action=approve&id=<?php echo $row['id']; ?>" class="btn btn-approve btn-sm me-1">
                                            <i class="fas fa-check"></i>
                                        </a>
                                        <a href="admin.php?action=reject&id=<?php echo $row['id']; ?>" class="btn btn-reject btn-sm">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center py-5 text-muted">Walang pending na request sa ngayon.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="activity-panel">
                    <div class="activity-header">
                        <div>
                            <h5>Recent Activity</h5>
                            <small>Latest system activity</small>
                        </div>
                        <i class="fas fa-circle-info text-muted" style="font-size:.8rem" title="Shows bookings and slot creation events"></i>
                    </div>

                    <div class="activity-list">
                        <?php if ($activities_res && $activities_res->num_rows > 0): ?>
                            <?php while ($act = $activities_res->fetch_assoc()): 
                                $act_date_label = $act['visit_date'] ? date('D, M d', strtotime($act['visit_date'])) : '';

                                if ($act['type'] === 'slot') {
                                    $icon_class = 'slot';
                                    $icon = 'fas fa-calendar-plus';
                                    $badge_class = 'slot';
                                    $badge_label = 'NEW SLOT';
                                    $title = 'NEW VISIT SLOT CREATED: ' . $act_date_label;
                                    $meta = 'System';
                                } elseif ($act['status'] === 'Confirmed') {
                                    $icon_class = 'approved';
                                    $icon = 'fas fa-check-circle';
                                    $badge_class = 'ok';
                                    $badge_label = 'CONFIRMED';
                                    $title = 'BOOKING APPROVED: ' . htmlspecialchars($act['name']) . ' · ' . $act_date_label;
                                    $meta = 'System';
                                } elseif ($act['status'] === 'Rejected') {
                                    $icon_class = 'rejected';
                                    $icon = 'fas fa-times-circle';
                                    $badge_class = 'rej';
                                    $badge_label = 'REJECTED';
                                    $title = 'BOOKING REJECTED: ' . htmlspecialchars($act['name']);
                                    $meta = 'System';
                                } else {
                                    $icon_class = 'booking';
                                    $icon = 'fas fa-bookmark';
                                    $badge_class = 'new';
                                    $badge_label = 'NEW';
                                    $title = 'NEW BOOKING CREATED: ' . htmlspecialchars($act['name']) . ' has booked a visit for ' . $act_date_label;
                                    $meta = 'System';
                                }
                            ?>
                            <div class="activity-item">
                                <div class="act-icon <?php echo $icon_class; ?>">
                                    <i class="<?php echo $icon; ?>"></i>
                                </div>
                                <div class="act-body">
                                    <div class="act-title" title="<?php echo htmlspecialchars($title); ?>"><?php echo $title; ?></div>
                                    <div class="act-meta"><?php echo $meta; ?></div>
                                </div>
                                <span class="act-badge <?php echo $badge_class; ?>"><?php echo $badge_label; ?></span>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="text-center py-4 text-muted" style="font-size:.82rem">No activity yet.</div>
                        <?php endif; ?>
                    </div>

                    <div class="act-pagination">
                        <span class="pg-info">
                            Page <strong><?php echo $act_page; ?></strong> of <?php echo max(1, $act_total_pages); ?>
                            &nbsp;·&nbsp; <?php echo $act_total; ?> total
                        </span>
                        <div class="pg-btns">
                            <a href="?act_page=<?php echo $act_page - 1; ?>" class="pg-btn <?php echo $act_page <= 1 ? 'disabled' : ''; ?>">Prev</a>
                            <a href="?act_page=<?php echo $act_page + 1; ?>" class="pg-btn <?php echo $act_page >= $act_total_pages ? 'disabled' : ''; ?>">Next</a>
                        </div>
                    </div>
                </div></div></div></div>
</div>

<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="fw-bold m-0">Booking Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-md-6 border-end pe-4">
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="text-muted small fw-bold">VISIT DATE</label>
                                <input type="text" id="modalVisitDate" class="form-control fw-bold bg-white" readonly>
                            </div>
                            <div class="col-6">
                                <label class="text-muted small fw-bold">VISIT TIME</label>
                                <input type="text" id="modalVisitTime" class="form-control fw-bold bg-white" readonly>
                            </div>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label class="text-muted small fw-bold">GCASH SENDER NAME</label>
                            <h5 id="modalGcashName" class="fw-bold text-dark">--</h5>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small fw-bold">GCASH REFERENCE NO.</label>
                            <h5 id="modalRef" class="fw-bold text-primary">--</h5>
                        </div>
                        <hr>
                        <div class="mb-0">
                            <label class="text-muted small fw-bold">SPECIAL REQUEST</label>
                            <p id="modalRequest" class="fst-italic text-secondary">--</p>
                        </div>
                    </div>
                    <div class="col-md-6 ps-4 d-flex flex-column">
                        <label class="text-muted small fw-bold mb-2">PAYMENT PROOF (RECEIPT)</label>
                        <div class="receipt-image-container flex-grow-1" id="receiptContainer">
                            <span class="text-muted small" id="noReceiptText" style="display:none;">No receipt uploaded</span>
                        </div>
                        <div class="mt-2 text-center">
                            <a href="#" id="viewFullReceiptBtn" target="_blank" class="btn btn-sm btn-outline-primary w-100" style="display:none;">
                                <i class="fas fa-external-link-alt me-1"></i> View Full Image
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addSlotModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="fw-bold m-0">Create New Schedule Slot</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="create_slot.php">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Date</label>
                        <input type="date" class="form-control" name="slot_date" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">Start Time</label>
                            <input type="time" name="slot_start_time" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">End Time</label>
                            <input type="time" name="slot_end_time" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Capacity (Max Guests)</label>
                        <input type="number" name="slot_capacity" class="form-control" value="20" required min="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Create Slot</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ── NOTIFICATION BELL TOGGLE ──
function toggleNotif(e) {
    e.stopPropagation();
    document.getElementById('notifDropdown').classList.toggle('show');
}
document.addEventListener('click', function(e) {
    const wrapper = document.getElementById('notifWrapper');
    if (wrapper && !wrapper.contains(e.target)) {
        document.getElementById('notifDropdown').classList.remove('show');
    }
});

// ── BOOKINGS CHART ──
const ctx = document.getElementById('bookingsChart').getContext('2d');
const bookingsChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo $chart_labels_json; ?>,
        datasets: [{
            label: 'Bookings',
            data: <?php echo $chart_data_json; ?>,
            backgroundColor: function(context) {
                const chart = context.chart;
                const { ctx: c, chartArea } = chart;
                if (!chartArea) return '#4361ee';
                const gradient = c.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                gradient.addColorStop(0, 'rgba(67,97,238,0.85)');
                gradient.addColorStop(1, 'rgba(67,97,238,0.15)');
                return gradient;
            },
            borderColor: '#4361ee',
            borderWidth: 0,
            borderRadius: 8,
            borderSkipped: false,
            hoverBackgroundColor: '#3f37c9',
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1e293b',
                titleFont: { family: 'Inter', size: 12 },
                bodyFont:  { family: 'Inter', size: 13, weight: '600' },
                padding: 10,
                cornerRadius: 8,
                callbacks: {
                    label: ctx => ` ${ctx.parsed.y} booking${ctx.parsed.y !== 1 ? 's' : ''}`
                }
            }
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: { font: { family: 'Inter', size: 11 }, color: '#94a3b8' }
            },
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1,
                    font: { family: 'Inter', size: 11 }, color: '#94a3b8',
                    callback: v => Number.isInteger(v) ? v : ''
                },
                grid: { color: '#f1f5f9', drawBorder: false }
            }
        }
    }
});
function viewDetails(name, gcashName, gcashRef, request, visitDate, visitTime, receiptPath) {
    document.getElementById('modalGcashName').innerText = gcashName;
    document.getElementById('modalRef').innerText = gcashRef;
    document.getElementById('modalRequest').innerText = request || "None provided";
    document.getElementById('modalVisitDate').value = visitDate;
    document.getElementById('modalVisitTime').value = visitTime;
    
    const container = document.getElementById('receiptContainer');
    const noReceiptText = document.getElementById('noReceiptText');
    const viewFullBtn = document.getElementById('viewFullReceiptBtn');
    
    const oldImg = container.querySelector('img');
    if (oldImg) oldImg.remove();
    
    if (receiptPath && receiptPath !== '') {
        noReceiptText.style.display = 'none';
        viewFullBtn.style.display = 'block';
        viewFullBtn.href = receiptPath;
        const img = document.createElement('img');
        img.src = receiptPath;
        img.alt = "GCash Receipt";
        img.onclick = function() { window.open(receiptPath, '_blank'); };
        container.appendChild(img);
    } else {
        noReceiptText.style.display = 'block';
        viewFullBtn.style.display = 'none';
    }

    new bootstrap.Modal(document.getElementById('detailsModal')).show();
}
</script>
</body>
</html>