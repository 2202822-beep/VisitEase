<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | VisitEase - Pedro S. Tolentino Museum</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=DM+Sans:opsz,wght@9..40,400;9..40,500&family=Playfair+Display:ital,wght@0,600;1,600&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-cream: #f7f2ea;
            --dark-brown: #1e1a14;
            --gold: #b8842a;
            
            --font-display: 'Cormorant Garamond', serif;
            --font-body: 'DM Sans', sans-serif;
            --font-accent: 'Playfair Display', serif;
        }

        body {
            background-color: var(--dark-brown);
            color: var(--bg-cream);
            font-family: var(--font-body);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            /* Museum background */
            background: linear-gradient(rgba(30, 26, 20, 0.75), rgba(30, 26, 20, 0.85)), 
                        url('https://images.unsplash.com/photo-1541123356219-284ebe98ae3b?q=80&w=2070&auto=format&fit=crop') no-repeat center center;
            background-size: cover;
            background-attachment: fixed;
            overflow: hidden;
            margin: 0;
            text-align: center;
        }

        /* ── HEADER ── */
        .brand-header {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            padding: 30px 40px;
            text-align: left;
            z-index: 10;
        }

        .visitease-logo {
            font-family: var(--font-display);
            font-size: 1.8rem;
            color: var(--bg-cream);
            letter-spacing: 2px;
            font-weight: 600;
            text-decoration: none;
        }

        .visitease-logo span {
            color: var(--gold);
        }

        /* ── MAIN CONTENT ── */
        .welcome-title {
            font-family: var(--font-display);
            font-size: 4.5rem;
            font-weight: 600;
            letter-spacing: 2px;
            margin-bottom: 10px;
            text-transform: capitalize;
        }

        .museum-name {
            font-family: var(--font-accent);
            font-style: italic;
            color: var(--gold);
            font-size: 1.5rem;
            letter-spacing: 2px;
        }

        .divider-gold {
            height: 1px;
            background-color: var(--gold);
            width: 80px;
            margin: 30px auto;
        }

        .welcome-subtitle {
            font-size: 1.1rem;
            line-height: 1.6;
            max-width: 600px;
            margin: 0 auto 50px auto;
            opacity: 0.9;
            font-weight: 300;
        }

        /* ── BUTTON ── */
        .btn-custom {
            border-radius: 3px;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.9rem;
            font-weight: 500;
            padding: 16px 40px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-outline-gold {
            background-color: transparent;
            color: var(--bg-cream);
            border: 1px solid var(--gold);
        }

        .btn-outline-gold:hover {
            background-color: var(--gold);
            color: #fff;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }

        /* Animation */
        .fade-in {
            animation: fadeIn 1.5s ease-out forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Mobile Adjustments */
        @media (max-width: 768px) {
            .welcome-title { font-size: 3rem; }
            .brand-header { padding: 20px; text-align: center; }
            .welcome-subtitle { font-size: 1rem; padding: 0 20px; }
        }
    </style>
</head>
<body>

    <header class="brand-header">
        <div class="visitease-logo">Visit<span>Ease</span></div>
    </header>

    <div class="container fade-in">
        <p class="museum-name mb-2">Pedro S. Tolentino Museum</p>
        
        <h1 class="welcome-title">Welcome to our museum</h1>
        
        <div class="divider-gold"></div>
        
        <p class="welcome-subtitle">
            Plan your visit with ease. Book your preferred time slot and experience our amazing collection.
        </p>

        <a href="index.php" class="btn btn-custom btn-outline-gold">Enter the Museum</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>