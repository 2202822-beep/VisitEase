<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
include 'db.php';

// ── AJAX DELETE ──
if (isset($_POST['ajax_delete']) && isset($_POST['id'])) {
    $id   = intval($_POST['id']);
    // Inalis ang status restriction para pwede i-delete kahit anong status
    $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
    $stmt->bind_param("i", $id);
    echo $stmt->execute() ? json_encode(['success' => true]) : json_encode(['success' => false]);
    $stmt->close();
    exit();
}

// ── AJAX FETCH (for live search/filter) ──
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    $search = '%' . $conn->real_escape_string($_GET['search'] ?? '') . '%';
    $status = $conn->real_escape_string($_GET['status'] ?? 'all');
    $page   = max(1, intval($_GET['page'] ?? 1));
    $limit  = 10;
    $offset = ($page - 1) * $limit;

    // Pinalitan ng 1=1 para isama lahat ng status (kasama ang pending)
    $where  = "1=1";
    $where .= " AND (b.name LIKE '$search' OR b.email LIKE '$search' OR b.token LIKE '$search')";
    if ($status !== 'all') $where .= " AND b.status = '$status'";

    $total_res = $conn->query("SELECT COUNT(*) as c FROM bookings b LEFT JOIN schedule_settings s ON b.schedule_id = s.id WHERE $where");
    $total     = $total_res->fetch_assoc()['c'];
    $res       = $conn->query("SELECT b.*, s.date as visit_date, s.start_time FROM bookings b LEFT JOIN schedule_settings s ON b.schedule_id = s.id WHERE $where ORDER BY b.id DESC LIMIT $limit OFFSET $offset");
    $rows = [];
    while ($r = $res->fetch_assoc()) $rows[] = $r;
    echo json_encode(['rows' => $rows, 'total' => (int)$total, 'page' => $page, 'limit' => $limit]);
    exit();
}

// ── ADMIN INFO ──
$admin_username = $_SESSION['admin_username'] ?? $_SESSION['username'] ?? 'Admin';
$admin_name     = ucfirst($admin_username);
$admin_initial  = strtoupper(substr($admin_name, 0, 1));

