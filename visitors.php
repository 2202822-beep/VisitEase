<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
include 'db.php';
require_once 'email_helper.php';

// ── FETCH ADMIN PROFILE ──
$admin_username = $_SESSION['admin_username'] ?? $_SESSION['username'] ?? 'Admin';
$admin_name     = ucfirst($admin_username);
$admin_email    = 'admin@visitease.com';
$admin_res      = @$conn->query("SELECT * FROM admins WHERE username = '" . $conn->real_escape_string($admin_username) . "' LIMIT 1");
if ($admin_res && $admin_res->num_rows > 0) {
    $admin_row   = $admin_res->fetch_assoc();
    $admin_name  = $admin_row['name'] ?? $admin_row['full_name'] ?? $admin_name;
    $admin_email = $admin_row['email'] ?? $admin_email;
}
$admin_email   = $_SESSION['admin_email'] ?? $admin_email;
$admin_initial = strtoupper(substr($admin_name, 0, 1));

// ── NOTIFICATIONS (pending count for bell — same as admin.php) ──
$notif_res   = $conn->query("SELECT b.name, b.token, s.date as visit_date, s.start_time, b.id FROM bookings b LEFT JOIN schedule_settings s ON b.schedule_id = s.id WHERE b.status = 'Pending' ORDER BY b.id DESC LIMIT 5");
$notifs      = [];
while ($n = $notif_res->fetch_assoc()) $notifs[] = $n;
$notif_count    = count($notifs);
$notif_ids_str  = implode(',', array_column($notifs, 'id'));

// ════════════════════════════════════════
// AJAX HANDLERS
// ════════════════════════════════════════
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');

    // ── APPROVE ──
    if ($_GET['ajax'] === 'approve') {
        $id  = intval($_POST['id'] ?? 0);
        $get = $conn->query("SELECT b.*, s.date as sched_date, s.start_time FROM bookings b LEFT JOIN schedule_settings s ON b.schedule_id = s.id WHERE b.id = $id LIMIT 1");
        if ($get && $get->num_rows > 0) {
            $b = $get->fetch_assoc();
            $conn->query("UPDATE bookings SET status = 'Confirmed' WHERE id = $id");
            $vname  = $b['name'];  $vtoken = $b['token'];
            $vemail = $b['email']; $guests = $b['guests'];
            $vdate  = $b['sched_date']  ? date('F d, Y', strtotime($b['sched_date']))   : '—';
            $vtime  = $b['start_time']  ? date('h:i A',  strtotime($b['start_time']))   : '—';
            $subject = 'VisitEase: Your Booking is CONFIRMED!';
            $body = "
            <div style='font-family:Arial,sans-serif;color:#333;max-width:600px;margin:0 auto;border:1px solid #ddd;border-radius:8px;overflow:hidden;'>
                <div style='background-color:#10b981;color:#fff;padding:20px;text-align:center;'><h2 style='margin:0;'>Booking Confirmed! ✅</h2></div>
                <div style='padding:20px;background-color:#fafaf9;'>
                    <h3 style='color:#1a2035;'>Hello $vname,</h3>
                    <p>Magandang balita! Ang iyong booking sa VisitEase ay na-approve na ng aming admin.</p>
                    <div style='background-color:#fff;border-left:4px solid #10b981;padding:15px;margin:20px 0;'>
                        <p style='margin:0 0 10px;font-size:14px;color:#777;'>YOUR ENTRANCE TOKEN</p>
                        <h1 style='margin:0;color:#1a2035;letter-spacing:2px;'>$vtoken</h1>
                    </div>
                    <p>Ipakita ang token na ito sa entrance sa araw ng iyong pagbisita.</p>
                    <ul style='list-style:none;padding:0;'>
                        <li style='margin-bottom:8px;'><b>Date:</b> $vdate</li>
                        <li style='margin-bottom:8px;'><b>Time:</b> $vtime</li>
                        <li style='margin-bottom:8px;'><b>Guests:</b> $guests Pax</li>
                    </ul>
                </div>
            </div>";
            if (!empty($vemail)) sendSystemEmail($vemail, $subject, $body);
            echo json_encode(['success' => true, 'message' => 'Booking approved and email sent.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Booking not found.']);
        }
        exit();
    }

    // ── REJECT ──
    if ($_GET['ajax'] === 'reject') {
        $id  = intval($_POST['id'] ?? 0);
        $get = $conn->query("SELECT * FROM bookings WHERE id = $id LIMIT 1");
        if ($get && $get->num_rows > 0) {
            $b = $get->fetch_assoc();
            $conn->query("UPDATE bookings SET status = 'Rejected' WHERE id = $id");
            $conn->query("UPDATE schedule_settings SET slots = slots + {$b['guests']} WHERE id = '{$b['schedule_id']}'");
            $vname  = $b['name'];  $vtoken = $b['token']; $vemail = $b['email'];
            $subject = 'VisitEase: Booking Update';
            $body = "
            <div style='font-family:Arial,sans-serif;color:#333;max-width:600px;margin:0 auto;border:1px solid #ddd;border-radius:8px;overflow:hidden;'>
                <div style='background-color:#ef4444;color:#fff;padding:20px;text-align:center;'><h2 style='margin:0;'>Booking Notice</h2></div>
                <div style='padding:20px;background-color:#fafaf9;'>
                    <h3 style='color:#1a2035;'>Hello $vname,</h3>
                    <p>Ikinalulungkot naming ipaalam na hindi na-approve ang iyong booking (Token: <b>$vtoken</b>).</p>
                    <p>Ito ay maaaring dahil sa invalid na GCash Reference number o full capacity na ang napiling schedule. Mangyaring mag-book na lamang muli gamit ang tamang detalye.</p>
                </div>
            </div>";
            if (!empty($vemail)) sendSystemEmail($vemail, $subject, $body);
            echo json_encode(['success' => true, 'message' => 'Booking rejected.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Booking not found.']);
        }
        exit();
    }

    // ── DELETE ──
    if ($_GET['ajax'] === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        $conn->query("DELETE FROM bookings WHERE id = $id");
        echo json_encode(['success' => true]);
        exit();
    }

    // ── GET SINGLE BOOKING ──
    if ($_GET['ajax'] === 'get') {
        $id  = intval($_GET['id'] ?? 0);
        $res = $conn->query("SELECT b.*, s.date as sched_date, s.start_time, s.end_time FROM bookings b LEFT JOIN schedule_settings s ON b.schedule_id = s.id WHERE b.id = $id LIMIT 1");
        if ($res && $res->num_rows > 0) {
            echo json_encode(['success' => true, 'data' => $res->fetch_assoc()]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit();
    }

    // ── EXPORT CSV ──
    if ($_GET['ajax'] === 'export_csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="visitors_' . date('Y-m-d') . '.csv"');
        $res = $conn->query("SELECT b.token, b.name, b.email, s.date as visit_date, s.start_time, b.guests, b.status, b.gcash_name, b.gcash_ref, b.special_request, b.created_at FROM bookings b LEFT JOIN schedule_settings s ON b.schedule_id = s.id ORDER BY b.created_at DESC");
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Token','Name','Email','Visit Date','Visit Time','Guests','Status','GCash Name','GCash Ref','Special Request','Booked At']);
        while ($row = $res->fetch_assoc()) fputcsv($out, $row);
        fclose($out);
        exit();
    }
    exit();
}

