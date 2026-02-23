<?php
session_start();
include 'db.php';

// --- FETCH SCHEDULES ---
$schedules = [];
$sql = "SELECT * FROM schedule_settings WHERE slots > 0 AND date >= CURDATE() ORDER BY date ASC, start_time ASC";
$result = $conn->query($sql);

if (!$result) {
    die("Database Error: " . $conn->error);
}

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $schedules[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Visit | VisitEase</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Raleway:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-blue: #2563eb; 
            --primary-blue-hover: #1d4ed8; 
            --bg-dark: #0f172a; 
            --dark-panel: rgba(15, 23, 42, 0.92); 
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        /* --- BACKGROUND ANIMATION CSS --- */
        .animated-bg-container {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; overflow: hidden; z-index: -2; 
        }

        .bg-image-animate {
            width: 100%; height: 100%;
            background-image: url("494813889_1218375400006712_3528886370220596685_n.jpg"); 
            background-size: cover; background-position: center;
            animation: slowZoom 25s linear infinite alternate;
        }

        .bg-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.45); 
            backdrop-filter: blur(8px); 
            -webkit-backdrop-filter: blur(8px);
            z-index: -1; 
        }

        @keyframes slowZoom {
            0% { transform: scale(1); }
            100% { transform: scale(1.15); } 
        }
        /* ------------------------------------ */

        body { font-family: 'Raleway', sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        h1, h2, h3, .brand-title, .modal-title { font-family: 'Playfair Display', serif; }

        .booking-card {
            background: #ffffff; border-radius: 24px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6); 
            border: 1px solid rgba(255, 255, 255, 0.2); overflow: hidden; width: 100%; max-width: 1100px;
            display: flex; min-height: 650px; z-index: 1; 
        }

        .brand-panel {
            flex: 1; background: var(--dark-panel); color: white; padding: 50px;
            display: flex; flex-direction: column; justify-content: space-between; position: relative;
            min-width: 350px; border-right: 1px solid rgba(37, 99, 235, 0.2); 
        }

        .brand-panel::before, .brand-panel::after {
            content: ''; position: absolute; border-radius: 50%; background: rgba(37, 99, 235, 0.08); filter: blur(20px);
        }
        .brand-panel::before { width: 300px; height: 300px; top: -50px; left: -50px; }
        .brand-panel::after { width: 200px; height: 200px; bottom: 50px; right: -50px; }

        .brand-title { font-size: 2.8rem; font-weight: 700; letter-spacing: 1px; margin-bottom: 10px; color: var(--primary-blue); position: relative; z-index: 2;}
        .brand-subtitle { font-size: 1.05rem; opacity: 0.9; font-weight: 300; line-height: 1.6; color: #f1f5f9; position: relative; z-index: 2;}

        .payment-card {
            background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(37, 99, 235, 0.3); 
            border-radius: 16px; padding: 25px; text-align: center; margin-top: 30px; position: relative; z-index: 2; box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .qr-box {
            background: white; padding: 10px; border-radius: 12px; width: 140px; height: 140px; margin: 15px auto;
            display: flex; align-items: center; justify-content: center; border: 2px solid var(--primary-blue);
        }
        .qr-box img { width: 100%; height: 100%; object-fit: contain; }

        .form-panel { flex: 1.4; padding: 50px; overflow-y: auto; max-height: 800px; background: #ffffff; }

        .section-label {
            font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px;
            color: var(--primary-blue); margin-bottom: 15px; display: block;
            border-bottom: 1px dashed rgba(37, 99, 235, 0.3); padding-bottom: 5px;
        }

        .form-label { font-size: 0.85rem; font-weight: 600; color: var(--text-muted); margin-bottom: 6px; }

        .form-control, .form-select {
            padding: 12px 16px; border-radius: 10px; border: 1px solid #cbd5e1; background: #f8fafc;
            font-size: 0.95rem; font-weight: 500; color: var(--text-main); transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            background: #ffffff; border-color: var(--primary-blue); box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15); 
        }

        .security-wrapper {
            background: #f8fafc; border: 1px dashed var(--primary-blue); padding: 15px 20px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center; gap: 20px;
        }

        .btn-book {
            background: var(--primary-blue); color: #ffffff; width: 100%; padding: 16px; border-radius: 10px; font-weight: 700;
            font-size: 1.05rem; border: none; transition: all 0.3s; margin-top: 10px; text-transform: uppercase;
            letter-spacing: 1px; box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3); 
        }

        .btn-book:hover {
            background: var(--primary-blue-hover); transform: translateY(-2px); box-shadow: 0 12px 25px rgba(37, 99, 235, 0.5); color: white;
        }

        .alert-blue { background-color: rgba(37, 99, 235, 0.1); border-left: 4px solid var(--primary-blue); color: #1e3a8a; }

        @media (max-width: 992px) {
            .booking-card { flex-direction: column; min-height: auto; }
            .brand-panel { padding: 40px 30px; text-align: center; border-right: none; border-bottom: 1px solid rgba(37, 99, 235, 0.2); } 
            .payment-card { margin: 20px auto 0; max-width: 400px; }
            .form-panel { padding: 30px; }
            .brand-panel::before, .brand-panel::after { display: none; }
        }
    </style>
</head>
<body>

<div class="animated-bg-container">
    <div class="bg-image-animate"></div>
</div>
<div class="bg-overlay"></div>

<div class="booking-card">
    
    <div class="brand-panel">
        <div>
            <div class="brand-title">VisitEase.</div>
            <p class="brand-subtitle">Experience art and history seamlessly. Secure your museum tour slot instantly.</p>
        </div>

        <div class="payment-card">
            <div class="small fw-bold text-uppercase mb-2" style="letter-spacing: 1px; color: #93c5fd;">Payment Method</div>
            <h3 class="fw-bold mb-0 text-white" style="font-family: 'Raleway', sans-serif;">GCash</h3>
            <div class="d-flex align-items-center justify-content-center gap-2 mt-1">
                <span class="fw-medium text-white">0917-123-4567</span>
                <span class="badge rounded-pill" style="background-color: var(--primary-blue); color: #fff;">Admin</span>
            </div>
            
            <div class="qr-box">
                <img src="gcash_qr.png" onerror="this.src='https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=09171234567'" alt="GCash QR">
            </div>
            
            <div class="small mt-2" style="opacity: 0.8; font-size: 0.8rem; color: #e2e8f0;">
                <i class="fas fa-info-circle me-1" style="color: #93c5fd;"></i> Save your Ref No. for verification.
            </div>
        </div>
        
        <div class="mt-4 text-center d-none d-lg-block">
            <small style="opacity: 0.5;">&copy; 2026 VisitEase System</small>
        </div>
    </div>

    <div class="form-panel">
        <h2 class="fw-bold mb-1" style="color: var(--text-main);">Book a Slot</h2>
        <p class="text-muted mb-4">Please fill in your details correctly.</p>

        <?php if (count($schedules) == 0): ?>
            <div class="alert alert-blue" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Walang available slots sa ngayon.</strong> Please contact admin or check back later.
            </div>
        <?php endif; ?>

        <form action="process_booking.php" method="POST" id="bookingForm" enctype="multipart/form-data">
            
            <span class="section-label">Visitor Information</span>
            
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" placeholder="Juan Dela Cruz" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="name@email.com">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-control" placeholder="0912 345 6789" required>
                </div>
            </div>

            <hr class="my-4" style="opacity: 0.1;">

            <span class="section-label">Schedule & Guests</span>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Guests</label>
                    <input type="number" name="guests" class="form-control" value="1" min="1" max="10" required>
                </div>
                <div class="col-md-8 mb-3">
                    <label class="form-label">Select Slot <span class="text-danger">*</span></label>
                    <select name="slot_id" id="slotSelect" class="form-select" required>
                        <option value="">-- Choose Date & Time --</option>
                        <?php foreach($schedules as $sched): ?>
                            <?php 
                                $sched_date = date('M d, Y', strtotime($sched['date']));
                                $start_time = date('h:i A', strtotime($sched['start_time']));
                                $end_time = date('h:i A', strtotime($sched['end_time']));
                                $remaining = $sched['slots'];
                            ?>
                            <option value="<?php echo $sched['id']; ?>">
                                <?php echo "$sched_date - $start_time to $end_time ($remaining slots available)"; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted mt-1 d-block">Available slots: <?php echo count($schedules); ?></small>
                </div>
            </div>

            <hr class="my-4" style="opacity: 0.1;">

            <span class="section-label">Payment Proof</span>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">GCash Sender Name</label>
                    <input type="text" name="gcash_name" class="form-control" placeholder="Sender Name" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Reference No.</label>
                    <input type="text" name="gcash_ref" class="form-control" placeholder="Last 6-8 Digits" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Upload GCash Receipt <span class="text-danger">*</span></label>
                <input type="file" name="gcash_receipt" class="form-control" accept="image/*" required>
                <small class="text-muted d-block mt-1">Please upload a clear screenshot of your GCash payment.</small>
            </div>

            <div class="mb-4">
                <label class="form-label">Special Request (Optional)</label>
                <textarea name="special_request" class="form-control" rows="2" placeholder="PWD assistance, etc."></textarea>
            </div>

            <?php 
                $n1 = rand(1, 9);
                $n2 = rand(1, 9);
                $_SESSION['captcha_answer'] = $n1 + $n2; 
            ?>
            <div class="security-wrapper mb-4">
                <div>
                    <div class="fw-bold" style="color: var(--primary-blue);">Security Check</div>
                    <small class="text-muted">Solve this: <b class="text-dark"><?php echo $n1; ?> + <?php echo $n2; ?> = ?</b></small>
                </div>
                <input type="number" name="captcha_input" class="form-control" style="width: 100px; text-align: center; font-weight: bold; border-color: var(--primary-blue);" required placeholder="0">
            </div>

            <button type="button" class="btn-book" onclick="validateAndShowReview()">
                Review Details <i class="fas fa-arrow-right ms-2"></i>
            </button>

            <div class="text-center mt-4">
                <a href="index.php" class="text-decoration-none fw-bold small text-muted hover-dark"><i class="fas fa-arrow-left me-1"></i> Back to Home</a>
            </div>

        </form>
    </div>
</div>

<div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
      <div class="modal-header text-white" style="background: var(--bg-dark); border-bottom: 3px solid var(--primary-blue);">
        <h5 class="modal-title fw-bold"><i class="fas fa-clipboard-check me-2" style="color: var(--primary-blue);"></i>Confirm Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4 bg-light">
        <p class="text-center text-muted small mb-3">Please verify your information before submitting.</p>
        
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0">
                        <span class="small text-muted fw-bold text-uppercase">Visitor</span>
                        <span id="rev_name" class="fw-bold text-dark text-end"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0">
                        <span class="small text-muted fw-bold text-uppercase">Contact</span>
                        <div class="text-end">
                            <div id="rev_phone" class="fw-bold text-dark" style="font-size: 0.9rem;"></div>
                            <div id="rev_email" class="small text-muted"></div>
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0">
                        <span class="small text-muted fw-bold text-uppercase">Slot</span>
                        <span id="rev_slot" class="fw-bold text-end" style="color: var(--primary-blue);"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0">
                        <span class="small text-muted fw-bold text-uppercase">Guests</span>
                        <span id="rev_guests" class="fw-bold text-dark"></span>
                    </li>
                    <li class="list-group-item bg-transparent px-0">
                         <span class="small text-muted fw-bold text-uppercase">GCash Payment</span>
                         <div class="d-flex justify-content-between mt-1">
                             <span id="rev_gcash_name" class="fw-bold"></span>
                             <span id="rev_gcash_ref" class="badge" style="background-color: var(--bg-dark);"></span>
                         </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0">
                        <span class="small text-muted fw-bold text-uppercase">Receipt Attached</span>
                        <span id="rev_receipt" class="fw-bold text-success"><i class="fas fa-check-circle"></i> Yes</span>
                    </li>
                    <li class="list-group-item bg-transparent px-0 pb-0 border-0" id="rev_req_container">
                        <span class="small text-muted fw-bold text-uppercase">Note</span>
                        <p id="rev_request" class="small text-dark fst-italic mt-1 mb-0"></p>
                    </li>
                </ul>
            </div>
        </div>

      </div>
      <div class="modal-footer border-0 bg-light">
        <button type="button" class="btn btn-outline-secondary px-4 fw-bold" data-bs-dismiss="modal" style="border-radius: 8px;">Edit</button>
        <button type="button" class="btn px-4 fw-bold text-white" style="background-color: var(--primary-blue); border-radius: 8px;" onclick="submitRealForm()">
            Confirm & Submit <i class="fas fa-check-circle ms-1"></i>
        </button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function validateAndShowReview() {
    var form = document.getElementById('bookingForm');
    
    // Validate Form
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    // Check if slot is selected
    var slotSelect = document.getElementById('slotSelect');
    if (!slotSelect.value || slotSelect.value === '') {
        alert('Please select a schedule slot!');
        slotSelect.focus();
        return;
    }

    // Check if receipt is uploaded
    var receiptInput = document.querySelector('input[name="gcash_receipt"]');
    if (receiptInput.files.length === 0) {
        alert('Please upload your GCash receipt!');
        return;
    }

    // Get Values
    var name = document.querySelector('input[name="name"]').value;
    var email = document.querySelector('input[name="email"]').value;
    var phone = document.querySelector('input[name="phone"]').value;
    var guests = document.querySelector('input[name="guests"]').value;
    var slotText = slotSelect.options[slotSelect.selectedIndex].text;
    var gcashName = document.querySelector('input[name="gcash_name"]').value;
    var gcashRef = document.querySelector('input[name="gcash_ref"]').value;
    var request = document.querySelector('textarea[name="special_request"]').value;

    // Populate Modal
    document.getElementById('rev_name').innerText = name;
    document.getElementById('rev_email').innerText = email;
    document.getElementById('rev_phone').innerText = phone;
    document.getElementById('rev_guests').innerText = guests + " Pax";
    document.getElementById('rev_slot').innerText = slotText;
    document.getElementById('rev_gcash_name').innerText = gcashName;
    document.getElementById('rev_gcash_ref').innerText = "Ref: " + gcashRef;
    document.getElementById('rev_request').innerText = request.trim() !== "" ? request : "None";

    // Show Modal
    var myModal = new bootstrap.Modal(document.getElementById('reviewModal'));
    myModal.show();
}

function submitRealForm() {
    document.getElementById('bookingForm').submit();
}
</script>

</body>
</html>