<?php
session_start();
include 'db.php';

// 1. Kunin ang Token
$token = "";
if (isset($_GET['token'])) {
    $token = trim($_GET['token']);
} elseif (isset($_SESSION['latest_token'])) {
    $token = $_SESSION['latest_token'];
}

// 2. Hanapin sa Database
$booking = null;
$schedule = null;
$error_msg = "";

if (!empty($token)) {
    $stmt = $conn->prepare("
        SELECT b.*, s.date as visit_date, s.start_time, s.end_time
        FROM bookings b
        LEFT JOIN schedule_settings s ON b.schedule_id = s.id
        WHERE b.token = ?
    ");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $booking = $result->fetch_assoc();
    } else {
        $error_msg = "Booking token not found. Please check and try again.";
    }
}

// Logic para sa Cancel
if (isset($_POST['cancel_booking']) && !empty($token)) {
    $stmt = $conn->prepare("UPDATE bookings SET status = 'Cancelled' WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    header("Location: " . $_SERVER['PHP_SELF'] . "?token=" . $token);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Booking | VisitEase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=DM+Sans:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --cream:  #f7f2ea;
            --cream2: #efe8d8;
            --white:  #ffffff;
            --dark:   #231e14;
            --muted:  #6b6050;
            --gold:   #b8842a;
            --brown:  #7c5c2e;
            --terra:  #c4704a;
            --black:  #1e1a14;
            --border: rgba(184,132,42,0.2);
            --shadow: rgba(124,92,46,0.12);
            --fd: 'Playfair Display', serif;
            --fh: 'Cormorant Garamond', serif;
            --fb: 'DM Sans', sans-serif;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }

        body {
            font-family: var(--fb);
            background:
                linear-gradient(rgba(30,26,20,0.82), rgba(30,26,20,0.82)),
                url('https://images.unsplash.com/photo-1566127444979-b3d2b654e3d7?auto=format&fit=crop&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 16px;
        }

        /* ── TOP BRAND BAR ── */
        .brand-bar {
            text-align: center;
            margin-bottom: 28px;
        }
        .brand-bar .eyebrow {
            display: inline-flex; align-items: center; gap: 10px;
            font-family: var(--fb); font-size: .65rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: 4px; color: var(--gold);
            margin-bottom: 6px;
        }
        .brand-bar .eyebrow::before,
        .brand-bar .eyebrow::after { content:''; width: 22px; height: 1px; background: var(--gold); }
        .brand-bar h1 {
            font-family: var(--fh); font-size: 2rem; font-weight: 700;
            color: #f2ece0; letter-spacing: 4px;
        }

        /* ── MAIN CARD ── */
        .main-card {
            background: var(--cream);
            border-radius: 6px;
            box-shadow: 0 24px 60px rgba(0,0,0,0.35);
            overflow: hidden;
            width: 100%;
            max-width: 720px;
            border: 1px solid rgba(184,132,42,0.15);
            animation: slideUp .5s ease-out;
        }
        @keyframes slideUp {
            from { transform: translateY(24px); opacity: 0; }
            to   { transform: translateY(0);    opacity: 1; }
        }

        /* ── SEARCH STATE ── */
        .search-container {
            padding: 50px 44px;
            text-align: center;
            background: var(--cream);
        }
        .search-icon-wrap {
            width: 64px; height: 64px; border-radius: 50%;
            background: var(--cream2); border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px; font-size: 1.6rem; color: var(--gold);
        }
        .search-container h2 {
            font-family: var(--fd); font-size: 1.8rem;
            color: var(--dark); margin-bottom: 6px;
        }
        .search-container p { color: var(--muted); font-size: .88rem; margin-bottom: 28px; }

        .token-input {
            border: 1.5px solid var(--border);
            border-radius: 3px;
            padding: 14px 22px;
            font-family: var(--fb); font-size: 1rem;
            background: var(--white); color: var(--dark);
            text-align: center; letter-spacing: 2px;
            width: 100%;
            transition: all .3s;
        }
        .token-input:focus {
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(184,132,42,.12);
        }
        .token-input::placeholder { color: #b8a898; letter-spacing: 1px; font-size: .9rem; }

        .btn-search {
            width: 100%; margin-top: 14px;
            background: var(--brown); color: #fff;
            border: 1px solid var(--brown); border-radius: 3px;
            padding: 13px; font-family: var(--fb);
            font-size: .78rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: 2.5px;
            cursor: pointer; transition: all .3s;
        }
        .btn-search:hover { background: var(--dark); border-color: var(--dark); }

        .back-link {
            display: inline-flex; align-items: center; gap: 6px;
            margin-top: 20px; color: rgba(184,132,42,.8);
            font-size: .78rem; text-decoration: none;
            text-transform: uppercase; letter-spacing: 1.5px;
            transition: color .2s;
        }
        .back-link:hover { color: var(--gold); }

        /* ── TICKET HEADER ── */
        .ticket-header {
            background: var(--black);
            padding: 32px 36px 42px;
            position: relative; overflow: hidden; text-align: center;
        }
        .ticket-header::before {
            content: ''; position: absolute; inset: 0;
            background: repeating-linear-gradient(72deg, transparent 0, transparent 60px, rgba(184,132,42,.04) 60px, rgba(184,132,42,.04) 61px);
        }
        .ticket-header::after {
            content: ''; position: absolute; bottom: -1px; left: 0; right: 0; height: 22px;
            background: var(--cream);
            clip-path: polygon(0% 100%, 2.5% 0%, 5% 100%, 7.5% 0%, 10% 100%, 12.5% 0%, 15% 100%, 17.5% 0%, 20% 100%, 22.5% 0%, 25% 100%, 27.5% 0%, 30% 100%, 32.5% 0%, 35% 100%, 37.5% 0%, 40% 100%, 42.5% 0%, 45% 100%, 47.5% 0%, 50% 100%, 52.5% 0%, 55% 100%, 57.5% 0%, 60% 100%, 62.5% 0%, 65% 100%, 67.5% 0%, 70% 100%, 72.5% 0%, 75% 100%, 77.5% 0%, 80% 100%, 82.5% 0%, 85% 100%, 87.5% 0%, 90% 100%, 92.5% 0%, 95% 100%, 97.5% 0%, 100% 100%);
        }
        .th-inner { position: relative; z-index: 2; }
        .th-eyebrow {
            display: inline-flex; align-items: center; gap: 8px;
            font-size: .62rem; font-weight: 600; text-transform: uppercase;
            letter-spacing: 3px; color: var(--gold); margin-bottom: 8px;
        }
        .th-eyebrow::before, .th-eyebrow::after { content:''; width:18px; height:1px; background:var(--gold); }
        .th-token {
            font-family: var(--fh); font-size: 2.4rem; font-weight: 700;
            color: #f2ece0; letter-spacing: 3px; line-height: 1.1; margin-bottom: 14px;
        }

        /* Status badges */
        .status-pill {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 7px 18px; border-radius: 50px;
            font-size: .75rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1.5px;
        }
        .status-pill .dot { width: 7px; height: 7px; border-radius: 50%; }
        .s-pending  { background: #fff7ed; color: #c2410c; border:1px solid #ffedd5; }
        .s-pending .dot  { background: #f97316; }
        .s-confirmed{ background: #f0fdf4; color: #15803d; border:1px solid #dcfce7; }
        .s-confirmed .dot{ background: #22c55e; animation: blink 1.4s ease-in-out infinite; }
        .s-rejected { background: #fef2f2; color: #b91c1c; border:1px solid #fee2e2; }
        .s-rejected .dot { background: #ef4444; }
        .s-cancelled{ background: #f8fafc; color: #475569; border:1px solid #e2e8f0; }
        .s-cancelled .dot{ background: #94a3b8; }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.4} }

        /* ── BODY CONTENT ── */
        .card-body-inner { padding: 36px 36px 32px; }

        .section-label {
            display: flex; align-items: center; gap: 8px;
            font-size: .62rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: 2.5px; color: var(--terra); margin-bottom: 16px;
        }
        .section-label::after { content:''; flex:1; height:1px; background:var(--border); }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 24px; }
        @media(max-width:576px){ .info-grid { grid-template-columns: 1fr; } }

        .info-item .lbl {
            font-size: .65rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: 1.5px; color: var(--muted); margin-bottom: 4px;
            display: flex; align-items: center; gap: 5px;
        }
        .info-item .lbl i { color: var(--gold); font-size: .6rem; }
        .info-item .val {
            font-size: .92rem; font-weight: 600; color: var(--dark);
        }
        .info-item .val.mono { font-family: monospace; letter-spacing: 1px; }

        /* Visit date highlight box */
        .visit-box {
            background: var(--black); border-radius: 4px;
            padding: 18px 22px; display: flex; align-items: center; gap: 18px;
            margin-bottom: 22px;
        }
        .visit-box .vb-icon {
            width: 46px; height: 46px; border-radius: 4px;
            background: rgba(184,132,42,.15); border: 1px solid rgba(184,132,42,.3);
            display: flex; align-items: center; justify-content: center;
            color: var(--gold); font-size: 1.2rem; flex-shrink: 0;
        }
        .visit-box .vb-label { font-size: .62rem; text-transform: uppercase; letter-spacing: 2px; color: rgba(242,236,224,.45); margin-bottom: 3px; }
        .visit-box .vb-date  { font-family: var(--fd); font-size: 1.15rem; color: #f2ece0; font-weight: 700; }
        .visit-box .vb-time  { font-size: .78rem; color: var(--gold); font-weight: 600; margin-top: 2px; }
        .visit-box .vb-divider { width: 1px; height: 40px; background: rgba(255,255,255,.08); flex-shrink:0; }

        /* GCash box */
        .gcash-box {
            background: var(--white); border-radius: 4px;
            border: 1px solid var(--border); padding: 18px 20px;
            border-left: 3px solid #007dfe;
        }
        .gcash-title {
            font-size: .65rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: 2px; color: #0066d6; margin-bottom: 12px;
            display: flex; align-items: center; gap: 6px;
        }
        .gcash-row { display: flex; gap: 24px; flex-wrap: wrap; }
        .gcash-item .g-lbl { font-size: .62rem; text-transform: uppercase; letter-spacing: 1px; color: var(--muted); margin-bottom: 3px; }
        .gcash-item .g-val { font-size: .9rem; font-weight: 700; color: var(--dark); }

        /* Status info cards */
        .status-info {
            background: var(--cream2); border-radius: 4px; border: 1px solid var(--border);
            padding: 16px 20px; display: flex; align-items: flex-start; gap: 14px;
            margin-bottom: 22px;
        }
        .status-info .si-icon { font-size: 1.1rem; flex-shrink:0; margin-top:2px; }
        .status-info .si-title { font-size: .82rem; font-weight: 700; color: var(--dark); margin-bottom: 3px; }
        .status-info .si-text  { font-size: .8rem; color: var(--muted); line-height: 1.6; }

        /* Dashed divider */
        .dash-line { border-top: 1.5px dashed var(--border); margin: 24px 0; }

        /* Action buttons */
        .btn-outline-museum {
            border: 1.5px solid var(--border); color: var(--muted);
            background: transparent; border-radius: 3px;
            padding: 10px 24px; font-size: .76rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: 1.5px;
            text-decoration: none; display: inline-flex; align-items: center; gap: 7px;
            transition: all .25s;
        }
        .btn-outline-museum:hover { background: var(--cream2); color: var(--dark); border-color: var(--brown); }

        .btn-cancel-museum {
            border: 1.5px solid #fca5a5; color: #b91c1c;
            background: #fef2f2; border-radius: 3px;
            padding: 10px 24px; font-size: .76rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: 1.5px;
            cursor: pointer; display: inline-flex; align-items: center; gap: 7px;
            transition: all .25s;
        }
        .btn-cancel-museum:hover { background: #fee2e2; border-color: #f87171; }

        .btn-home-museum {
            border: 1.5px solid var(--brown); color: #fff;
            background: var(--brown); border-radius: 3px;
            padding: 10px 24px; font-size: .76rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: 1.5px;
            text-decoration: none; display: inline-flex; align-items: center; gap: 7px;
            transition: all .25s;
        }
        .btn-home-museum:hover { background: var(--dark); border-color: var(--dark); color: #fff; }

        .error-card {
            background: #fef2f2; border: 1px solid #fecaca;
            border-radius: 4px; padding: 14px 18px;
            color: #b91c1c; font-size: .84rem;
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<!-- BRAND -->
<div class="brand-bar">
    <div class="eyebrow">Pedro S. Tolentino Museum</div>
    <h1>VISITEASE</h1>
</div>

<div class="main-card">

    <?php if (!$booking): ?>
    <!-- ══ SEARCH STATE ══ -->
    <div class="search-container">
        <div class="search-icon-wrap">
            <i class="fas fa-ticket-alt"></i>
        </div>
        <h2>Track Your Booking</h2>
        <p>Enter your reference token to check your booking status and details.</p>

        <?php if ($error_msg): ?>
        <div class="error-card">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo $error_msg; ?>
        </div>
        <?php endif; ?>

        <form action="" method="GET">
            <input type="text" name="token" class="token-input"
                   placeholder="e.g., VST-12345ABC"
                   value="<?php echo htmlspecialchars($token); ?>"
                   required autocomplete="off">
            <button type="submit" class="btn-search">
                <i class="fas fa-search me-2"></i>Check Booking Status
            </button>
        </form>

        <div class="mt-3" style="border-top:1px solid var(--border);padding-top:18px">
            <p style="color:var(--muted);font-size:.78rem;margin-bottom:6px">
                <i class="fas fa-info-circle" style="color:var(--gold)"></i>
                Your token was sent to your email after booking.
            </p>
        </div>

        <a href="index.php" class="back-link">
            <i class="fas fa-arrow-left fa-xs"></i> Back to Homepage
        </a>
    </div>

    <?php else:
        $status = $booking['status'];
        $sc = 's-pending';
        $status_icon = 'fas fa-clock';
        $status_title = 'Booking Under Review';
        $status_msg   = 'Your booking is currently being reviewed by our team. You will receive an email confirmation once it is approved. Please check back later.';
        if ($status == 'Confirmed') {
            $sc = 's-confirmed';
            $status_icon  = 'fas fa-check-circle';
            $status_title = 'Booking Confirmed!';
            $status_msg   = 'Your visit has been confirmed. Please show your reference token at the museum entrance on your scheduled visit date.';
        } elseif ($status == 'Rejected') {
            $sc = 's-rejected';
            $status_icon  = 'fas fa-times-circle';
            $status_title = 'Booking Not Approved';
            $status_msg   = 'Unfortunately, your booking was not approved. This may be due to an invalid GCash reference or full capacity. Please book again with correct details.';
        } elseif ($status == 'Cancelled') {
            $sc = 's-cancelled';
            $status_icon  = 'fas fa-ban';
            $status_title = 'Booking Cancelled';
            $status_msg   = 'This booking has been cancelled. If you still wish to visit, please create a new booking.';
        }

        $v_date = !empty($booking['visit_date']) ? date('l, F d, Y', strtotime($booking['visit_date'])) : 'Not assigned';
        $v_start = !empty($booking['start_time']) ? date('h:i A', strtotime($booking['start_time'])) : '';
        $v_end   = !empty($booking['end_time'])   ? date('h:i A', strtotime($booking['end_time']))   : '';
        $v_time_str = $v_start ? ($v_end ? "$v_start – $v_end" : $v_start) : 'Not assigned';
    ?>

    <!-- ══ TICKET HEADER ══ -->
    <div class="ticket-header">
        <div class="th-inner">
            <div class="th-eyebrow">Booking Reference</div>
            <div class="th-token"><?php echo htmlspecialchars($booking['token']); ?></div>
            <span class="status-pill <?php echo $sc; ?>">
                <span class="dot"></span>
                <?php echo strtoupper($status); ?>
            </span>
        </div>
    </div>

    <!-- ══ CARD BODY ══ -->
    <div class="card-body-inner">

        <!-- STATUS INFO BANNER -->
        <div class="status-info">
            <i class="<?php echo $status_icon; ?> si-icon" style="color:<?php
                echo $status=='Confirmed' ? '#22c55e' :
                    ($status=='Rejected'  ? '#ef4444' :
                    ($status=='Cancelled' ? '#94a3b8' : '#f97316')); ?>"></i>
            <div>
                <div class="si-title"><?php echo $status_title; ?></div>
                <div class="si-text"><?php echo $status_msg; ?></div>
            </div>
        </div>

        <!-- VISIT SCHEDULE BOX -->
        <?php if ($v_date !== 'Not assigned'): ?>
        <div class="visit-box">
            <div class="vb-icon"><i class="fas fa-calendar-check"></i></div>
            <div>
                <div class="vb-label">Scheduled Visit</div>
                <div class="vb-date"><?php echo $v_date; ?></div>
                <?php if ($v_time_str !== 'Not assigned'): ?>
                    <div class="vb-time"><i class="fas fa-clock fa-xs me-1"></i><?php echo $v_time_str; ?></div>
                <?php endif; ?>
            </div>
            <div class="vb-divider"></div>
            <div>
                <div class="vb-label">Group Size</div>
                <div class="vb-date"><?php echo $booking['guests']; ?></div>
                <div class="vb-time">Pax</div>
            </div>
        </div>
        <?php endif; ?>

        <!-- VISITOR INFO -->
        <div class="section-label"><i class="fas fa-user fa-xs"></i>Visitor Information</div>
        <div class="info-grid">
            <div class="info-item">
                <div class="lbl"><i class="fas fa-user"></i>Full Name</div>
                <div class="val"><?php echo htmlspecialchars($booking['name']); ?></div>
            </div>
            <div class="info-item">
                <div class="lbl"><i class="fas fa-envelope"></i>Email</div>
                <div class="val" style="font-size:.83rem"><?php echo htmlspecialchars($booking['email']); ?></div>
            </div>
            <div class="info-item">
                <div class="lbl"><i class="fas fa-phone"></i>Contact Number</div>
                <div class="val"><?php echo htmlspecialchars($booking['phone']); ?></div>
            </div>
            <div class="info-item">
                <div class="lbl"><i class="fas fa-users"></i>Number of Guests</div>
                <div class="val"><?php echo htmlspecialchars($booking['guests']); ?> Pax</div>
            </div>
        </div>

        <?php if (!empty($booking['special_request']) && strtolower(trim($booking['special_request'])) !== 'wala'): ?>
        <div class="info-item mb-4">
            <div class="lbl"><i class="fas fa-comment-dots"></i>Special Request</div>
            <div class="val" style="font-style:italic;color:var(--muted);font-size:.87rem;font-weight:400">
                "<?php echo htmlspecialchars($booking['special_request']); ?>"
            </div>
        </div>
        <?php endif; ?>

        <div class="dash-line"></div>

        <!-- PAYMENT DETAILS -->
        <div class="section-label"><i class="fas fa-wallet fa-xs"></i>Payment Details</div>
        <div class="gcash-box mb-4">
            <div class="gcash-title"><i class="fas fa-mobile-alt"></i>GCash Payment</div>
            <div class="gcash-row">
                <div class="gcash-item">
                    <div class="g-lbl">Sender Name</div>
                    <div class="g-val"><?php echo htmlspecialchars($booking['gcash_name']); ?></div>
                </div>
                <div class="gcash-item">
                    <div class="g-lbl">Reference No.</div>
                    <div class="g-val mono"><?php echo htmlspecialchars($booking['gcash_ref']); ?></div>
                </div>
            </div>
        </div>

        <div class="dash-line"></div>

        <!-- ACTIONS -->
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <a href="check_status.php" class="btn-outline-museum">
                <i class="fas fa-search fa-xs"></i> Check Another
            </a>
            <?php if ($status == 'Pending'): ?>
                <button type="button" class="btn-cancel-museum" data-bs-toggle="modal" data-bs-target="#cancelModal">
                    <i class="fas fa-times fa-xs"></i> Cancel Booking
                </button>
            <?php else: ?>
                <a href="index.php" class="btn-home-museum">
                    Back to Home <i class="fas fa-home fa-xs"></i>
                </a>
            <?php endif; ?>
        </div>

    </div><!-- end card-body-inner -->

    <?php endif; ?>
</div><!-- end main-card -->

<!-- CANCEL MODAL -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:6px;overflow:hidden;">
            <div class="modal-header" style="background:var(--black);border:none;padding:20px 24px;">
                <h5 class="fw-bold m-0" style="color:#f2ece0;font-family:var(--fd)">Cancel Booking?</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4" style="background:var(--cream);">
                <div style="width:60px;height:60px;border-radius:50%;background:#fee2e2;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:1.4rem;color:#ef4444;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <p style="color:var(--dark);font-size:.9rem;line-height:1.6">
                    Are you sure you want to cancel this reservation?<br>
                    <strong>This action cannot be undone.</strong>
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2" style="background:var(--cream);padding-bottom:24px;">
                <button type="button" class="btn-outline-museum" data-bs-dismiss="modal">
                    <i class="fas fa-arrow-left fa-xs"></i> Keep Booking
                </button>
                <form method="POST">
                    <button type="submit" name="cancel_booking" class="btn-cancel-museum">
                        <i class="fas fa-times fa-xs"></i> Yes, Cancel It
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>