// ════════════════════════════════════════
// STATS — pulled from real bookings table
// ════════════════════════════════════════
$stat_total     = $conn->query("SELECT COUNT(*) as c FROM bookings")->fetch_assoc()['c'];
$stat_pending   = $conn->query("SELECT COUNT(*) as c FROM bookings WHERE status='Pending'")->fetch_assoc()['c'];
$stat_confirmed = $conn->query("SELECT COUNT(*) as c FROM bookings WHERE status='Confirmed'")->fetch_assoc()['c'];
$stat_completed = $conn->query("SELECT COUNT(*) as c FROM bookings WHERE status='Completed'")->fetch_assoc()['c'];
$stat_rejected  = $conn->query("SELECT COUNT(*) as c FROM bookings WHERE status='Rejected'")->fetch_assoc()['c'];
$stat_guests    = $conn->query("SELECT SUM(guests) as c FROM bookings WHERE status='Confirmed'")->fetch_assoc()['c'] ?? 0;

// ════════════════════════════════════════
// SEARCH / FILTER / PAGINATION
// ════════════════════════════════════════
$search  = trim($_GET['search'] ?? '');
$filter  = trim($_GET['filter'] ?? '');
$per_pg  = 10;
$page    = max(1, intval($_GET['pg'] ?? 1));
$offset  = ($page - 1) * $per_pg;

$where = "WHERE 1=1";
if ($search !== '') {
    $s      = $conn->real_escape_string($search);
    $where .= " AND (b.name LIKE '%$s%' OR b.email LIKE '%$s%' OR b.token LIKE '%$s%')";
}
if ($filter !== '') {
    $where .= " AND b.status = '" . $conn->real_escape_string($filter) . "'";
}

$total_filtered = $conn->query("SELECT COUNT(*) as c FROM bookings b LEFT JOIN schedule_settings s ON b.schedule_id = s.id $where")->fetch_assoc()['c'];
$total_pages    = max(1, ceil($total_filtered / $per_pg));
$bookings_res   = $conn->query("SELECT b.*, s.date as sched_date, s.start_time, s.end_time FROM bookings b LEFT JOIN schedule_settings s ON b.schedule_id = s.id $where ORDER BY b.created_at DESC LIMIT $per_pg OFFSET $offset");

