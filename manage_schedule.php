<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
include 'db.php';

// --- HANDLE DELETE ACTION ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM schedule_settings WHERE id = $id");
    header("Location: manage_schedule.php");
    exit();
}

// --- HANDLE MONTH NAVIGATION ---
if (isset($_GET['ym'])) {
    $ym = $_GET['ym'];
} else {
    $ym = date('Y-m');
}

$timestamp = strtotime($ym . '-01');
if ($timestamp === false) {
    $ym = date('Y-m');
    $timestamp = strtotime($ym . '-01');
}

$today = date('Y-m-d', time());
$html_title = date('F Y', $timestamp);
$prev = date('Y-m', mktime(0, 0, 0, date('m', $timestamp)-1, 1, date('Y', $timestamp)));
$next = date('Y-m', mktime(0, 0, 0, date('m', $timestamp)+1, 1, date('Y', $timestamp)));

$day_count = date('t', $timestamp);
$str = date('w', mktime(0, 0, 0, date('m', $timestamp), 1, date('Y', $timestamp)));

// --- GET SLOTS FOR CALENDAR ---
$slots = [];
$start_date = date('Y-m-01', $timestamp);
$end_date = date('Y-m-t', $timestamp);

$sql = "SELECT * FROM schedule_settings WHERE date BETWEEN '$start_date' AND '$end_date' ORDER BY start_time ASC";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $slots[$row['date']][] = $row;
}

// --- GET ALL UPCOMING SLOTS FOR THE LIST (BOTTOM PART) ---
$list_sql = "SELECT * FROM schedule_settings WHERE date >= '$today' ORDER BY date ASC, start_time ASC";
$list_result = $conn->query($list_sql);

// --- CALENDAR GENERATION LOGIC ---
$weeks = array();
$week = '';
$week .= str_repeat('<td class="empty-cell"></td>', $str);