// ── COUNTS ──
// Inalis din ang filter dito para mabilang LAHAT ng laman ng database
$count_all       = $conn->query("SELECT COUNT(*) as c FROM bookings")->fetch_assoc()['c'];
$count_pending   = $conn->query("SELECT COUNT(*) as c FROM bookings WHERE status = 'Pending'")->fetch_assoc()['c']; // Bagong count para sa pending
$count_confirmed = $conn->query("SELECT COUNT(*) as c FROM bookings WHERE status = 'Confirmed'")->fetch_assoc()['c'];
$count_rejected  = $conn->query("SELECT COUNT(*) as c FROM bookings WHERE status = 'Rejected'")->fetch_assoc()['c'];
$count_cancelled = $conn->query("SELECT COUNT(*) as c FROM bookings WHERE status = 'Cancelled'")->fetch_assoc()['c'];
$count_completed = $conn->query("SELECT COUNT(*) as c FROM bookings WHERE status = 'Completed'")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VisitEase Admin | History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary:      #4361ee;
            --success:      #10b981;
            --danger:       #ef4444;
            --warning:      #f59e0b;
            --info:         #3b82f6;
            --grey:         #94a3b8;
            --bg-body:      #f8fafc;
            --sidebar-blue: #1e1b4b;
            --card-border:  #e2e8f0;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; overflow: hidden; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-body); color: #1e293b; display: flex; }

        /* ── SIDEBAR ── */
        .sidebar {
            width: 220px; min-width: 220px; background: var(--sidebar-blue);
            height: 100vh; padding: 25px 15px; color: #fff;
            display: flex; flex-direction: column; flex-shrink: 0;
        }
        .sidebar-brand { font-size: 1.25rem; font-weight: 800; letter-spacing: 2px; color: #fff; margin-bottom: 36px; padding: 0 6px; }
        .nav-link {
            color: rgba(255,255,255,.65); padding: 11px 14px; border-radius: 8px;
            margin-bottom: 4px; display: flex; align-items: center; gap: 10px;
            text-decoration: none; font-weight: 500; font-size: .85rem; transition: all .2s;
        }
        .nav-link i { width: 18px; text-align: center; font-size: .9rem; }
        .nav-link:hover { background: rgba(255,255,255,.1); color: #fff; }
        .nav-link.active { background: rgba(255,255,255,.14); color: #fff; font-weight: 700; }
        .nav-link.danger { color: #fca5a5; margin-top: auto; }
        .nav-link.danger:hover { background: rgba(239,68,68,.15); color: #f87171; }
        .sidebar-spacer { flex: 1; }

        /* ── MAIN CONTENT ── */
        .main-wrap {
            flex: 1; height: 100vh; overflow: hidden;
            display: flex; flex-direction: column; min-width: 0;
        }

        /* ── TOP BAR ── */
        .topbar {
            padding: 20px 32px; background: #fff;
            border-bottom: 1px solid var(--card-border);
            display: flex; align-items: center; justify-content: space-between;
            flex-shrink: 0; gap: 16px;
        }
        .topbar-left h2 { font-size: 1.25rem; font-weight: 800; color: #1e293b; margin: 0; }
        .topbar-left p  { font-size: .75rem; color: #94a3b8; margin: 2px 0 0; }
        .admin-chip {
            display: flex; align-items: center; gap: 9px;
            background: #f8fafc; border: 1px solid var(--card-border);
            border-radius: 10px; padding: 6px 13px 6px 7px;
        }
        .admin-avatar {
            width: 30px; height: 30px; border-radius: 50%;
            background: linear-gradient(135deg, #4361ee, #7c3aed);
            color: #fff; display: flex; align-items: center; justify-content: center;
            font-size: .75rem; font-weight: 800;
        }
        .admin-name  { font-size: .78rem; font-weight: 700; color: #1e293b; }
        .admin-role  { font-size: .65rem; color: #94a3b8; }

        /* ── SCROLLABLE CONTENT AREA ── */
        .content-scroll {
            flex: 1; overflow-y: auto; padding: 28px 32px;
            display: flex; flex-direction: column; gap: 22px;
        }
        .content-scroll::-webkit-scrollbar { width: 5px; }
        .content-scroll::-webkit-scrollbar-track { background: transparent; }
        .content-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

        /* ── ANIMATIONS ── */
        @keyframes fadeInUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:none; } }
        .anim { opacity: 0; animation: fadeInUp .5s ease forwards; }
        .anim-d1 { animation-delay: .05s; }
        .anim-d2 { animation-delay: .12s; }
        .anim-d3 { animation-delay: .18s; }
        .anim-d4 { animation-delay: .24s; }
        .anim-d5 { animation-delay: .30s; }
        .anim-d6 { animation-delay: .36s; }

        /* ── STAT CARDS ── */
        /* Changed from 5 to 6 columns to accommodate the Pending card */
        .stat-row { display: grid; grid-template-columns: repeat(6, 1fr); gap: 14px; }
        @media(max-width:1200px) { .stat-row { grid-template-columns: repeat(3, 1fr); } }
        @media(max-width:768px)  { .stat-row { grid-template-columns: 1fr 1fr; } }

        .stat-card {
            background: #fff; border: 1px solid var(--card-border);
            border-radius: 12px; padding: 18px 20px;
            display: flex; align-items: center; gap: 14px;
            cursor: pointer; transition: all .25s;
            position: relative; overflow: hidden;
        }
        .stat-card::before {
            content: ''; position: absolute; top: 0; left: 0;
            width: 4px; height: 100%; border-radius: 0 2px 2px 0;
        }
        .stat-card.all::before    { background: var(--primary); }
        .stat-card.yellow::before { background: var(--warning); }
        .stat-card.green::before  { background: var(--success); }
        .stat-card.red::before    { background: var(--danger); }
        .stat-card.blue::before   { background: var(--info); }
        .stat-card.grey::before   { background: var(--grey); }
        .stat-card:hover          { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,.08); }
        .stat-card.active-filter  { box-shadow: 0 0 0 2px var(--primary); }

        .stat-icon {
            width: 42px; height: 42px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; flex-shrink: 0;
        }
        .stat-icon.all   { background: #eef2ff; color: var(--primary); }
        .stat-icon.yellow{ background: #fffbeb; color: var(--warning); }
        .stat-icon.green { background: #ecfdf5; color: var(--success); }
        .stat-icon.red   { background: #fef2f2; color: var(--danger); }
        .stat-icon.blue  { background: #eff6ff; color: var(--info); }
        .stat-icon.grey  { background: #f1f5f9; color: var(--grey); }

        .stat-value { font-size: 1.5rem; font-weight: 800; color: #1e293b; line-height: 1; }
        .stat-label { font-size: .68rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; margin-top: 3px; }

        /* ── MAIN CARD ── */
        .history-card {
            background: #fff; border: 1px solid var(--card-border);
            border-radius: 14px; overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,.05);
            display: flex; flex-direction: column;
        }

        /* ── CARD HEADER ── */
        .card-head {
            padding: 18px 22px; border-bottom: 1px solid #f1f5f9;
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 12px; flex-shrink: 0;
        }
        .card-head-left { display: flex; align-items: center; gap: 12px; }
        .head-icon {
            width: 38px; height: 38px; border-radius: 10px;
            background: #eef2ff; color: var(--primary);
            display: flex; align-items: center; justify-content: center; font-size: .95rem;
        }
        .card-head-title { font-size: .95rem; font-weight: 800; color: #1e293b; margin: 0; }
        .card-head-sub   { font-size: .72rem; color: #94a3b8; margin: 2px 0 0; }

        /* help button */
        .help-btn {
            width: 26px; height: 26px; border-radius: 50%;
            background: #f1f5f9; border: none; color: #64748b;
            display: flex; align-items: center; justify-content: center;
            font-size: .65rem; font-weight: 700; cursor: pointer; transition: all .2s;
        }
        .help-btn:hover { background: var(--primary); color: #fff; transform: scale(1.1); }

        /* ── CONTROLS ROW ── */
        .controls-row {
            padding: 14px 22px; border-bottom: 1px solid #f1f5f9;
            display: flex; align-items: center; gap: 10px; flex-wrap: wrap; flex-shrink: 0;
        }
        .search-box {
            display: flex; align-items: center; gap: 8px;
            background: #f8fafc; border: 1px solid var(--card-border);
            border-radius: 8px; padding: 8px 14px; flex: 1; min-width: 200px; max-width: 320px;
        }
        .search-box i    { color: #94a3b8; font-size: .8rem; flex-shrink: 0; }
        .search-box input {
            border: none; outline: none; background: transparent;
            font-family: 'Inter', sans-serif; font-size: .82rem;
            color: #1e293b; width: 100%;
        }
        .search-box input::placeholder { color: #94a3b8; }

        .filter-select {
            padding: 8px 14px; border: 1px solid var(--card-border); border-radius: 8px;
            background: #f8fafc; font-family: 'Inter', sans-serif; font-size: .82rem;
            color: #475569; cursor: pointer; outline: none; font-weight: 500;
            transition: border-color .2s;
        }
        .filter-select:focus { border-color: var(--primary); }

        .result-badge {
            padding: 5px 12px; border-radius: 20px;
            background: #eef2ff; color: var(--primary);
            font-size: .72rem; font-weight: 700; white-space: nowrap;
        }

        .btn-export {
            padding: 8px 16px; border-radius: 8px; border: 1px solid var(--card-border);
            background: #fff; color: #475569; font-size: .78rem; font-weight: 600;
            cursor: pointer; display: flex; align-items: center; gap: 6px;
            transition: all .2s; white-space: nowrap; font-family: 'Inter', sans-serif;
        }
        .btn-export:hover { background: #f1f5f9; border-color: #cbd5e1; }

        /* ── TABLE SCROLL AREA ── */
        .table-scroll {
            overflow-y: auto; flex: 1;
            min-height: 0;
        }
        .table-scroll::-webkit-scrollbar { width: 5px; }
        .table-scroll::-webkit-scrollbar-track { background: transparent; }
        .table-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .table-scroll:hover::-webkit-scrollbar-thumb { background: #cbd5e1; }

        /* ── TABLE ── */
        .history-table { width: 100%; border-collapse: collapse; }
        .history-table thead { position: sticky; top: 0; z-index: 5; }
        .history-table thead th {
            background: #f8fafc; color: #64748b;
            font-size: .7rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: .06em; padding: 13px 18px;
            border-bottom: 1px solid var(--card-border);
            white-space: nowrap;
        }
        .history-table thead th:first-child { border-radius: 0; }
        .history-table tbody tr {
            border-bottom: 1px solid #f8fafc; transition: background .15s;
        }
        .history-table tbody tr:last-child { border-bottom: none; }
        .history-table tbody tr:hover { background: #fafbff; }
        .history-table td { padding: 14px 18px; font-size: .82rem; vertical-align: middle; }

        /* visitor cell */
        .visitor-name  { font-weight: 700; color: #1e293b; }
        .visitor-email { font-size: .72rem; color: #94a3b8; margin-top: 2px; }

        /* token cell */
        .token-pill {
            display: inline-block; padding: 3px 9px; border-radius: 6px;
            background: #f1f5f9; border: 1px solid #e2e8f0;
            font-size: .7rem; font-weight: 700; color: var(--primary);
            font-family: monospace; letter-spacing: .5px;
        }

        /* schedule cell */
        .sched-date { font-weight: 700; color: #1e293b; }
        .sched-time { font-size: .72rem; color: var(--primary); font-weight: 600; margin-top: 2px; }

        /* pax */
        .pax-badge {
            display: inline-flex; align-items: center; gap: 4px;
            background: #f1f5f9; padding: 4px 10px; border-radius: 20px;
            font-size: .78rem; font-weight: 700; color: #475569;
        }

        /* status badges */
        .status-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 11px; border-radius: 20px;
            font-size: .7rem; font-weight: 700; white-space: nowrap;
        }
        .status-badge .dot { width: 6px; height: 6px; border-radius: 50%; }
        .badge-confirmed { background: #ecfdf5; color: #065f46; }
        .badge-confirmed .dot { background: var(--success); }
        .badge-rejected  { background: #fef2f2; color: #991b1b; }
        .badge-rejected  .dot { background: var(--danger); }
        .badge-cancelled { background: #f1f5f9; color: #475569; }
        .badge-cancelled .dot { background: var(--grey); }
        .badge-completed { background: #eff6ff; color: #1e40af; }
        .badge-completed .dot { background: var(--info); }

        /* processed date */
        .proc-date { font-size: .78rem; color: #64748b; }
        .proc-time { font-size: .68rem; color: #94a3b8; margin-top: 1px; }

        /* delete btn */
        .btn-delete {
            width: 32px; height: 32px; border-radius: 8px;
            background: #fef2f2; border: 1px solid #fecaca;
            color: var(--danger); display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all .2s; font-size: .8rem;
        }
        .btn-delete:hover { background: var(--danger); color: #fff; transform: scale(1.08); }

        /* ── EMPTY STATE ── */
        .empty-state {
            padding: 60px 20px; text-align: center; color: #94a3b8;
        }
        .empty-state .empty-icon {
            width: 64px; height: 64px; border-radius: 50%;
            background: #f1f5f9; color: #cbd5e1;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.6rem; margin: 0 auto 16px;
        }
        .empty-state h6 { color: #475569; font-weight: 700; margin-bottom: 6px; }
        .empty-state p  { font-size: .82rem; }

        /* ── PAGINATION ── */
        .pagination-row {
            padding: 12px 20px; border-top: 1px solid #f1f5f9;
            display: flex; align-items: center; justify-content: space-between;
            gap: 12px; flex-shrink: 0; flex-wrap: wrap;
        }
        .pg-info { font-size: .75rem; color: #94a3b8; font-weight: 500; }
        .pg-btns { display: flex; gap: 5px; }
        .pg-btn {
            width: 32px; height: 32px; border-radius: 7px;
            border: 1px solid var(--card-border); background: #fff;
            color: #475569; font-size: .75rem; font-weight: 600;
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            transition: all .15s;
        }
        .pg-btn:hover:not(:disabled) { background: var(--primary); color: #fff; border-color: var(--primary); }
        .pg-btn.active { background: var(--primary); color: #fff; border-color: var(--primary); }
        .pg-btn:disabled { opacity: .35; cursor: default; }

        /* ── LOADING SKELETON ── */
        .skeleton-row td { padding: 14px 18px; }
        .skeleton { background: linear-gradient(90deg, #f1f5f9 25%, #e8edf5 50%, #f1f5f9 75%); background-size: 200% 100%; animation: shimmer 1.4s infinite; border-radius: 6px; height: 14px; }
        @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

        /* ── HELP & CONFIRM MODALS ── */
        .modal-content { border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,.15); }
        .confirm-icon {
            width: 56px; height: 56px; border-radius: 50%;
            background: #fef2f2; color: var(--danger);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem; margin: 0 auto 14px;
        }

        /* ── TOAST ── */
        .toast-custom {
            position: fixed; bottom: 28px; right: 28px;
            background: #1e293b; color: #fff; padding: 13px 20px;
            border-radius: 10px; font-size: .82rem; font-weight: 600;
            display: flex; align-items: center; gap: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,.2);
            z-index: 9999; transform: translateY(80px); opacity: 0;
            transition: all .3s cubic-bezier(0.34, 1.56, 0.64, 1);
            pointer-events: none;
        }
        .toast-custom.show { transform: translateY(0); opacity: 1; }
        .toast-custom.success i { color: #34d399; }
        .toast-custom.error   i { color: #f87171; }

        /* ── RESPONSIVE ── */
        @media(max-width:991px) {
            .sidebar { display: none; }
            .content-scroll { padding: 18px; }
        }
        @media(max-width:600px) {
            .topbar { padding: 14px 18px; }
            .card-head { padding: 14px 16px; }
            .controls-row { padding: 12px 16px; }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand">VISITEASE</div>
    <nav style="display:flex; flex-direction:column; flex:1;">
        <a href="admin.php"           class="nav-link"><i class="fas fa-th-large"></i> Dashboard</a>
        <a href="manage_schedule.php" class="nav-link"><i class="fas fa-calendar-alt"></i> Schedule</a>
        <a href="visitors.php"        class="nav-link"><i class="fas fa-users"></i> Visitors</a>
        <a href="history.php"         class="nav-link active"><i class="fas fa-history"></i> History</a>
        <div class="sidebar-spacer"></div>
        <a href="logout.php"          class="nav-link danger"><i class="fas fa-power-off"></i> Logout</a>
    </nav>
</div>

<div class="main-wrap">

    <div class="topbar">
        <div class="topbar-left">
            <h2><i class="fas fa-history me-2" style="color:var(--primary);font-size:1rem;"></i>History Records</h2>
            <p>All system bookings including pending, approved, rejected, completed & cancelled</p>
        </div>
        <div class="admin-chip">
            <div class="admin-avatar"><?php echo $admin_initial; ?></div>
            <div>
                <div class="admin-name"><?php echo htmlspecialchars($admin_name); ?></div>
                <div class="admin-role">Administrator</div>
            </div>
        </div>
    </div>

    <div class="content-scroll">

        <div class="stat-row">
            <div class="stat-card all anim anim-d1" onclick="filterByStatus('all')" id="card-all">
                <div class="stat-icon all"><i class="fas fa-list-alt"></i></div>
                <div>
                    <div class="stat-value"><?php echo $count_all; ?></div>
                    <div class="stat-label">All Records</div>
                </div>
            </div>
            <div class="stat-card yellow anim anim-d2" onclick="filterByStatus('Pending')" id="card-Pending">
                <div class="stat-icon yellow"><i class="fas fa-clock"></i></div>
                <div>
                    <div class="stat-value"><?php echo $count_pending; ?></div>
                    <div class="stat-label">Pending</div>
                </div>
            </div>
            <div class="stat-card green anim anim-d3" onclick="filterByStatus('Confirmed')" id="card-Confirmed">
                <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
                <div>
                    <div class="stat-value"><?php echo $count_confirmed; ?></div>
                    <div class="stat-label">Approved</div>
                </div>
            </div>
            <div class="stat-card red anim anim-d4" onclick="filterByStatus('Rejected')" id="card-Rejected">
                <div class="stat-icon red"><i class="fas fa-times-circle"></i></div>
                <div>
                    <div class="stat-value"><?php echo $count_rejected; ?></div>
                    <div class="stat-label">Rejected</div>
                </div>
            </div>
            <div class="stat-card blue anim anim-d5" onclick="filterByStatus('Completed')" id="card-Completed">
                <div class="stat-icon blue"><i class="fas fa-flag-checkered"></i></div>
                <div>
                    <div class="stat-value"><?php echo $count_completed; ?></div>
                    <div class="stat-label">Completed</div>
                </div>
            </div>
            <div class="stat-card grey anim anim-d6" onclick="filterByStatus('Cancelled')" id="card-Cancelled">
                <div class="stat-icon grey"><i class="fas fa-ban"></i></div>
                <div>
                    <div class="stat-value"><?php echo $count_cancelled; ?></div>
                    <div class="stat-label">Cancelled</div>
                </div>
            </div>
        </div>

        <div class="history-card anim anim-d3" style="flex:1; min-height:0;">

            <div class="card-head">
                <div class="card-head-left">
                    <div class="head-icon"><i class="fas fa-clock-rotate-left"></i></div>
                    <div>
                        <div class="card-head-title">Booking History</div>
                        <div class="card-head-sub">Live records of all transactions in the system</div>
                    </div>
                    <button class="help-btn ms-2" onclick="showHelpModal()" title="What is History?">
                        <i class="fas fa-question"></i>
                    </button>
                </div>
                <span class="result-badge" id="resultBadge">Total: <?php echo $count_all; ?></span>
            </div>

            <div class="controls-row">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Search name, email or token...">
                </div>
                <select class="filter-select" id="statusFilter">
                    <option value="all">All Status</option>
                    <option value="Pending">Pending</option>
                    <option value="Confirmed">Approved</option>
                    <option value="Rejected">Rejected</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
                <div style="margin-left:auto; display:flex; gap:8px;">
                    <button class="btn-export" onclick="exportCSV()">
                        <i class="fas fa-download"></i> Export CSV
                    </button>
                </div>
            </div>

            <div class="table-scroll" id="tableScroll">
                <table class="history-table" id="historyTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Visitor</th>
                            <th>Token</th>
                            <th>Schedule</th>
                            <th>Pax</th>
                            <th>Status</th>
                            <th>Date Processed</th>
                            <th style="text-align:center;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        </tbody>
                </table>
            </div>

            <div class="pagination-row">
                <div class="pg-info" id="pgInfo">Loading...</div>
                <div class="pg-btns" id="pgBtns"></div>
            </div>

        </div>

    </div></div><div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:380px;">
        <div class="modal-content">
            <div class="modal-body p-4 text-center">
                <div class="confirm-icon"><i class="fas fa-trash-alt"></i></div>
                <h5 class="fw-bold mb-2" style="color:#1e293b;">Delete This Record?</h5>
                <p style="font-size:.85rem; color:#64748b; margin-bottom:22px;">
                    This action is <strong>permanent</strong> and cannot be undone.<br>
                    The booking record will be permanently removed.
                </p>
                <div class="d-flex gap-3 justify-content-center">
                    <button type="button" class="btn btn-outline-secondary px-4 fw-bold" data-bs-dismiss="modal" style="border-radius:10px;">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-danger px-4 fw-bold" id="confirmDeleteBtn" style="border-radius:10px;">
                        <i class="fas fa-trash-alt me-2"></i>Yes, Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="helpModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content">
            <div class="modal-body p-4 text-center">
                <div style="width:54px;height:54px;border-radius:50%;background:#eef2ff;color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:1.4rem;margin:0 auto 14px;">
                    <i class="fas fa-history"></i>
                </div>
                <span style="display:inline-block;background:#eef2ff;color:var(--primary);font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;padding:3px 12px;border-radius:20px;margin-bottom:12px;">About This Page</span>
                <h5 class="fw-bold mb-3" style="color:#1e293b;">What is History?</h5>
                <p style="font-size:.85rem;color:#475569;line-height:1.7;text-align:left;">
                    The <strong>History page</strong> shows all booking records in the system. This includes:
                </p>
                <ul style="font-size:.82rem;color:#475569;text-align:left;padding-left:18px;line-height:2;margin-top:8px;">
                    <li><strong style="color:#b45309;">⏳ Pending</strong> — newly booked requests</li>
                    <li><strong style="color:#065f46;">✅ Approved</strong> — bookings confirmed by admin</li>
                    <li><strong style="color:#991b1b;">❌ Rejected</strong> — bookings declined by admin</li>
                    <li><strong style="color:#1e40af;">🏁 Completed</strong> — visits that have taken place</li>
                    <li><strong style="color:#475569;">🚫 Cancelled</strong> — requests cancelled by the visitor</li>
                </ul>
                <p style="font-size:.78rem;color:#94a3b8;margin-top:12px;">
                    You can search, filter, export, and delete individual records permanently using the trash icon.
                </p>
                <button type="button" class="btn btn-primary w-100 mt-3 fw-bold" data-bs-dismiss="modal" style="border-radius:10px;">Got it!</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-custom" id="toastMsg">
    <i class="fas fa-check-circle"></i>
    <span id="toastText">Done!</span>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* ══════════════════════════════════════════
   STATE
   ══════════════════════════════════════════ */
let currentPage    = 1;
let currentSearch  = '';
let currentStatus  = 'all';
let totalRecords   = 0;
let totalPages     = 1;
const LIMIT        = 10;

let deleteId       = null;
let searchTimer    = null;
let allRowsCache   = []; // used for CSV export

/* ══════════════════════════════════════════
   HELPERS
   ══════════════════════════════════════════ */
function statusBadge(status) {
    const map = {
        'Pending':   `<span class="status-badge" style="background:#fffbeb; color:#b45309;"><span class="dot" style="background:var(--warning);"></span>Pending</span>`,
        'Confirmed': `<span class="status-badge badge-confirmed"><span class="dot"></span>Approved</span>`,
        'Rejected':  `<span class="status-badge badge-rejected"><span class="dot"></span>Rejected</span>`,
        'Cancelled': `<span class="status-badge badge-cancelled"><span class="dot"></span>Cancelled</span>`,
        'Completed': `<span class="status-badge badge-completed"><span class="dot"></span>Completed</span>`,
    };
    return map[status] || `<span class="status-badge badge-cancelled"><span class="dot"></span>${status}</span>`;
}

function fmtDate(d) {
    if (!d || d === '0000-00-00') return '<span style="color:#cbd5e1;">—</span>';
    const dt = new Date(d);
    return dt.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}
function fmtTime(t) {
    if (!t) return '';
    const [h, m] = t.split(':');
    const hh = parseInt(h); const ampm = hh >= 12 ? 'PM' : 'AM';
    return `${hh % 12 || 12}:${m} ${ampm}`;
}
function fmtDateTime(dt) {
    if (!dt || dt === '0000-00-00 00:00:00') return '<span style="color:#cbd5e1;">—</span>';
    const d = new Date(dt.replace(' ', 'T'));
    return {
        date: d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }),
        time: d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })
    };
}
function escHtml(s) {
    if (!s) return '—';
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

/* ══════════════════════════════════════════
   SKELETON LOADER
   ══════════════════════════════════════════ */
function showSkeleton() {
    let html = '';
    for (let i = 0; i < 6; i++) {
        html += `<tr class="skeleton-row">
            <td><div class="skeleton" style="width:24px;"></div></td>
            <td><div class="skeleton" style="width:130px;margin-bottom:6px;"></div><div class="skeleton" style="width:100px;height:10px;"></div></td>
            <td><div class="skeleton" style="width:70px;"></div></td>
            <td><div class="skeleton" style="width:90px;margin-bottom:6px;"></div><div class="skeleton" style="width:60px;height:10px;"></div></td>
            <td><div class="skeleton" style="width:40px;"></div></td>
            <td><div class="skeleton" style="width:80px;border-radius:20px;"></div></td>
            <td><div class="skeleton" style="width:90px;margin-bottom:6px;"></div><div class="skeleton" style="width:60px;height:10px;"></div></td>
            <td style="text-align:center;"><div class="skeleton" style="width:32px;height:32px;border-radius:8px;margin:auto;"></div></td>
        </tr>`;
    }
    document.getElementById('tableBody').innerHTML = html;
}

/* ══════════════════════════════════════════
   FETCH + RENDER
   ══════════════════════════════════════════ */
function fetchHistory(page = 1) {
    currentPage = page;
    showSkeleton();

    const url = `history.php?ajax=1&search=${encodeURIComponent(currentSearch)}&status=${currentStatus}&page=${page}`;

    fetch(url)
        .then(r => r.json())
        .then(data => {
            totalRecords  = data.total;
            totalPages    = Math.max(1, Math.ceil(totalRecords / LIMIT));
            allRowsCache  = data.rows;
            renderTable(data.rows, page);
            renderPagination(page);
            document.getElementById('resultBadge').textContent = `Total: ${totalRecords}`;
        })
        .catch(() => {
            document.getElementById('tableBody').innerHTML = `
                <tr><td colspan="8">
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-exclamation-triangle"></i></div>
                        <h6>Failed to load records</h6>
                        <p>Please refresh the page and try again.</p>
                    </div>
                </td></tr>`;
        });
}

function renderTable(rows, page) {
    const tbody = document.getElementById('tableBody');
    if (!rows || rows.length === 0) {
        tbody.innerHTML = `
            <tr><td colspan="8">
                <div class="empty-state">
                    <div class="empty-icon"><i class="fas fa-folder-open"></i></div>
                    <h6>No history available</h6>
                    <p>No records match your current search or filter.</p>
                </div>
            </td></tr>`;
        document.getElementById('pgInfo').textContent = '0 records';
        return;
    }

    const offset = (page - 1) * LIMIT;
    let html = '';

    rows.forEach((r, idx) => {
        const proc = fmtDateTime(r.updated_at || r.created_at);
        const visitDate = fmtDate(r.visit_date);
        const visitTime = fmtTime(r.start_time);

        html += `
        <tr id="row-${r.id}">
            <td style="color:#94a3b8;font-weight:700;font-size:.75rem;">${offset + idx + 1}</td>
            <td>
                <div class="visitor-name">${escHtml(r.name)}</div>
                <div class="visitor-email">${escHtml(r.email) || '—'}</div>
            </td>
            <td><span class="token-pill">#${escHtml(r.token)}</span></td>
            <td>
                <div class="sched-date">${visitDate}</div>
                ${visitTime ? `<div class="sched-time">${visitTime}</div>` : ''}
            </td>
            <td>
                <span class="pax-badge"><i class="fas fa-user" style="font-size:.6rem;"></i>${r.guests}</span>
            </td>
            <td>${statusBadge(r.status)}</td>
            <td>
                <div class="proc-date">${typeof proc === 'object' ? proc.date : proc}</div>
                ${typeof proc === 'object' ? `<div class="proc-time">${proc.time}</div>` : ''}
            </td>
            <td style="text-align:center;">
                <button class="btn-delete" onclick="confirmDelete(${r.id})" title="Delete record">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        </tr>`;
    });

    tbody.innerHTML = html;

    // animate rows in
    tbody.querySelectorAll('tr').forEach((tr, i) => {
        tr.style.opacity = '0';
        tr.style.transform = 'translateY(8px)';
        tr.style.transition = `opacity .25s ${i * 0.03}s ease, transform .25s ${i * 0.03}s ease`;
        requestAnimationFrame(() => {
            tr.style.opacity = '1';
            tr.style.transform = 'none';
        });
    });

    // update pg info
    const from = offset + 1;
    const to   = Math.min(offset + rows.length, totalRecords);
    document.getElementById('pgInfo').textContent = `Showing ${from}–${to} of ${totalRecords} records`;
}

/* ══════════════════════════════════════════
   PAGINATION
   ══════════════════════════════════════════ */
function renderPagination(page) {
    const container = document.getElementById('pgBtns');
    let html = '';

    html += `<button class="pg-btn" onclick="fetchHistory(${page - 1})" ${page <= 1 ? 'disabled' : ''}>
                <i class="fas fa-chevron-left" style="font-size:.6rem;"></i>
             </button>`;

    // Show limited page buttons
    const delta = 2;
    const start = Math.max(1, page - delta);
    const end   = Math.min(totalPages, page + delta);

    if (start > 1) {
        html += `<button class="pg-btn" onclick="fetchHistory(1)">1</button>`;
        if (start > 2) html += `<button class="pg-btn" disabled style="border:none;background:transparent;">…</button>`;
    }

    for (let i = start; i <= end; i++) {
        html += `<button class="pg-btn ${i === page ? 'active' : ''}" onclick="fetchHistory(${i})">${i}</button>`;
    }

    if (end < totalPages) {
        if (end < totalPages - 1) html += `<button class="pg-btn" disabled style="border:none;background:transparent;">…</button>`;
        html += `<button class="pg-btn" onclick="fetchHistory(${totalPages})">${totalPages}</button>`;
    }

    html += `<button class="pg-btn" onclick="fetchHistory(${page + 1})" ${page >= totalPages ? 'disabled' : ''}>
                <i class="fas fa-chevron-right" style="font-size:.6rem;"></i>
             </button>`;

    container.innerHTML = html;
}

/* ══════════════════════════════════════════
   SEARCH & FILTER
   ══════════════════════════════════════════ */
document.getElementById('searchInput').addEventListener('input', function () {
    clearTimeout(searchTimer);
    currentSearch = this.value.trim();
    searchTimer   = setTimeout(() => fetchHistory(1), 380);
});

document.getElementById('statusFilter').addEventListener('change', function () {
    currentStatus = this.value;
    // Sync stat card highlight
    document.querySelectorAll('.stat-card').forEach(c => c.classList.remove('active-filter'));
    const card = document.getElementById('card-' + (currentStatus === 'all' ? 'all' : currentStatus));
    if (card) card.classList.add('active-filter');
    fetchHistory(1);
});

function filterByStatus(status) {
    currentStatus = status;
    document.getElementById('statusFilter').value = status;
    document.querySelectorAll('.stat-card').forEach(c => c.classList.remove('active-filter'));
    const card = document.getElementById('card-' + status);
    if (card) card.classList.add('active-filter');
    fetchHistory(1);
}

/* ══════════════════════════════════════════
   DELETE
   ══════════════════════════════════════════ */
const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

function confirmDelete(id) {
    deleteId = id;
    deleteModal.show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
    if (!deleteId) return;

    const btn = this;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Deleting...';
    btn.disabled  = true;

    const fd = new FormData();
    fd.append('ajax_delete', '1');
    fd.append('id', deleteId);

    fetch('history.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            deleteModal.hide();
            btn.innerHTML = '<i class="fas fa-trash-alt me-2"></i>Yes, Delete';
            btn.disabled  = false;

            if (data.success) {
                // Animate row out then re-fetch
                const row = document.getElementById('row-' + deleteId);
                if (row) {
                    row.style.transition = 'all .3s ease';
                    row.style.opacity    = '0';
                    row.style.transform  = 'translateX(20px)';
                    setTimeout(() => fetchHistory(currentPage), 320);
                } else {
                    fetchHistory(currentPage);
                }
                showToast('Record deleted successfully.', 'success');
                // update stat cards counts
                updateStatCounts();
            } else {
                showToast('Failed to delete. Please try again.', 'error');
            }
            deleteId = null;
        })
        .catch(() => {
            deleteModal.hide();
            btn.innerHTML = '<i class="fas fa-trash-alt me-2"></i>Yes, Delete';
            btn.disabled  = false;
            showToast('Network error. Please try again.', 'error');
        });
});

function updateStatCounts() {
    fetch('history.php?ajax=1&search=&status=all&page=1')
        .then(r => r.json())
        .then(data => {
            document.getElementById('resultBadge').textContent = `Total: ${data.total}`;
            // Optional: you can fetch individual counts again if needed, 
            // but refreshing the page ensures the upper PHP counts match 100%.
        });
}

/* ══════════════════════════════════════════
   TOAST
   ══════════════════════════════════════════ */
function showToast(msg, type = 'success') {
    const toast = document.getElementById('toastMsg');
    const icon  = toast.querySelector('i');
    toast.querySelector('#toastText').textContent = msg;
    toast.className = `toast-custom ${type}`;
    icon.className  = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
    requestAnimationFrame(() => toast.classList.add('show'));
    setTimeout(() => toast.classList.remove('show'), 3000);
}

/* ══════════════════════════════════════════
   HELP MODAL
   ══════════════════════════════════════════ */
function showHelpModal() {
    new bootstrap.Modal(document.getElementById('helpModal')).show();
}

/* ══════════════════════════════════════════
   EXPORT CSV
   ══════════════════════════════════════════ */
function exportCSV() {
    if (!allRowsCache || allRowsCache.length === 0) {
        showToast('No records to export.', 'error');
        return;
    }

    const headers = ['#', 'Name', 'Email', 'Token', 'Visit Date', 'Visit Time', 'Pax', 'Status', 'Date Processed'];
    const rows    = allRowsCache.map((r, i) => [
        i + 1,
        '"' + (r.name || '').replace(/"/g, '""') + '"',
        '"' + (r.email || '').replace(/"/g, '""') + '"',
        r.token || '',
        r.visit_date || '',
        r.start_time || '',
        r.guests || '',
        r.status || '',
        (r.updated_at || r.created_at || '').split(' ')[0]
    ]);

    const csv  = [headers, ...rows].map(r => r.join(',')).join('\n');
    const blob = new Blob([csv], { type: 'text/csv' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href     = url;
    a.download = `history_${new Date().toISOString().slice(0,10)}.csv`;
    a.click();
    URL.revokeObjectURL(url);
    showToast('CSV exported successfully!', 'success');
}

/* ══════════════════════════════════════════
   INIT
   ══════════════════════════════════════════ */
document.getElementById('card-all').classList.add('active-filter');
fetchHistory(1);
</script>
</body>
</html>