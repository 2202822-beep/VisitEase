<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
include 'db.php';

// ── DELETE ACTION ──
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn->query("DELETE FROM bookings WHERE id = $id AND status != 'Pending'");
    header("Location: history.php");
    exit();
}

// ── BULK DELETE ──
if (isset($_POST['bulk_delete']) && !empty($_POST['booking_ids'])) {
    foreach ($_POST['booking_ids'] as $id) {
        $id = intval($id);
        $conn->query("DELETE FROM bookings WHERE id = $id AND status != 'Pending'");
    }
    header("Location: history.php");
    exit();
}

// ── FETCH HISTORY ──
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$where  = "WHERE b.status != 'Pending'";
if ($filter === 'confirmed') $where .= " AND b.status = 'Confirmed'";
if ($filter === 'rejected')  $where .= " AND b.status = 'Rejected'";

$result = $conn->query("
    SELECT b.*, s.date as visit_date, s.start_time
    FROM bookings b
    LEFT JOIN schedule_settings s ON b.schedule_id = s.id
    $where
    ORDER BY b.id DESC
");

// ── COUNTS ──
$total     = $conn->query("SELECT COUNT(*) as c FROM bookings WHERE status != 'Pending'")->fetch_assoc()['c'];
$confirmed = $conn->query("SELECT COUNT(*) as c FROM bookings WHERE status = 'Confirmed'")->fetch_assoc()['c'];
$rejected  = $conn->query("SELECT COUNT(*) as c FROM bookings WHERE status = 'Rejected'")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>History | VisitEase Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary:   #4361ee;
            --success:   #10b981;
            --danger:    #ef4444;
            --sidebar:   #1e1b4b;
            --bg:        #f8fafc;
            --border:    #e2e8f0;
            --muted:     #94a3b8;
        }

        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: #1e293b; overflow-x: hidden; }

        /* ── ANIMATIONS ── */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .anim { opacity: 0; animation: fadeInUp .55s ease-out forwards; }
        .a1 { animation-delay: .05s; } .a2 { animation-delay: .15s; }
        .a3 { animation-delay: .25s; } .a4 { animation-delay: .35s; }

        /* ── SIDEBAR ── */
        .sidebar {
            background: linear-gradient(175deg, var(--sidebar) 0%, #312e81 100%);
            min-height: 100vh; padding: 28px 16px; color: white;
            position: sticky; top: 0; height: 100vh; overflow-y: auto;
        }
        .brand { font-size: 1.15rem; font-weight: 800; letter-spacing: 3px; color: #fff; text-align: center; margin-bottom: 40px; display: block; }
        .brand span { color: #818cf8; }
        .nav-link {
            color: rgba(255,255,255,.65); padding: 11px 16px; border-radius: 10px;
            margin-bottom: 4px; font-weight: 600; font-size: .85rem;
            transition: all .25s; display: flex; align-items: center; gap: 10px; text-decoration: none;
        }
        .nav-link i { width: 18px; text-align: center; }
        .nav-link:hover, .nav-link.active { background: rgba(255,255,255,.12); color: #fff; transform: translateX(4px); }

        /* ── MAIN ── */
        .main-content { padding: 38px 40px; }

        /* ── STAT PILLS ── */
        .stat-pill {
            background: white; border: 1px solid var(--border); border-radius: 14px;
            padding: 18px 22px; display: flex; align-items: center; gap: 14px;
            box-shadow: 0 1px 4px rgba(0,0,0,.06); transition: all .3s;
        }
        .stat-pill:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,.09); }
        .stat-icon { width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; flex-shrink: 0; }
        .stat-label { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--muted); }
        .stat-val   { font-size: 1.6rem; font-weight: 800; line-height: 1.1; }

        /* ── FILTER TABS ── */
        .filter-tabs { display: flex; gap: 8px; }
        .ftab {
            padding: 7px 18px; border-radius: 8px; font-size: .78rem; font-weight: 700;
            cursor: pointer; border: 1.5px solid var(--border); background: white;
            color: #475569; text-decoration: none; transition: all .2s;
        }
        .ftab:hover { border-color: var(--primary); color: var(--primary); }
        .ftab.active-all       { background: var(--primary); color: white; border-color: var(--primary); }
        .ftab.active-confirmed { background: var(--success); color: white; border-color: var(--success); }
        .ftab.active-rejected  { background: var(--danger);  color: white; border-color: var(--danger); }

        /* ── TABLE CARD ── */
        .table-card {
            background: white; border-radius: 18px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 20px rgba(0,0,0,.06);
            overflow: hidden;
        }
        .table-card-header {
            padding: 20px 24px; border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;
        }

        /* ── TABLE ── */
        .table { margin: 0; }
        .table thead th {
            background: #f8fafc; color: var(--muted);
            font-size: .7rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .8px;
            padding: 14px 20px; border: none; white-space: nowrap;
        }
        .table tbody tr { transition: background .15s; }
        .table tbody tr:hover { background: #fafbff; }
        .table td { padding: 16px 20px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; font-size: .88rem; }
        .table tbody tr:last-child td { border-bottom: none; }

        /* ── BADGES ── */
        .badge-confirmed { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; padding: 5px 12px; border-radius: 20px; font-size: .75rem; font-weight: 700; white-space: nowrap; }
        .badge-rejected  { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; padding: 5px 12px; border-radius: 20px; font-size: .75rem; font-weight: 700; white-space: nowrap; }
        .token-chip { font-family: monospace; background: #f1f5f9; padding: 4px 10px; border-radius: 7px; color: #334155; font-weight: 700; font-size: .85rem; border: 1px solid var(--border); }

        /* ── ACTION BUTTONS ── */
        .btn-del {
            width: 34px; height: 34px; border-radius: 9px;
            background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: .8rem; cursor: pointer; transition: all .2s; text-decoration: none;
        }
        .btn-del:hover { background: #b91c1c; color: white; transform: scale(1.08); box-shadow: 0 4px 12px rgba(185,28,28,.3); }

        /* ── SEARCH ── */
        .search-wrap { position: relative; }
        .search-wrap input { padding-left: 36px; border-radius: 9px; font-size: .82rem; border: 1.5px solid var(--border); outline: none; height: 36px; }
        .search-wrap input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(67,97,238,.1); }
        .search-wrap .si { position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: .8rem; pointer-events: none; }

        /* ── CHECKBOX ── */
        .form-check-input { width: 1.1rem; height: 1.1rem; cursor: pointer; }
        .form-check-input:checked { background-color: var(--primary); border-color: var(--primary); }

        /* ── BULK BAR ── */
        #bulkBar {
            display: none; align-items: center; gap: 10px;
            background: #eff6ff; border: 1px solid #bfdbfe;
            border-radius: 10px; padding: 10px 16px;
            font-size: .82rem; font-weight: 600; color: #1d4ed8;
            animation: fadeInUp .2s ease-out;
        }

        /* ── EMPTY STATE ── */
        .empty-state { padding: 70px 20px; text-align: center; color: var(--muted); }
        .empty-state i { font-size: 2.8rem; margin-bottom: 14px; display: block; opacity: .4; }
        .empty-state p { font-size: .88rem; }

        /* ── DELETE CONFIRM MODAL ── */
        .modal-content { border: none; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,.15); }
        .modal-header { border-bottom: 1px solid #f1f5f9; padding: 20px 24px; }
        .modal-footer { border-top: 1px solid #f1f5f9; padding: 16px 24px; }
    </style>
</head>
<body>

<div class="container-fluid p-0">
<div class="row g-0">

    <!-- SIDEBAR -->
    <div class="col-lg-2 sidebar d-none d-lg-block">
        <span class="brand">VISIT<span>EASE</span></span>
        <nav class="d-flex flex-column">
            <a href="admin.php"           class="nav-link"><i class="fas fa-th-large"></i> Dashboard</a>
            <a href="manage_schedule.php" class="nav-link"><i class="fas fa-calendar-alt"></i> Schedule</a>
            <a href="visitors.php"        class="nav-link"><i class="fas fa-users"></i> Visitors</a>
            <a href="history.php"         class="nav-link active"><i class="fas fa-history"></i> History</a>
            <a href="logout.php"          class="nav-link text-danger mt-5"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>

    <!-- MAIN -->
    <div class="col-lg-10 main-content">

        <!-- HEADER -->
        <div class="anim a1 d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
            <div>
                <h2 class="fw-bold mb-1" style="letter-spacing:-.3px">Booking History</h2>
                <p class="text-secondary mb-0" style="font-size:.88rem">Archives of confirmed and rejected bookings</p>
            </div>
            <div class="filter-tabs">
                <a href="history.php?filter=all"       class="ftab <?php echo $filter === 'all'       ? 'active-all'       : ''; ?>"><i class="fas fa-list me-1"></i>All</a>
                <a href="history.php?filter=confirmed" class="ftab <?php echo $filter === 'confirmed' ? 'active-confirmed' : ''; ?>"><i class="fas fa-check-circle me-1"></i>Confirmed</a>
                <a href="history.php?filter=rejected"  class="ftab <?php echo $filter === 'rejected'  ? 'active-rejected'  : ''; ?>"><i class="fas fa-times-circle me-1"></i>Rejected</a>
            </div>
        </div>

        <!-- STAT PILLS -->
        <div class="row g-3 mb-4">
            <div class="col-sm-4 anim a2">
                <div class="stat-pill">
                    <div class="stat-icon" style="background:#eef2ff; color:var(--primary);"><i class="fas fa-archive"></i></div>
                    <div>
                        <div class="stat-label">Total Records</div>
                        <div class="stat-val"><?php echo $total; ?></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 anim a3">
                <div class="stat-pill">
                    <div class="stat-icon" style="background:#dcfce7; color:#15803d;"><i class="fas fa-check-circle"></i></div>
                    <div>
                        <div class="stat-label">Confirmed</div>
                        <div class="stat-val" style="color:#15803d"><?php echo $confirmed; ?></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 anim a4">
                <div class="stat-pill">
                    <div class="stat-icon" style="background:#fee2e2; color:#b91c1c;"><i class="fas fa-times-circle"></i></div>
                    <div>
                        <div class="stat-label">Rejected</div>
                        <div class="stat-val" style="color:#b91c1c"><?php echo $rejected; ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABLE CARD -->
        <div class="table-card anim a4">
            <div class="table-card-header">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <h5 class="m-0 fw-bold"><i class="fas fa-clock-rotate-left me-2 text-primary"></i>Records</h5>

                    <!-- BULK DELETE BAR -->
                    <form method="POST" action="history.php" id="bulkForm">
                        <div id="bulkBar">
                            <i class="fas fa-check-square"></i>
                            <span id="bulkCount">0</span> selected
                            <button type="submit" name="bulk_delete" value="1"
                                class="btn btn-sm btn-danger fw-bold ms-2"
                                onclick="return confirm('Permanently delete all selected records?')">
                                <i class="fas fa-trash me-1"></i> Delete Selected
                            </button>
                            <button type="button" class="btn btn-sm btn-secondary" onclick="clearSelection()">Cancel</button>
                        </div>
                        <!-- hidden inputs injected by JS -->
                        <div id="hiddenIds"></div>
                    </form>
                </div>

                <!-- SEARCH -->
                <div class="search-wrap">
                    <i class="fas fa-search si"></i>
                    <input type="text" id="searchInput" placeholder="Search name, token, email…" style="width:240px;">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table" id="historyTable">
                    <thead>
                        <tr>
                            <th style="width:40px; text-align:center;">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th>Token</th>
                            <th>Visitor</th>
                            <th>Visit Date</th>
                            <th>Pax</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()):
                                $v_date = $row['visit_date'] ? date('M d, Y', strtotime($row['visit_date'])) : '—';
                                $v_time = $row['start_time'] ? date('h:i A', strtotime($row['start_time'])) : '';
                            ?>
                            <tr data-id="<?php echo $row['id']; ?>">
                                <td style="text-align:center;">
                                    <input type="checkbox" class="form-check-input row-chk" value="<?php echo $row['id']; ?>">
                                </td>
                                <td><span class="token-chip">#<?php echo htmlspecialchars($row['token']); ?></span></td>
                                <td>
                                    <div class="fw-semibold"><?php echo htmlspecialchars($row['name']); ?></div>
                                    <div class="small text-muted"><?php echo htmlspecialchars($row['email']); ?></div>
                                </td>
                                <td>
                                    <div class="fw-semibold"><?php echo $v_date; ?></div>
                                    <?php if ($v_time): ?>
                                        <div class="small" style="color:var(--primary); font-weight:600;"><?php echo $v_time; ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold fs-6"><?php echo $row['guests']; ?></td>
                                <td>
                                    <?php if ($row['status'] === 'Confirmed'): ?>
                                        <span class="badge-confirmed"><i class="fas fa-check-circle me-1"></i>Confirmed</span>
                                    <?php else: ?>
                                        <span class="badge-rejected"><i class="fas fa-times-circle me-1"></i>Rejected</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="#" class="btn-del"
                                        onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['name']); ?>')"
                                        title="Delete record">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="fas fa-box-open"></i>
                                        <p>No history records found<?php echo $filter !== 'all' ? ' for this filter' : ''; ?>.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div><!-- end main-content -->
</div>
</div>

<!-- DELETE CONFIRM MODAL -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;border-radius:10px;background:#fee2e2;display:flex;align-items:center;justify-content:center;color:#b91c1c;font-size:.95rem;">
                        <i class="fas fa-trash"></i>
                    </div>
                    <h5 class="m-0 fw-bold">Delete Record</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <p class="mb-1" style="font-size:.9rem;">Are you sure you want to permanently delete the record of</p>
                <p class="fw-bold mb-0" id="deleteVisitorName" style="font-size:1rem;color:#1e293b;">—</p>
                <p class="text-muted mt-2 mb-0" style="font-size:.8rem;"><i class="fas fa-warning me-1 text-warning"></i>This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger fw-bold">
                    <i class="fas fa-trash me-1"></i>Delete
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ── SINGLE DELETE ──
function confirmDelete(id, name) {
    document.getElementById('deleteVisitorName').textContent = name;
    document.getElementById('confirmDeleteBtn').href = `history.php?action=delete&id=${id}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// ── LIVE SEARCH ──
document.getElementById('searchInput').addEventListener('keyup', function () {
    const filter = this.value.toLowerCase();
    document.querySelectorAll('#historyTable tbody tr').forEach(row => {
        if (row.cells.length < 2) return;
        row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none';
    });
});

// ── CHECKBOX & BULK SELECT ──
const selectAll = document.getElementById('selectAll');
const bulkBar   = document.getElementById('bulkBar');
const bulkCount = document.getElementById('bulkCount');
const hiddenIds = document.getElementById('hiddenIds');

selectAll.addEventListener('change', function () {
    document.querySelectorAll('.row-chk').forEach(chk => {
        if (chk.closest('tr').style.display !== 'none') chk.checked = this.checked;
    });
    updateBulkBar();
});

document.addEventListener('change', function (e) {
    if (e.target.classList.contains('row-chk')) updateBulkBar();
});

function updateBulkBar() {
    const checked = document.querySelectorAll('.row-chk:checked');
    if (checked.length > 0) {
        bulkBar.style.display = 'flex';
        bulkCount.textContent = checked.length;
        // Rebuild hidden inputs
        hiddenIds.innerHTML = '';
        checked.forEach(chk => {
            const inp = document.createElement('input');
            inp.type = 'hidden'; inp.name = 'booking_ids[]'; inp.value = chk.value;
            hiddenIds.appendChild(inp);
        });
    } else {
        bulkBar.style.display = 'none';
        selectAll.checked = false;
    }
}

function clearSelection() {
    document.querySelectorAll('.row-chk, #selectAll').forEach(chk => chk.checked = false);
    bulkBar.style.display = 'none';
}
</script>
</body>
</html>