for ($day = 1; $day <= $day_count; $day++, $str++) {
    $current_date = $ym . '-' . sprintf('%02d', $day);
    $is_today = ($today == $current_date) ? 'today-cell' : '';

    $week .= '<td class="day-cell ' . $is_today . '">';
    $week .= '<div class="day-number">' . $day . '</div>';
    
    $week .= '<div class="slots-container">';
    if (isset($slots[$current_date])) {
        foreach ($slots[$current_date] as $slot) {
            $time_display = date('h:i A', strtotime($slot['start_time']));
            $week .= '<div class="slot-pill" title="Capacity: '.$slot['max_slots'].'">';
            $week .= '<i class="fas fa-clock me-1"></i> ' . $time_display;
            $week .= '</div>';
        }
    }
    $week .= '</div>'; 
    $week .= '</td>';

    if ($str % 7 == 6 || $day == $day_count) {
        if ($day == $day_count) {
            $week .= str_repeat('<td class="empty-cell"></td>', 6 - ($str % 7));
        }
        $weeks[] = '<tr>' . $week . '</tr>';
        $week = '';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Schedule | VisitEase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --secondary: #3f37c9;
            --bg-body: #f8fafc; /* Medyo grayish para lumutang ang white cards */
            --sidebar-blue: #1e1b4b;
        }

        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--bg-body); color: #334155; }

        /* SIDEBAR */
        .sidebar { background: var(--sidebar-blue); min-height: 100vh; padding: 25px 15px; color: white; position: fixed; width: 16.66667%; z-index: 1000; }
        .nav-link { color: rgba(255,255,255,0.7); padding: 12px 15px; border-radius: 10px; margin-bottom: 5px; display: flex; align-items: center; gap: 12px; text-decoration: none; font-weight: 500; transition: all 0.3s; }
        .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.15); color: white; transform: translateX(5px); }

        .main-content { margin-left: 16.66667%; padding: 40px; }

        /* HEADER */
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .page-title { font-weight: 800; font-size: 1.8rem; color: #1e293b; letter-spacing: -0.5px; }
        
        /* BUTTONS */
        .btn-create {
            background: linear-gradient(135deg, #4361ee 0%, #3a56d4 100%);
            border: none; padding: 12px 25px; border-radius: 12px; font-weight: 600; color: white;
            box-shadow: 0 10px 20px rgba(67, 97, 238, 0.3); transition: 0.3s;
        }
        .btn-create:hover { transform: translateY(-2px); box-shadow: 0 15px 25px rgba(67, 97, 238, 0.4); color: white; }

        /* CALENDAR */
        .calendar-wrapper { background: white; border-radius: 24px; box-shadow: 0 10px 30px -5px rgba(0,0,0,0.05); padding: 30px; margin-bottom: 50px; }
        .calendar-controls { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .month-title { font-size: 1.5rem; font-weight: 700; color: var(--primary); }
        .btn-nav { background: #f1f5f9; border: none; padding: 10px 15px; border-radius: 12px; color: #64748b; transition: 0.2s; }
        .btn-nav:hover { background: var(--primary); color: white; }
        
        .table-calendar { width: 100%; table-layout: fixed; border-collapse: separate; border-spacing: 10px; }
        .table-calendar th { text-align: center; color: #94a3b8; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; padding-bottom: 15px; letter-spacing: 1px; }
        
        .day-cell {
            height: 120px; vertical-align: top; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 10px;
            transition: all 0.3s; position: relative;
        }
        .day-cell:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(67, 97, 238, 0.1); border-color: var(--primary); }
        .today-cell { background: linear-gradient(to bottom right, #eff6ff, #ffffff); border: 2px solid var(--primary); }
        .day-number { font-weight: 700; font-size: 1rem; color: #64748b; margin-bottom: 8px; }
        .today-cell .day-number { color: var(--primary); }

        .slots-container { display: flex; flex-direction: column; gap: 4px; overflow-y: auto; max-height: 80px; }
        .slot-pill {
            background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; font-size: 0.65rem; padding: 4px 8px;
            border-radius: 6px; font-weight: 500; display: flex; align-items: center;
        }

        /* --- ACTIVE SLOTS LIST STYLING (YUNG NAWALA) --- */
        .list-header { margin-bottom: 20px; font-weight: 700; font-size: 1.2rem; color: #334155; }
        .slot-card {
            background: white; border-radius: 16px; padding: 20px; margin-bottom: 15px;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #f1f5f9;
            transition: 0.2s;
        }
        .slot-card:hover { transform: translateX(5px); border-left: 5px solid var(--primary); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); }
        
        .date-box { text-align: center; min-width: 60px; }
        .date-box .month { display: block; font-size: 0.7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; }
        .date-box .day { display: block; font-size: 1.5rem; font-weight: 800; color: #1e293b; line-height: 1; }
        
        .info-group { display: flex; align-items: center; gap: 40px; flex-grow: 1; margin-left: 30px; }
        .info-item { display: flex; flex-direction: column; }
        .info-label { font-size: 0.7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 2px; }
        .info-value { font-weight: 600; color: #334155; font-size: 0.95rem; }
        
        .capacity-badge { background: #e0e7ff; color: var(--primary); padding: 5px 12px; border-radius: 20px; font-weight: 600; font-size: 0.85rem; }
        .status-badge { display: flex; align-items: center; gap: 6px; font-weight: 600; color: #10b981; font-size: 0.9rem; }
        .status-dot { width: 8px; height: 8px; background: #10b981; border-radius: 50%; }

        .btn-delete {
            width: 40px; height: 40px; border-radius: 10px; background: #fee2e2; color: #ef4444; border: none;
            display: flex; align-items: center; justify-content: center; transition: 0.2s;
        }
        .btn-delete:hover { background: #ef4444; color: white; }

        /* Scrollbar hide */
        .slots-container::-webkit-scrollbar { width: 0px; background: transparent; }
    </style>
</head>
<body>

<div class="container-fluid p-0">
    <div class="row g-0">
        <div class="col-md-2 sidebar d-none d-lg-block">
            <h4 class="fw-bold mb-5 px-3" style="letter-spacing: -1px;">VISITEASE</h4>
            <nav>
                <a href="admin.php" class="nav-link"><i class="fas fa-grid-2"></i> Dashboard</a>
                <a href="manage_schedule.php" class="nav-link active"><i class="fas fa-calendar"></i> Schedule</a>
                <a href="visitors.php" class="nav-link"><i class="fas fa-users"></i> Visitors</a>
                <a href="history.php" class="nav-link"><i class="fas fa-history"></i> History</a>
                <a href="logout.php" class="nav-link text-danger mt-5"><i class="fas fa-power-off"></i> Logout</a>
            </nav>
        </div>

        <div class="col-md-10 main-content">
            
            <div class="page-header">
                <div>
                    <h2 class="page-title">Schedule Manager</h2>
                    <p class="text-muted m-0">Organize museum slots and availability.</p>
                </div>
                <button class="btn btn-create" data-bs-toggle="modal" data-bs-target="#addSlotModal">
                    <i class="fas fa-plus me-2"></i> Create Slot
                </button>
            </div>

            <div class="calendar-wrapper">
                <div class="calendar-controls">
                    <a href="?ym=<?php echo $prev; ?>" class="btn btn-nav"><i class="fas fa-chevron-left"></i></a>
                    <div class="month-title"><?php echo $html_title; ?></div>
                    <a href="?ym=<?php echo $next; ?>" class="btn btn-nav"><i class="fas fa-chevron-right"></i></a>
                </div>

                <table class="table-calendar">
                    <thead>
                        <tr>
                            <th>Sun</th>
                            <th>Mon</th>
                            <th>Tue</th>
                            <th>Wed</th>
                            <th>Thu</th>
                            <th>Fri</th>
                            <th>Sat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($weeks as $week) { echo $week; } ?>
                    </tbody>
                </table>
            </div>

            <h4 class="list-header">Active Slots List</h4>
            
            <?php if ($list_result->num_rows > 0): ?>
                <?php while($slot = $list_result->fetch_assoc()): 
                    $dateObj = strtotime($slot['date']);
                    $month = date('M', $dateObj);
                    $day = date('d', $dateObj);
                    $dayName = date('l', $dateObj);
                    $year = date('Y', $dateObj);
                    $timeRange = date('h:i A', strtotime($slot['start_time'])) . ' - ' . date('h:i A', strtotime($slot['end_time']));
                ?>
                
                <div class="slot-card">
                    <div class="d-flex align-items-center flex-grow-1">
                        <div class="date-box">
                            <span class="month"><?php echo $month; ?></span>
                            <span class="day"><?php echo $day; ?></span>
                        </div>

                        <div class="info-group">
                            <div class="info-item" style="min-width: 150px;">
                                <div class="info-value fw-bold fs-5"><?php echo $dayName; ?></div>
                                <div class="text-muted small"><?php echo $year; ?></div>
                            </div>
                            
                            <div class="info-item" style="min-width: 200px;">
                                <div class="info-value"><i class="far fa-clock text-primary me-2"></i><?php echo $timeRange; ?></div>
                            </div>

                            <div class="info-item">
                                <span class="capacity-badge">
                                    <?php echo $slot['slots']; ?> / <?php echo $slot['max_slots']; ?> Slots
                                </span>
                            </div>

                            <div class="info-item ms-auto">
                                <div class="status-badge">
                                    <div class="status-dot"></div> Open
                                </div>
                            </div>
                        </div>
                    </div>

                    <a href="manage_schedule.php?delete=<?php echo $slot['id']; ?>" class="btn-delete ms-4" onclick="return confirm('Are you sure you want to delete this slot?');">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                </div>

                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-5 text-muted">
                    <i class="far fa-calendar-times fa-3x mb-3"></i>
                    <p>No active slots found. Click "Create Slot" to add one.</p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<div class="modal fade" id="addSlotModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header text-white" style="background: var(--primary); padding: 20px 30px;">
                <h5 class="fw-bold m-0"><i class="fas fa-calendar-plus me-2"></i> New Schedule Slot</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <form method="POST" action="create_slot.php">
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary text-uppercase small">Date</label>
                        <input type="date" class="form-control form-control-lg" name="slot_date" required min="<?php echo date('Y-m-d'); ?>" style="background:#f8fafc; border: 1px solid #e2e8f0;">
                    </div>

                    <div class="row mb-4">
                        <div class="col-6">
                            <label class="form-label fw-bold text-secondary text-uppercase small">Start Time</label>
                            <input type="time" name="slot_start_time" class="form-control form-control-lg" required style="background:#f8fafc; border: 1px solid #e2e8f0;">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold text-secondary text-uppercase small">End Time</label>
                            <input type="time" name="slot_end_time" class="form-control form-control-lg" required style="background:#f8fafc; border: 1px solid #e2e8f0;">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold text-secondary text-uppercase small">Capacity</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-users text-muted"></i></span>
                            <input type="number" name="slot_capacity" class="form-control form-control-lg border-start-0" value="20" required min="1" style="background:#f8fafc; border: 1px solid #e2e8f0;">
                        </div>
                        <div class="form-text text-end">Default: 20 Guests</div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 p-3">
                    <button type="button" class="btn btn-link text-muted text-decoration-none fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-bold shadow-sm">Save Slot</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>