// Status badge helper
function statusBadge($s) {
    $map = [
        'Pending'   => ['#fef9c3','#854d0e','#f59e0b','⏳ Pending'],
        'Confirmed' => ['#dcfce7','#166534','#22c55e','✅ Confirmed'],
        'Completed' => ['#dbeafe','#1e40af','#3b82f6','🏁 Completed'],
        'Rejected'  => ['#fee2e2','#991b1b','#ef4444','❌ Rejected'],
        'Cancelled' => ['#f3e8ff','#6b21a8','#a855f7','🚫 Cancelled'],
    ];
    $c = $map[$s] ?? ['#f1f5f9','#475569','#94a3b8',$s];
    return "<span class='status-badge' style='background:{$c[0]};color:{$c[1]};'>
                <span class='status-dot' style='background:{$c[2]};'></span>{$c[3]}
            </span>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VisitEase Admin | Visitors</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* ════ ROOT — identical to admin.php ════ */
        :root {
            --primary:      #4361ee;
            --secondary:    #3f37c9;
            --success:      #10b981;
            --bg-body:      #f8fafc;
            --sidebar-blue: #1e1b4b;
        }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg-body); color: #1e293b; }

        /* ── ANIMATIONS — same as admin.php ── */
        @keyframes fadeInUp   { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:none; } }
        @keyframes fadeInLeft { from { opacity:0; transform:translateX(-14px); } to { opacity:1; transform:none; } }
        @keyframes pulse-dot  { 0%,100%{transform:scale(1);opacity:1;} 50%{transform:scale(1.3);opacity:.75;} }
        @keyframes scaleIn    { from { opacity:0; transform:scale(.9); } to { opacity:1; transform:scale(1); } }
        @keyframes spin       { to { transform:rotate(360deg); } }
        @keyframes slideDown  { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:none; } }

        /* ── SIDEBAR — exact copy from admin.php ── */
        .sidebar { background: var(--sidebar-blue); min-height: 100vh; padding: 25px 15px; color: #fff; }
        .nav-link { color: rgba(255,255,255,.7); padding: 12px 15px; border-radius: 8px; margin-bottom: 5px; display: flex; align-items: center; gap: 10px; text-decoration: none; font-weight: 500; }
        .nav-link:hover, .nav-link.active { background: rgba(255,255,255,.1); color: #fff; }

        /* ── LAYOUT — exact copy from admin.php ── */
        .main-content { padding: 40px; }

        /* ── TOP NAV ROW — exact copy from admin.php ── */
        .top-nav-row { display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem; animation:fadeInUp .5s ease-out forwards; position:relative; z-index:9990; overflow:visible; }

        /* ── STAT CARDS — exact copy from admin.php ── */
        .stat-card { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:20px; box-shadow:0 1px 3px rgba(0,0,0,.1); height:100%; transition:all .3s; opacity:0; animation:fadeInUp .6s ease-out forwards; position:relative; }
        .stat-card:hover { transform:translateY(-5px); box-shadow:0 8px 15px rgba(0,0,0,.1); }
        .card-delay-1{animation-delay:.1s;} .card-delay-2{animation-delay:.2s;}
        .card-delay-3{animation-delay:.3s;} .card-delay-4{animation-delay:.4s;}
        .icon-box { width:45px; height:45px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.2rem; flex-shrink:0; }

        /* ── HELP ICON — exact copy from admin.php ── */
        .card-help-btn { position:absolute; top:10px; right:10px; width:20px; height:20px; border-radius:50%; background:#e2e8f0; color:#64748b; border:none; display:flex; align-items:center; justify-content:center; font-size:.62rem; cursor:pointer; transition:all .2s; padding:0; z-index:2; }
        .card-help-btn:hover { background:var(--primary); color:#fff; transform:scale(1.15); box-shadow:0 3px 8px rgba(67,97,238,.35); }

        /* ── NOTIFICATION BELL — exact copy from admin.php ── */
        .notif-wrapper { position:relative; z-index:9999; }
        .notif-btn { width:40px; height:40px; border-radius:10px; background:#fff; border:1px solid #e2e8f0; display:flex; align-items:center; justify-content:center; cursor:pointer; color:#475569; font-size:1.1rem; box-shadow:0 1px 3px rgba(0,0,0,.08); position:relative; transition:all .2s; }
        .notif-btn:hover { background:#f1f5f9; color:var(--primary); }
        .notif-dot { position:absolute; top:6px; right:6px; width:9px; height:9px; background:#ef4444; border-radius:50%; border:2px solid #fff; animation:pulse-dot 1.8s ease-in-out infinite; }
        .notif-dropdown { display:none; position:absolute; top:calc(100% + 10px); right:0; width:320px; background:#fff; border-radius:12px; border:1px solid #e2e8f0; box-shadow:0 15px 40px rgba(0,0,0,.18); z-index:99999; overflow:hidden; }
        .notif-dropdown.show { display:block; animation:fadeInUp .18s ease-out; }
        .notif-drop-header { padding:14px 18px; border-bottom:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center; }
        .notif-drop-header h6 { margin:0; font-weight:700; font-size:.85rem; }
        .notif-item { display:flex; align-items:flex-start; gap:12px; padding:12px 18px; border-bottom:1px solid #f8fafc; transition:background .15s; color:inherit; }
        .notif-item:hover { background:#f8fafc; }
        .notif-item:last-child { border-bottom:none; }
        .notif-avatar { width:34px; height:34px; border-radius:50%; background:#eef2ff; color:var(--primary); display:flex; align-items:center; justify-content:center; font-size:.75rem; font-weight:700; flex-shrink:0; }
        .notif-item-body .notif-name { font-size:.8rem; font-weight:600; color:#1e293b; }
        .notif-item-body .notif-sub  { font-size:.72rem; color:#94a3b8; margin-top:2px; }
        .notif-drop-footer { padding:10px 18px; text-align:center; border-top:1px solid #f1f5f9; font-size:.78rem; color:var(--primary); font-weight:600; cursor:pointer; }
        .notif-drop-footer:hover { background:#f8fafc; }
        .notif-empty { padding:24px; text-align:center; color:#94a3b8; font-size:.82rem; }

        /* ── PROFILE DROPDOWN — exact copy from admin.php ── */
        .profile-wrapper { position:relative; z-index:9999; }
        .profile-btn { display:flex; align-items:center; gap:9px; background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:6px 13px 6px 7px; cursor:pointer; box-shadow:0 1px 3px rgba(0,0,0,.08); transition:all .2s; }
        .profile-btn:hover { background:#f1f5f9; box-shadow:0 3px 10px rgba(0,0,0,.1); }
        .profile-avatar { width:32px; height:32px; border-radius:50%; background:linear-gradient(135deg,#4361ee,#7c3aed); color:#fff; display:flex; align-items:center; justify-content:center; font-size:.8rem; font-weight:800; flex-shrink:0; }
        .profile-info .p-name { font-size:.8rem; font-weight:700; color:#1e293b; line-height:1.15; }
        .profile-info .p-role { font-size:.67rem; color:#94a3b8; font-weight:500; }
        .p-chevron { color:#94a3b8; font-size:.62rem; margin-left:2px; transition:transform .2s; }
        .profile-wrapper.open .p-chevron { transform:rotate(180deg); }
        .profile-dropdown { display:none; position:absolute; top:calc(100% + 10px); right:0; width:262px; background:#fff; border-radius:14px; border:1px solid #e2e8f0; box-shadow:0 16px 40px rgba(0,0,0,.15); z-index:99999; overflow:hidden; }
        .profile-dropdown.show { display:block; animation:fadeInUp .18s ease-out; }
        .pd-header { padding:20px 18px 16px; background:linear-gradient(135deg,#4361ee 0%,#7c3aed 100%); text-align:center; }
        .pd-avatar { width:54px; height:54px; border-radius:50%; background:rgba(255,255,255,.22); border:2px solid rgba(255,255,255,.45); color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.35rem; font-weight:800; margin:0 auto 10px; }
        .pd-name  { font-size:.95rem; font-weight:700; color:#fff; margin:0; }
        .pd-email { font-size:.73rem; color:rgba(255,255,255,.78); margin:4px 0 0; word-break:break-all; }
        .pd-role  { display:inline-block; margin-top:9px; background:rgba(255,255,255,.2); color:#fff; font-size:.64rem; font-weight:700; letter-spacing:1px; text-transform:uppercase; padding:3px 11px; border-radius:20px; }
        .pd-body  { padding:6px 0; }
        .pd-item  { display:flex; align-items:center; gap:11px; padding:11px 18px; font-size:.82rem; font-weight:600; color:#475569; text-decoration:none; transition:background .15s; background:none; border:none; width:100%; cursor:pointer; }
        .pd-item:hover { background:#f8fafc; color:#1e293b; }
        .pd-item i { width:16px; text-align:center; font-size:.85rem; }
        .pd-item.danger { color:#dc2626; }
        .pd-item.danger:hover { background:#fee2e2; }
        .pd-divider { height:1px; background:#f1f5f9; margin:4px 0; }

        /* ── SEARCH / FILTER BAR ── */
        .search-filter-bar { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:16px 22px; margin-bottom:20px; display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; box-shadow:0 1px 3px rgba(0,0,0,.05); opacity:0; animation:fadeInUp .6s ease-out .35s forwards; }
        .search-wrap { position:relative; }
        .search-wrap i { position:absolute; left:11px; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:.82rem; }
        .search-input { padding:9px 12px 9px 32px; border:1px solid #e2e8f0; border-radius:9px; font-family:'Inter',sans-serif; font-size:.84rem; color:#1e293b; background:#f8fafc; outline:none; transition:all .2s; width:240px; }
        .search-input:focus { border-color:var(--primary); background:#fff; box-shadow:0 0 0 3px rgba(67,97,238,.1); }
        .filter-select { padding:9px 14px; border:1px solid #e2e8f0; border-radius:9px; font-family:'Inter',sans-serif; font-size:.83rem; color:#1e293b; background:#f8fafc; cursor:pointer; outline:none; transition:all .2s; }
        .filter-select:focus { border-color:var(--primary); box-shadow:0 0 0 3px rgba(67,97,238,.1); }

        /* ── TABLE CARD ── */
        .table-card { background:#fff; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; box-shadow:0 4px 6px -1px rgba(0,0,0,.08); opacity:0; animation:fadeInUp .6s ease-out .45s forwards; }
        .table-card-head { padding:16px 22px; border-bottom:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; }
        .table thead th { background:#f1f5f9; color:#64748b; font-size:.73rem; text-transform:uppercase; letter-spacing:.05em; padding:13px 18px; border:none; white-space:nowrap; }
        .table thead th:first-child { padding-left:22px; }
        .table thead th:last-child  { padding-right:22px; }
        .table td { padding:14px 18px; vertical-align:middle; border-bottom:1px solid #f8fafc; font-size:.85rem; }
        .table td:first-child { padding-left:22px; }
        .table td:last-child  { padding-right:22px; }
        .table tbody tr { animation:fadeInLeft .3s ease-out both; }
        .table tbody tr:hover { background:#fafbff; }
        .table tbody tr:last-child td { border-bottom:none; }
        .table tbody tr:nth-child(1){ animation-delay:.04s; }
        .table tbody tr:nth-child(2){ animation-delay:.08s; }
        .table tbody tr:nth-child(3){ animation-delay:.12s; }
        .table tbody tr:nth-child(4){ animation-delay:.16s; }
        .table tbody tr:nth-child(5){ animation-delay:.20s; }
        .table tbody tr:nth-child(6){ animation-delay:.24s; }
        .table tbody tr:nth-child(7){ animation-delay:.28s; }
        .table tbody tr:nth-child(8){ animation-delay:.32s; }
        .table tbody tr:nth-child(9){ animation-delay:.36s; }
        .table tbody tr:nth-child(10){ animation-delay:.40s; }

        /* Visitor avatar + name cell */
        .visitor-avatar { width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg,#4361ee,#7c3aed); color:#fff; display:inline-flex; align-items:center; justify-content:center; font-size:.8rem; font-weight:800; flex-shrink:0; }
        .visitor-name  { font-weight:700; font-size:.86rem; color:#1e293b; }
        .visitor-email { font-size:.75rem; color:#94a3b8; margin-top:1px; }

        /* Token badge */
        .token-badge { font-family:'Courier New',monospace; font-size:.76rem; font-weight:700; background:#eef2ff; color:var(--primary); border:1px solid #c7d2fe; border-radius:6px; padding:3px 8px; letter-spacing:.04em; white-space:nowrap; }

        /* Status badge */
        .status-badge { display:inline-flex; align-items:center; gap:6px; padding:5px 11px; border-radius:20px; font-size:.72rem; font-weight:700; white-space:nowrap; }
        .status-dot   { width:6px; height:6px; border-radius:50%; flex-shrink:0; }

        /* Action buttons — same style as admin.php */
        .btn-approve { background:#dcfce7; color:#166534; border:1px solid #bbf7d0; transition:all .2s; font-size:.8rem; }
        .btn-approve:hover { background:#166534; color:#fff; }
        .btn-reject  { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; transition:all .2s; font-size:.8rem; }
        .btn-reject:hover  { background:#991b1b; color:#fff; }
        .btn-view    { background:#f1f5f9; color:#475569; border:1px solid #e2e8f0; transition:all .2s; font-size:.8rem; }
        .btn-view:hover    { background:#e2e8f0; color:#1e293b; }
        .btn-del     { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; transition:all .2s; font-size:.8rem; }
        .btn-del:hover     { background:#991b1b; color:#fff; }

        /* ── EMPTY STATE ── */
        .empty-state { text-align:center; padding:60px 32px; }
        .empty-icon  { width:68px; height:68px; border-radius:50%; background:#f1f5f9; display:flex; align-items:center; justify-content:center; font-size:1.7rem; margin:0 auto 16px; color:#94a3b8; }

        /* ── PAGINATION — exact copy from admin.php ── */
        .pg-wrap { padding:14px 22px; border-top:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center; font-size:.75rem; color:#64748b; flex-wrap:wrap; gap:10px; }
        .pg-btns { display:flex; gap:5px; }
        .pg-btn  { height:32px; min-width:32px; padding:0 10px; border-radius:7px; border:1px solid #e2e8f0; background:#fff; color:#475569; font-size:.76rem; font-weight:600; cursor:pointer; display:flex; align-items:center; justify-content:center; text-decoration:none; transition:all .15s; }
        .pg-btn:hover:not(.disabled):not(.active) { background:#f1f5f9; color:#1e293b; }
        .pg-btn.active   { background:var(--primary); color:#fff; border-color:var(--primary); }
        .pg-btn.disabled { opacity:.4; pointer-events:none; }

        /* ── MODALS ── */
        .modal-content { border:none; border-radius:16px; overflow:hidden; box-shadow:0 24px 60px rgba(0,0,0,.18); }
        .modal-grad-header { padding:22px 24px; background:linear-gradient(135deg,#4361ee,#7c3aed); color:#fff; display:flex; align-items:center; gap:14px; }

        /* View detail rows */
        .detail-row   { display:flex; align-items:flex-start; gap:11px; padding:11px 0; border-bottom:1px solid #f8fafc; }
        .detail-row:last-child { border-bottom:none; }
        .detail-icon  { width:30px; height:30px; border-radius:8px; background:#eef2ff; color:var(--primary); display:flex; align-items:center; justify-content:center; font-size:.75rem; flex-shrink:0; }
        .detail-label { font-size:.68rem; font-weight:800; text-transform:uppercase; letter-spacing:.06em; color:#94a3b8; margin-bottom:2px; }
        .detail-value { font-size:.86rem; font-weight:600; color:#1e293b; }

        /* Receipt */
        .receipt-box { width:100%; height:220px; border-radius:10px; border:1px solid #e2e8f0; background:#f8fafc; display:flex; align-items:center; justify-content:center; overflow:hidden; }
        .receipt-box img { max-width:100%; max-height:100%; object-fit:contain; cursor:zoom-in; }

        /* Help bullets */
        .help-bullet { display:flex; align-items:flex-start; gap:10px; padding:9px 0; border-bottom:1px solid #f1f5f9; font-size:.82rem; color:#475569; }
        .help-bullet:last-child { border-bottom:none; }
        .help-bullet i { margin-top:2px; flex-shrink:0; }

        /* ── CARD HELP MODAL (same as admin.php) ── */
        #cardHelpModal .modal-content { border:none; border-radius:16px; overflow:hidden; }
        #cardHelpModal .modal-dialog  { animation:scaleIn .22s ease-out; }

        /* ── TOAST ── */
        .toast-container { position:fixed; top:18px; right:18px; z-index:999999; display:flex; flex-direction:column; gap:8px; }
        .ve-toast { background:#fff; border:1px solid #e2e8f0; border-radius:11px; padding:12px 16px; box-shadow:0 8px 28px rgba(0,0,0,.14); min-width:260px; display:flex; align-items:center; gap:11px; font-size:.83rem; font-weight:600; animation:slideDown .22s ease-out; transition:opacity .3s, transform .3s; }
        .ve-toast.success { border-left:4px solid var(--success); }
        .ve-toast.error   { border-left:4px solid #ef4444; }

        @media(max-width:768px) {
            .main-content { padding:24px 16px 40px; }
            .search-input { width:160px; }
        }
    </style>
</head>
<body>

<!-- TOAST CONTAINER -->
<div class="toast-container" id="toastContainer"></div>

<div class="container-fluid p-0">
<div class="row g-0">

    <!-- ════════════════
         SIDEBAR — exact copy from admin.php
         ════════════════ -->
    <div class="col-md-2 sidebar d-none d-lg-block">
        <h4 class="fw-bold mb-5 px-3">VISITEASE</h4>
        <nav>
            <a href="admin.php"           class="nav-link"><i class="fas fa-grid-2"></i> Dashboard</a>
            <a href="manage_schedule.php" class="nav-link"><i class="fas fa-calendar"></i> Schedule</a>
            <a href="visitors.php"        class="nav-link active"><i class="fas fa-users"></i> Visitors</a>
            <a href="history.php"         class="nav-link"><i class="fas fa-history"></i> History</a>
            <a href="logout.php"          class="nav-link text-danger mt-5"><i class="fas fa-power-off"></i> Logout</a>
        </nav>
    </div>

    <!-- ════════════════
         MAIN CONTENT
         ════════════════ -->
    <div class="col-md-10 main-content">

        <!-- TOP NAV ROW (same structure as admin.php) -->
        <div class="top-nav-row">
            <div>
                <h2 class="fw-bold m-0">Visitors</h2>
                <small class="text-muted">All museum visitor bookings &amp; records</small>
            </div>

            <div class="d-flex align-items-center gap-3">

                <!-- NOTIFICATION BELL (exact from admin.php) -->
                <div class="notif-wrapper" id="notifWrapper" data-pending-ids="<?php echo htmlspecialchars($notif_ids_str); ?>">
                    <div class="notif-btn" onclick="toggleNotif(event)">
                        <i class="fas fa-bell"></i>
                        <?php if ($notif_count > 0): ?>
                            <span class="notif-dot" id="notifDot"></span>
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
                                $n_date   = $n['visit_date'] ? date('M d, Y', strtotime($n['visit_date'])) : 'No date';
                                $n_time   = $n['start_time'] ? date('h:i A',  strtotime($n['start_time'])) : '';
                                $initials = strtoupper(substr($n['name'], 0, 1));
                            ?>
                            <div class="notif-item">
                                <div class="notif-avatar"><?php echo $initials; ?></div>
                                <div class="notif-item-body">
                                    <div class="notif-name">📋 <?php echo htmlspecialchars($n['name']); ?> booked a visit</div>
                                    <div class="notif-sub">📅 <?php echo $n_date; ?><?php echo $n_time ? ' · '.$n_time : ''; ?> &nbsp;·&nbsp; Token: <strong>#<?php echo $n['token']; ?></strong></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <div class="notif-drop-footer" onclick="window.location='admin.php'">View all pending requests →</div>
                        <?php else: ?>
                            <div class="notif-empty">
                                <i class="fas fa-check-circle text-success mb-2" style="font-size:1.5rem;display:block"></i>
                                No pending notifications
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- PROFILE DROPDOWN (exact from admin.php) -->
                <div class="profile-wrapper" id="profileWrapper">
                    <div class="profile-btn" onclick="toggleProfile(event)">
                        <div class="profile-avatar"><?php echo $admin_initial; ?></div>
                        <div class="profile-info">
                            <div class="p-name"><?php echo htmlspecialchars($admin_name); ?></div>
                            <div class="p-role">Administrator</div>
                        </div>
                        <i class="fas fa-chevron-down p-chevron"></i>
                    </div>
                    <div class="profile-dropdown" id="profileDropdown">
                        <div class="pd-header">
                            <div class="pd-avatar"><?php echo $admin_initial; ?></div>
                            <div class="pd-name"><?php echo htmlspecialchars($admin_name); ?></div>
                            <div class="pd-email"><?php echo htmlspecialchars($admin_email); ?></div>
                            <span class="pd-role">Administrator</span>
                        </div>
                        <div class="pd-body">
                            <a href="admin.php"           class="pd-item"><i class="fas fa-grid-2"       style="color:var(--primary);"></i> Dashboard</a>
                            <a href="manage_schedule.php" class="pd-item"><i class="fas fa-calendar-alt"  style="color:#0891b2;"></i> Manage Schedule</a>
                            <a href="history.php"         class="pd-item"><i class="fas fa-history"       style="color:#7c3aed;"></i> Booking History</a>
                            <div class="pd-divider"></div>
                            <a href="logout.php"          class="pd-item danger"><i class="fas fa-sign-out-alt"></i> Sign Out</a>
                        </div>
                    </div>
                </div>

                <!-- EXPORT + HELP -->
                <button class="btn btn-success fw-bold shadow-sm" style="border-radius:8px; font-size:.82rem;" onclick="window.location='visitors.php?ajax=export_csv'">
                    <i class="fas fa-download me-1"></i> Export CSV
                </button>
                <button class="btn btn-outline-secondary fw-bold" style="border-radius:8px; font-size:.82rem;" onclick="openHelp()">
                    <i class="fas fa-question-circle me-1"></i> Help
                </button>
            </div>
        </div><!-- end top-nav-row -->

        <!-- ════════════════════════════════════
             STAT CARDS (same style as admin.php)
             ════════════════════════════════════ -->
        <div class="row g-4 mb-4">
            <div class="col-md-2">
                <div class="stat-card card-delay-1" style="border-left:5px solid var(--primary);">
                    <button class="card-help-btn" onclick="showCardHelp('total')"><i class="fas fa-question"></i></button>
                    <div class="d-flex justify-content-between align-items-start">
                        <div><p class="text-muted small mb-1 fw-bold text-uppercase" style="font-size:.68rem;">Total</p><h3 class="fw-bold mb-0"><?php echo number_format($stat_total); ?></h3></div>
                        <div class="icon-box" style="background:#eef2ff;color:var(--primary);"><i class="fas fa-users"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card card-delay-1" style="border-left:5px solid #f59e0b;">
                    <button class="card-help-btn" onclick="showCardHelp('pending')"><i class="fas fa-question"></i></button>
                    <div class="d-flex justify-content-between align-items-start">
                        <div><p class="text-muted small mb-1 fw-bold text-uppercase" style="font-size:.68rem;">Pending</p><h3 class="fw-bold mb-0 text-warning"><?php echo number_format($stat_pending); ?></h3></div>
                        <div class="icon-box" style="background:#fef9c3;color:#92400e;"><i class="fas fa-hourglass-half"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card card-delay-2" style="border-left:5px solid var(--success);">
                    <button class="card-help-btn" onclick="showCardHelp('confirmed')"><i class="fas fa-question"></i></button>
                    <div class="d-flex justify-content-between align-items-start">
                        <div><p class="text-muted small mb-1 fw-bold text-uppercase" style="font-size:.68rem;">Confirmed</p><h3 class="fw-bold mb-0 text-success"><?php echo number_format($stat_confirmed); ?></h3></div>
                        <div class="icon-box" style="background:#ecfdf5;color:var(--success);"><i class="fas fa-check-circle"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card card-delay-2" style="border-left:5px solid #3b82f6;">
                    <button class="card-help-btn" onclick="showCardHelp('completed')"><i class="fas fa-question"></i></button>
                    <div class="d-flex justify-content-between align-items-start">
                        <div><p class="text-muted small mb-1 fw-bold text-uppercase" style="font-size:.68rem;">Completed</p><h3 class="fw-bold mb-0 text-primary"><?php echo number_format($stat_completed); ?></h3></div>
                        <div class="icon-box" style="background:#dbeafe;color:#1e40af;"><i class="fas fa-flag-checkered"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card card-delay-3" style="border-left:5px solid #ef4444;">
                    <button class="card-help-btn" onclick="showCardHelp('rejected')"><i class="fas fa-question"></i></button>
                    <div class="d-flex justify-content-between align-items-start">
                        <div><p class="text-muted small mb-1 fw-bold text-uppercase" style="font-size:.68rem;">Rejected</p><h3 class="fw-bold mb-0 text-danger"><?php echo number_format($stat_rejected); ?></h3></div>
                        <div class="icon-box" style="background:#fee2e2;color:#991b1b;"><i class="fas fa-times-circle"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card card-delay-3" style="border-left:5px solid #7c3aed;">
                    <button class="card-help-btn" onclick="showCardHelp('guests')"><i class="fas fa-question"></i></button>
                    <div class="d-flex justify-content-between align-items-start">
                        <div><p class="text-muted small mb-1 fw-bold text-uppercase" style="font-size:.68rem;">Guests Served</p><h3 class="fw-bold mb-0" style="color:#7c3aed;"><?php echo number_format($stat_guests); ?></h3></div>
                        <div class="icon-box" style="background:#f3e8ff;color:#7c3aed;"><i class="fas fa-person-walking"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ════════════════════════════════════
             SEARCH & FILTER BAR
             ════════════════════════════════════ -->
        <div class="search-filter-bar">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <form method="GET" id="filterForm" style="display:contents;">
                    <div class="search-wrap">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" id="searchInput" class="search-input"
                               placeholder="Search name, email or token…"
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <select name="filter" class="filter-select" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <?php foreach(['Pending','Confirmed','Completed','Rejected','Cancelled'] as $st): ?>
                        <option value="<?php echo $st; ?>" <?php echo $filter===$st?'selected':''; ?>><?php echo $st; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="pg" value="1">
                </form>
                <span class="small text-muted fw-bold">
                    <?php echo number_format($total_filtered); ?> result<?php echo $total_filtered!==1?'s':''; ?>
                    <?php if($search||$filter): ?>
                        &nbsp;<a href="visitors.php" style="color:var(--primary);font-size:.78rem;text-decoration:none;">Clear ×</a>
                    <?php endif; ?>
                </span>
            </div>
            <span class="badge bg-primary bg-opacity-10 text-primary fw-bold" style="font-size:.72rem;">
                Page <?php echo $page; ?> of <?php echo $total_pages; ?>
            </span>
        </div>

        <!-- ════════════════════════════════════
             VISITORS TABLE
             ════════════════════════════════════ -->
        <div class="table-card">
            <div class="table-card-head">
                <div>
                    <h5 class="fw-bold m-0"><i class="fas fa-list me-2 text-primary"></i>Visitor Records</h5>
                    <small class="text-muted">Live data from booking submissions — no dummy records</small>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-warning text-dark fw-bold"><i class="fas fa-hourglass-half me-1"></i><?php echo $stat_pending; ?> Pending</span>
                    <span class="badge bg-success fw-bold"><i class="fas fa-check me-1"></i><?php echo $stat_confirmed; ?> Confirmed</span>
                </div>
            </div>

            <div style="overflow-x:auto;">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th style="width:36px;">#</th>
                            <th>Token</th>
                            <th>Visitor</th>
                            <th>Visit Date</th>
                            <th>Time</th>
                            <th>Pax</th>
                            <th>Status</th>
                            <th class="text-end" style="padding-right:22px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($bookings_res && $bookings_res->num_rows > 0):
                        $row_num = $offset + 1;
                        while ($b = $bookings_res->fetch_assoc()):
                            $initials     = strtoupper(substr($b['name'], 0, 1));
                            $vdate        = $b['sched_date'] ? date('M d, Y', strtotime($b['sched_date'])) : '<span class="text-danger small">No Date</span>';
                            $vtime        = $b['start_time'] ? date('h:i A', strtotime($b['start_time'])) : '—';
                            $receipt_path = !empty($b['gcash_receipt']) ? 'uploads/receipts/' . $b['gcash_receipt'] : '';
                    ?>
                    <tr id="row-<?php echo $b['id']; ?>">
                        <!-- # -->
                        <td style="color:#94a3b8;font-size:.75rem;font-weight:600;"><?php echo $row_num++; ?></td>

                        <!-- Token -->
                        <td><span class="token-badge">#<?php echo htmlspecialchars($b['token']); ?></span></td>

                        <!-- Visitor -->
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="visitor-avatar"><?php echo $initials; ?></div>
                                <div>
                                    <div class="visitor-name"><?php echo htmlspecialchars($b['name']); ?></div>
                                    <div class="visitor-email"><?php echo htmlspecialchars($b['email'] ?: '—'); ?></div>
                                </div>
                            </div>
                        </td>

                        <!-- Visit Date -->
                        <td><span class="fw-bold" style="font-size:.84rem;"><?php echo $vdate; ?></span></td>

                        <!-- Time -->
                        <td><span class="small fw-bold text-primary"><?php echo $vtime; ?></span></td>

                        <!-- Pax -->
                        <td><span class="fw-bold" style="font-size:.9rem;"><i class="fas fa-user" style="color:var(--primary);font-size:.65rem;"></i> <?php echo $b['guests']; ?></span></td>

                        <!-- Status -->
                        <td><?php echo statusBadge($b['status']); ?></td>

                        <!-- Actions -->
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <button class="btn btn-view btn-sm" title="View Details" onclick="viewBooking(<?php echo $b['id']; ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <?php if ($b['status'] === 'Pending'): ?>
                                <button class="btn btn-approve btn-sm" title="Approve" onclick="doAction('approve',<?php echo $b['id']; ?>,'<?php echo addslashes($b['name']); ?>')">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn btn-reject btn-sm" title="Reject" onclick="doAction('reject',<?php echo $b['id']; ?>,'<?php echo addslashes($b['name']); ?>')">
                                    <i class="fas fa-times"></i>
                                </button>
                                <?php endif; ?>
                                <button class="btn btn-del btn-sm" title="Delete" onclick="openDelete(<?php echo $b['id']; ?>,'<?php echo addslashes(htmlspecialchars($b['name'])); ?>')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fas fa-user-slash"></i></div>
                                <div class="fw-bold mb-1" style="font-size:.95rem;">
                                    <?php echo ($search||$filter) ? 'No visitors match your search.' : 'No visitor bookings yet.'; ?>
                                </div>
                                <small class="text-muted">
                                    <?php echo ($search||$filter) ? 'Try clearing your filter.' : 'Bookings submitted through the website will appear here automatically.'; ?>
                                </small>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <?php if ($total_pages > 1): ?>
            <div class="pg-wrap">
                <span>Showing <?php echo min($offset+1,$total_filtered); ?>–<?php echo min($offset+$per_pg,$total_filtered); ?> of <?php echo number_format($total_filtered); ?> visitors</span>
                <div class="pg-btns">
                    <a class="pg-btn <?php echo $page<=1?'disabled':''; ?>"
                       href="?pg=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>&filter=<?php echo urlencode($filter); ?>">
                        <i class="fas fa-chevron-left" style="font-size:.6rem;"></i>
                    </a>
                    <?php
                    $s2 = max(1,$page-2); $e2 = min($total_pages,$page+2);
                    if($s2>1) echo '<span class="pg-btn disabled">…</span>';
                    for($i=$s2;$i<=$e2;$i++): ?>
                    <a class="pg-btn <?php echo $i===$page?'active':''; ?>"
                       href="?pg=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&filter=<?php echo urlencode($filter); ?>"><?php echo $i; ?></a>
                    <?php endfor;
                    if($e2<$total_pages) echo '<span class="pg-btn disabled">…</span>'; ?>
                    <a class="pg-btn <?php echo $page>=$total_pages?'disabled':''; ?>"
                       href="?pg=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>&filter=<?php echo urlencode($filter); ?>">
                        <i class="fas fa-chevron-right" style="font-size:.6rem;"></i>
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div><!-- end table-card -->

    </div><!-- end main-content -->
</div>
</div>

<!-- ════════════════════════════════════════
     VIEW BOOKING MODAL
     (same style as admin.php detailsModal)
     ════════════════════════════════════════ -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-grad-header">
                <div id="viewAvatar" style="width:48px;height:48px;border-radius:50%;background:rgba(255,255,255,.22);border:2px solid rgba(255,255,255,.4);display:flex;align-items:center;justify-content:center;font-size:1.2rem;font-weight:800;flex-shrink:0;">J</div>
                <div style="flex:1;">
                    <div class="fw-bold" style="font-size:1rem;" id="viewName">—</div>
                    <div style="font-size:.78rem;opacity:.8;" id="viewEmail">—</div>
                </div>
                <div id="viewStatusBadge"></div>
                <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <!-- Left: booking details -->
                    <div class="col-md-6 p-4" style="border-right:1px solid #f1f5f9;">
                        <div class="detail-row">
                            <div class="detail-icon"><i class="fas fa-hashtag"></i></div>
                            <div><div class="detail-label">Entrance Token</div><div class="detail-value" id="viewToken" style="font-family:'Courier New',monospace;letter-spacing:.1em;color:var(--primary);font-size:.9rem;">—</div></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-icon"><i class="fas fa-calendar"></i></div>
                            <div><div class="detail-label">Visit Date</div><div class="detail-value" id="viewDate">—</div></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-icon"><i class="fas fa-clock"></i></div>
                            <div><div class="detail-label">Visit Time</div><div class="detail-value" id="viewTime">—</div></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-icon"><i class="fas fa-users"></i></div>
                            <div><div class="detail-label">Number of Guests</div><div class="detail-value" id="viewPax">—</div></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-icon"><i class="fas fa-mobile-alt"></i></div>
                            <div><div class="detail-label">GCash Sender Name</div><div class="detail-value" id="viewGcashName">—</div></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-icon"><i class="fas fa-receipt"></i></div>
                            <div><div class="detail-label">GCash Reference No.</div><div class="detail-value" id="viewGcashRef" style="color:var(--primary);">—</div></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-icon"><i class="fas fa-comment"></i></div>
                            <div><div class="detail-label">Special Request</div><div class="detail-value" id="viewRequest" style="font-style:italic;color:#64748b;">—</div></div>
                        </div>
                        <div class="detail-row" style="border-bottom:none;">
                            <div class="detail-icon"><i class="fas fa-calendar-plus"></i></div>
                            <div><div class="detail-label">Booked At</div><div class="detail-value" id="viewCreated" style="font-size:.8rem;color:#64748b;">—</div></div>
                        </div>
                    </div>
                    <!-- Right: GCash Receipt -->
                    <div class="col-md-6 p-4 d-flex flex-column">
                        <div class="detail-label mb-2">Payment Receipt (GCash)</div>
                        <div class="receipt-box flex-grow-1" id="viewReceiptBox">
                            <span class="text-muted small"><i class="fas fa-image me-1"></i>No receipt uploaded</span>
                        </div>
                        <a href="#" id="viewReceiptLink" target="_blank"
                           class="btn btn-sm btn-outline-primary mt-2 w-100" style="display:none; border-radius:8px;">
                            <i class="fas fa-external-link-alt me-1"></i> View Full Image
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="padding:12px 20px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-approve btn-sm fw-bold px-3" id="viewApproveBtn" style="display:none;border-radius:8px;padding:8px 16px;" onclick="doActionFromView('approve')">
                    <i class="fas fa-check me-1"></i> Approve
                </button>
                <button type="button" class="btn btn-reject btn-sm fw-bold px-3" id="viewRejectBtn" style="display:none;border-radius:8px;padding:8px 16px;" onclick="doActionFromView('reject')">
                    <i class="fas fa-times me-1"></i> Reject
                </button>
            </div>
        </div>
    </div>
</div>

<!-- DELETE CONFIRM MODAL -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:370px;">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div style="width:60px;height:60px;border-radius:50%;background:#fee2e2;display:flex;align-items:center;justify-content:center;font-size:1.4rem;color:#ef4444;margin:0 auto 16px;">
                    <i class="fas fa-trash"></i>
                </div>
                <div class="fw-bold mb-1" style="font-size:1rem;">Delete Visitor?</div>
                <p class="text-muted" style="font-size:.84rem;">
                    You are about to permanently delete <strong id="deleteVisitorName">this visitor</strong>. This cannot be undone.
                </p>
                <input type="hidden" id="deleteVisitorId">
                <div class="d-flex gap-2 justify-content-center mt-3">
                    <button class="btn btn-secondary px-4" data-bs-dismiss="modal" style="border-radius:9px;">Cancel</button>
                    <button class="btn btn-danger fw-bold px-4" onclick="confirmDelete()" style="border-radius:9px;">
                        <i class="fas fa-trash me-1"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- HELP MODAL -->
<div class="modal fade" id="helpModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content">
            <div class="modal-body p-4 text-center">
                <div style="width:56px;height:56px;border-radius:50%;background:#eef2ff;color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:1.4rem;margin:0 auto 14px;">
                    <i class="fas fa-users"></i>
                </div>
                <span style="display:inline-block;background:#eef2ff;color:var(--primary);font-size:.68rem;font-weight:800;letter-spacing:.08em;text-transform:uppercase;padding:3px 12px;border-radius:20px;margin-bottom:12px;">Page Guide</span>
                <div class="fw-bold mb-2" style="font-size:1.05rem;">Visitors Management</div>
                <p class="text-muted mb-3" style="font-size:.83rem;line-height:1.65;">
                    This page shows <strong>all real visitors</strong> who submitted bookings through the VisitEase website.
                    Data is pulled directly from your <code>bookings</code> database table — zero dummy records.
                </p>
                <div style="text-align:left;border:1px solid #f1f5f9;border-radius:11px;overflow:hidden;margin-bottom:20px;">
                    <div class="help-bullet"><i class="fas fa-hashtag" style="color:var(--primary);"></i><span><strong>Token</strong> — Unique code auto-assigned to each booking.</span></div>
                    <div class="help-bullet"><i class="fas fa-eye" style="color:#0891b2;"></i><span><strong>View</strong> — See full booking + GCash receipt in a modal.</span></div>
                    <div class="help-bullet"><i class="fas fa-check" style="color:#166534;"></i><span><strong>Approve</strong> — Confirm a pending booking; sends email to visitor.</span></div>
                    <div class="help-bullet"><i class="fas fa-times" style="color:#991b1b;"></i><span><strong>Reject</strong> — Decline a pending booking; restores schedule slot.</span></div>
                    <div class="help-bullet"><i class="fas fa-trash" style="color:#ef4444;"></i><span><strong>Delete</strong> — Permanently remove a booking record.</span></div>
                    <div class="help-bullet"><i class="fas fa-download" style="color:var(--success);"></i><span><strong>Export CSV</strong> — Download all visitor data as a spreadsheet.</span></div>
                </div>
                <button class="btn btn-primary fw-bold w-100" data-bs-dismiss="modal" style="border-radius:10px;">Got it! 👍</button>
            </div>
        </div>
    </div>
</div>

<!-- CARD HELP MODAL (same as admin.php) -->
<div class="modal fade" id="cardHelpModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:380px;">
        <div class="modal-content border-0">
            <div class="modal-body p-4 text-center">
                <div id="cardHelpInner"></div>
                <button class="btn btn-primary w-100 fw-bold mt-3" data-bs-dismiss="modal" style="border-radius:10px;">Got it!</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* ══ CARD HELP DATA ══ */
const cardHelp = {
    total:     { icon:'fas fa-users',         bg:'#eef2ff', col:'#4361ee', tag:'All Records',    title:'Total Bookings',   desc:'The <strong>total number of booking submissions</strong> from visitors — all statuses combined.' },
    pending:   { icon:'fas fa-hourglass-half', bg:'#fef9c3', col:'#92400e', tag:'Needs Action',  title:'Pending Bookings', desc:'Bookings <strong>waiting for your approval or rejection</strong>. Review them promptly.' },
    confirmed: { icon:'fas fa-check-circle',   bg:'#ecfdf5', col:'#10b981', tag:'Approved',      title:'Confirmed',        desc:'Bookings you have <strong>approved</strong>. Visitor received an email with entrance token.' },
    completed: { icon:'fas fa-flag-checkered', bg:'#dbeafe', col:'#1e40af', tag:'Done',           title:'Completed',        desc:'Bookings marked <strong>Completed</strong> — the visit has already taken place.' },
    rejected:  { icon:'fas fa-times-circle',   bg:'#fee2e2', col:'#991b1b', tag:'Declined',       title:'Rejected',         desc:'Bookings you <strong>rejected</strong> due to invalid payment or full capacity.' },
    guests:    { icon:'fas fa-person-walking', bg:'#f3e8ff', col:'#7c3aed', tag:'Visitor Count', title:'Guests Served',    desc:'Total <strong>individual guests (pax)</strong> across all confirmed bookings.' },
};
function showCardHelp(key) {
    const d = cardHelp[key]; if (!d) return;
    document.getElementById('cardHelpInner').innerHTML = `
        <div style="width:52px;height:52px;border-radius:50%;background:${d.bg};color:${d.col};display:flex;align-items:center;justify-content:center;font-size:1.3rem;margin:0 auto 12px;"><i class="${d.icon}"></i></div>
        <span style="display:inline-block;background:${d.bg};color:${d.col};font-size:.68rem;font-weight:800;letter-spacing:.07em;text-transform:uppercase;padding:3px 10px;border-radius:20px;margin-bottom:10px;">${d.tag}</span>
        <div style="font-size:1rem;font-weight:800;color:#1e293b;margin-bottom:7px;">${d.title}</div>
        <p style="font-size:.83rem;color:#475569;line-height:1.65;margin:0;">${d.desc}</p>`;
    new bootstrap.Modal(document.getElementById('cardHelpModal')).show();
}

/* ══ TOAST ══ */
function showToast(msg, type = 'success') {
    const icon = type==='success' ? 'fa-check-circle' : 'fa-times-circle';
    const col  = type==='success' ? 'var(--success)' : '#ef4444';
    const t = document.createElement('div');
    t.className = `ve-toast ${type}`;
    t.innerHTML = `<i class="fas ${icon}" style="color:${col};font-size:1rem;"></i><span>${msg}</span>`;
    document.getElementById('toastContainer').appendChild(t);
    setTimeout(() => { t.style.opacity='0'; t.style.transform='translateY(-6px)'; setTimeout(()=>t.remove(),340); }, 3200);
}

/* ══ LIVE SEARCH ══ */
let st;
document.getElementById('searchInput').addEventListener('input', function () {
    clearTimeout(st);
    st = setTimeout(() => document.getElementById('filterForm').submit(), 450);
});

/* ══ NOTIFICATION BELL (same as admin.php) ══ */
(function() {
    const w   = document.getElementById('notifWrapper'); if (!w) return;
    const ids = w.dataset.pendingIds || '';
    const dot = document.getElementById('notifDot');
    if (dot && ids && ids === localStorage.getItem('ve_seen_notif_ids')) dot.style.display = 'none';
})();
function toggleNotif(e) {
    e.stopPropagation();
    document.getElementById('profileDropdown').classList.remove('show');
    document.getElementById('profileWrapper').classList.remove('open');
    document.getElementById('notifDropdown').classList.toggle('show');
    const ids = document.getElementById('notifWrapper').dataset.pendingIds || '';
    if (ids) localStorage.setItem('ve_seen_notif_ids', ids);
    const dot = document.getElementById('notifDot');
    if (dot) dot.style.display = 'none';
}

/* ══ PROFILE DROPDOWN (same as admin.php) ══ */
function toggleProfile(e) {
    e.stopPropagation();
    document.getElementById('notifDropdown').classList.remove('show');
    document.getElementById('profileDropdown').classList.toggle('show');
    document.getElementById('profileWrapper').classList.toggle('open');
}
document.addEventListener('click', function (e) {
    if (!document.getElementById('notifWrapper').contains(e.target))
        document.getElementById('notifDropdown').classList.remove('show');
    if (!document.getElementById('profileWrapper').contains(e.target)) {
        document.getElementById('profileDropdown').classList.remove('show');
        document.getElementById('profileWrapper').classList.remove('open');
    }
});

/* ══ HELP ══ */
function openHelp() { new bootstrap.Modal(document.getElementById('helpModal')).show(); }

/* ══ STATUS MAP ══ */
const SM = {
    'Pending'  :['#fef9c3','#854d0e','⏳ Pending'],
    'Confirmed':['#dcfce7','#166534','✅ Confirmed'],
    'Completed':['#dbeafe','#1e40af','🏁 Completed'],
    'Rejected' :['#fee2e2','#991b1b','❌ Rejected'],
    'Cancelled':['#f3e8ff','#6b21a8','🚫 Cancelled'],
};

/* ══ VIEW BOOKING ══ */
let currentViewId = null;
function viewBooking(id) {
    currentViewId = id;
    fetch(`visitors.php?ajax=get&id=${id}`)
        .then(r => r.json())
        .then(res => {
            if (!res.success) { showToast('Could not load booking.','error'); return; }
            const b  = res.data;
            const sc = SM[b.status] || ['#f1f5f9','#475569', b.status];

            // Header
            document.getElementById('viewAvatar').textContent     = b.name.charAt(0).toUpperCase();
            document.getElementById('viewName').textContent       = b.name;
            document.getElementById('viewEmail').textContent      = b.email || '—';
            document.getElementById('viewStatusBadge').innerHTML  =
                `<span style="background:rgba(255,255,255,.22);color:#fff;padding:4px 13px;border-radius:20px;font-size:.73rem;font-weight:700;">${sc[2]}</span>`;

            // Fields
            document.getElementById('viewToken').textContent     = '#' + (b.token || '—');
            document.getElementById('viewDate').textContent      = b.sched_date ? new Date(b.sched_date + 'T00:00:00').toLocaleDateString('en-PH',{year:'numeric',month:'long',day:'numeric'}) : '—';
            document.getElementById('viewTime').textContent      = b.start_time  ? fmt12(b.start_time) : '—';
            document.getElementById('viewPax').textContent       = (b.guests || '—') + ' guest' + (b.guests != 1 ? 's' : '');
            document.getElementById('viewGcashName').textContent = b.gcash_name     || '—';
            document.getElementById('viewGcashRef').textContent  = b.gcash_ref      || '—';
            document.getElementById('viewRequest').textContent   = b.special_request || 'None';
            document.getElementById('viewCreated').textContent   = b.created_at     || '—';

            // Receipt
            const box  = document.getElementById('viewReceiptBox');
            const link = document.getElementById('viewReceiptLink');
            box.innerHTML = '';
            if (b.gcash_receipt) {
                const path = 'uploads/receipts/' + b.gcash_receipt;
                link.style.display = 'block'; link.href = path;
                const img = document.createElement('img');
                img.src = path; img.alt = 'GCash Receipt';
                img.onclick = () => window.open(path, '_blank');
                box.appendChild(img);
            } else {
                link.style.display = 'none';
                box.innerHTML = '<span class="text-muted small"><i class="fas fa-image me-1"></i>No receipt uploaded</span>';
            }

            // Approve / Reject buttons (only for Pending)
            document.getElementById('viewApproveBtn').style.display = b.status === 'Pending' ? 'inline-block' : 'none';
            document.getElementById('viewRejectBtn').style.display  = b.status === 'Pending' ? 'inline-block' : 'none';

            new bootstrap.Modal(document.getElementById('viewModal')).show();
        })
        .catch(() => showToast('Network error.','error'));
}
function fmt12(t) {
    const [h,m] = t.split(':'); const hr = parseInt(h);
    return `${hr%12||12}:${m} ${hr>=12?'PM':'AM'}`;
}
function doActionFromView(action) {
    if (!currentViewId) return;
    const name = document.getElementById('viewName').textContent;
    bootstrap.Modal.getInstance(document.getElementById('viewModal')).hide();
    doAction(action, currentViewId, name);
}

/* ══ APPROVE / REJECT ══ */
function doAction(action, id, name) {
    const label = action === 'approve' ? 'Approve' : 'Reject';
    const emoji = action === 'approve' ? '✅' : '❌';
    if (!confirm(`${emoji} ${label} booking for "${name}"?`)) return;
    const fd = new FormData(); fd.append('id', id);
    fetch(`visitors.php?ajax=${action}`, {method:'POST',body:fd})
        .then(r => r.json())
        .then(res => {
            if (res.success) { showToast(res.message || `${label}d!`, 'success'); setTimeout(()=>location.reload(), 900); }
            else showToast(res.message || 'Error.', 'error');
        })
        .catch(() => showToast('Network error.','error'));
}

/* ══ DELETE ══ */
function openDelete(id, name) {
    document.getElementById('deleteVisitorId').value       = id;
    document.getElementById('deleteVisitorName').textContent = name;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
function confirmDelete() {
    const id = document.getElementById('deleteVisitorId').value;
    const fd = new FormData(); fd.append('id', id);
    fetch('visitors.php?ajax=delete', {method:'POST',body:fd})
        .then(r => r.json())
        .then(res => {
            bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
            if (res.success) {
                const row = document.getElementById(`row-${id}`);
                if (row) { row.style.transition='opacity .3s,transform .3s'; row.style.opacity='0'; row.style.transform='translateX(20px)'; setTimeout(()=>row.remove(),350); }
                showToast('Visitor deleted.','success');
            } else showToast('Delete failed.','error');
        })
        .catch(() => showToast('Network error.','error'));
}
</script>
</body>
</html>