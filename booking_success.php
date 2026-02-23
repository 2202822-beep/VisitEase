<?php
// Kunin ang token mula sa URL
$token_display = isset($_GET['ref']) ? htmlspecialchars($_GET['ref']) : "NO-TOKEN";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Received | VisitEase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f1f5f9;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .success-card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            text-align: center;
            max-width: 450px;
            width: 100%;
            border-top: 5px solid #f59e0b; /* Gawing Orange/Yellow dahil Pending pa */
        }
        .icon-circle {
            width: 80px;
            height: 80px;
            background-color: #fef3c7; /* Yellow background */
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
        }
        .check-icon {
            font-size: 40px;
            color: #d97706; /* Dark Yellow/Orange icon */
        }
        .token-box {
            background-color: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
        }
        .token-label {
            font-size: 12px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .ref-code {
            font-size: 28px;
            font-weight: 800;
            color: #4f46e5;
            margin-top: 5px;
            letter-spacing: 2px;
        }
        .status-badge {
            background-color: #fffbeb;
            color: #b45309;
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 700;
            display: inline-block;
            margin-bottom: 20px;
            border: 1px solid #fcd34d;
        }
        .btn-home {
            background-color: #0f172a;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 500;
            width: 100%;
            transition: all 0.3s;
        }
        .btn-home:hover {
            background-color: #1e293b;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

    <div class="success-card">
        <div class="icon-circle">
            <i class="fas fa-clock check-icon"></i>
        </div>
        
        <h2 class="fw-bold text-dark mb-2">Request Submitted!</h2>
        
        <div class="status-badge">STATUS: PENDING APPROVAL</div>

        <p class="text-muted mb-4" style="font-size: 0.95rem;">
            Natanggap na namin ang iyong request. Hinihintay na lang ang confirmation mula sa Admin.
        </p>

        <div class="token-box">
            <div class="token-label">Booking Reference Token</div>
            <div class="ref-code"><?php echo $token_display; ?></div>
            <small class="text-danger d-block mt-2" style="font-size: 0.75rem;">
                *I-save o i-screenshot ang Token na ito.*
            </small>
        </div>

        <p class="small text-muted mb-4">
            Ipapakita mo ang Token na ito sa entrance kapag na-approve na ang iyong schedule.
        </p>

        <a href="index.php" class="btn btn-home text-decoration-none">
            Back to Home
        </a>
    </div>

</body>
</html>