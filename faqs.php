<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VisitEase | FAQs</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;0,700;1,300;1,400;1,600&family=DM+Sans:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        :root {
            --cream:  #f7f2ea;
            --cream2: #efe8d8;
            --cream3: #e8dfc8;
            --white:  #ffffff;
            --dark:   #231e14;
            --muted:  #6b6050;
            --light:  #a39078;
            --gold:   #b8842a;
            --gold2:  #d4a84b;
            --brown:  #7c5c2e;
            --terra:  #c4704a;
            --black:  #1e1a14;
            --border: rgba(184,132,42,0.18);
            --border2:rgba(184,132,42,0.32);
            --shadow: rgba(124,92,46,0.10);
            --fd: 'Playfair Display', serif;
            --fh: 'Cormorant Garamond', serif;
            --fb: 'DM Sans', sans-serif;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: var(--fb);
            color: var(--dark);
            background-color: var(--cream);
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed; inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='4' height='4'%3E%3Crect width='4' height='4' fill='%23f7f2ea'/%3E%3Ccircle cx='1' cy='1' r='0.4' fill='%23d4a84b' opacity='0.07'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
        }

        h1,h2,h3,h4,h5 { font-family: var(--fd); color: var(--dark); font-weight: 700; }

        /* ── NAVBAR ── */
        .navbar { background: rgba(247,242,234,0.97); backdrop-filter: blur(20px); padding: 0.8rem 0; border-bottom: 1px solid var(--border); z-index: 1000; }
        .navbar-brand { font-family: var(--fh); font-size: 1.6rem; letter-spacing: 3px; color: var(--brown) !important; font-weight: 600; }
        .nav-link-custom { color: var(--dark); font-weight: 500; font-size: 0.75rem; margin-left: 20px; text-decoration: none; transition: color .3s; text-transform: uppercase; letter-spacing: 2px; position: relative; }
        .nav-link-custom::after { content:''; position:absolute; bottom:-4px; left:0; width:0; height:1px; background:var(--gold); transition:width .3s; }
        .nav-link-custom:hover::after, .nav-link-custom.active::after { width:100%; }
        .nav-link-custom:hover, .nav-link-custom.active { color: var(--gold) !important; }
        .btn-book { background:var(--brown); color:#fff !important; padding:8px 20px; border-radius:2px; font-size:0.75rem; font-weight:500; text-transform:uppercase; letter-spacing:2px; border:1px solid var(--brown); text-decoration:none; transition:all .3s; margin-left:20px; }
        .btn-book:hover { background:transparent; color:var(--brown) !important; }

        /* ── PAGE HERO ── */
        .page-hero {
            padding: 155px 0 90px;
            background: linear-gradient(165deg, var(--cream2) 0%, var(--cream) 55%, var(--cream3) 100%);
            border-bottom: 1px solid var(--border);
            position: relative;
            overflow: hidden;
            text-align: center;
        }
        .page-hero::before {
            content: '';
            position: absolute; top: -80px; left: 50%; transform: translateX(-50%);
            width: 700px; height: 700px;
            background: radial-gradient(circle, rgba(184,132,42,0.07) 0%, transparent 65%);
            pointer-events: none;
        }
        .hero-bg-letter {
            position: absolute;
            font-family: var(--fh); font-size: 20rem; font-weight: 700; font-style: italic;
            color: rgba(184,132,42,0.04); line-height: 1;
            pointer-events: none; user-select: none;
            top: 0; left: 50%; transform: translateX(-50%);
            white-space: nowrap;
        }
        .hero-eyebrow {
            display: inline-flex; align-items: center; gap: 10px;
            font-family: var(--fb); font-size: .62rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: 4px; color: var(--terra);
            margin-bottom: 18px;
        }
        .hero-eyebrow::before, .hero-eyebrow::after { content:''; width:30px; height:1px; background:var(--terra); }
        .page-hero h1 { font-size: 4rem; line-height: 1.12; letter-spacing: -0.5px; margin-bottom: 18px; }
        .page-hero h1 em { color: var(--brown); font-style: italic; }
        .page-hero .lead {
            color: var(--muted); font-size: 0.92rem; line-height: 1.9;
            font-weight: 300; max-width: 560px; margin: 0 auto 32px;
        }

        /* ── SEARCH BAR ── */
        .faq-search-wrap {
            max-width: 480px;
            margin: 0 auto;
            position: relative;
        }
        .faq-search {
            width: 100%;
            padding: 14px 50px 14px 22px;
            border: 1px solid var(--border2);
            border-radius: 2px;
            background: var(--white);
            font-family: var(--fb);
            font-size: 0.85rem;
            color: var(--dark);
            outline: none;
            transition: border-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 16px var(--shadow);
        }
        .faq-search:focus { border-color: var(--gold); box-shadow: 0 4px 20px rgba(184,132,42,0.15); }
        .faq-search::placeholder { color: var(--light); font-size: 0.82rem; }
        .faq-search-icon {
            position: absolute; right: 16px; top: 50%; transform: translateY(-50%);
            color: var(--light); font-size: 0.9rem; pointer-events: none;
        }

        /* ── CATEGORY TABS ── */
        .faq-section { padding: 80px 0 100px; position: relative; z-index: 1; }

        .cat-tabs {
            display: flex; flex-wrap: wrap; gap: 10px;
            margin-bottom: 52px; justify-content: center;
        }
        .cat-tab {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 9px 20px; border-radius: 2px;
            border: 1px solid var(--border2);
            background: var(--white);
            font-family: var(--fb); font-size: 0.7rem; font-weight: 500;
            letter-spacing: 1.5px; text-transform: uppercase;
            color: var(--muted);
            cursor: pointer;
            transition: all 0.3s ease;
            user-select: none;
        }
        .cat-tab i { font-size: 0.75rem; }
        .cat-tab:hover { border-color: var(--gold); color: var(--gold); }
        .cat-tab.active {
            background: var(--dark);
            border-color: var(--dark);
            color: var(--gold2);
        }

        /* ── FAQ GROUPS ── */
        .faq-group { margin-bottom: 48px; display: none; }
        .faq-group.visible { display: block; }

        .faq-group-label {
            display: flex; align-items: center; gap: 14px;
            margin-bottom: 20px;
        }
        .faq-group-label .g-icon {
            width: 38px; height: 38px; min-width: 38px;
            background: linear-gradient(135deg, var(--cream3), var(--cream2));
            border: 1px solid var(--border2);
            border-radius: 3px;
            display: flex; align-items: center; justify-content: center;
        }
        .faq-group-label .g-icon i { color: var(--brown); font-size: 0.85rem; }
        .faq-group-label span {
            font-family: var(--fh); font-size: 1.3rem; font-weight: 700;
            color: var(--dark); letter-spacing: 0.3px;
        }
        .faq-group-line {
            flex: 1; height: 1px; background: var(--border2);
        }

        /* ── ACCORDION ITEMS ── */
        .faq-item {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 3px;
            margin-bottom: 10px;
            overflow: hidden;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .faq-item:hover { border-color: var(--gold); }
        .faq-item.open {
            border-color: var(--gold2);
            box-shadow: 0 6px 24px rgba(184,132,42,0.12);
        }

        .faq-question {
            display: flex; align-items: center; justify-content: space-between;
            padding: 20px 24px;
            cursor: pointer;
            gap: 16px;
            user-select: none;
        }
        .faq-q-left {
            display: flex; align-items: center; gap: 16px;
        }
        .faq-num {
            font-family: var(--fh); font-size: 0.85rem; font-weight: 700;
            color: var(--gold); min-width: 28px; letter-spacing: 1px;
        }
        .faq-question h6 {
            font-family: var(--fb); font-size: 0.88rem; font-weight: 600;
            color: var(--dark); margin: 0; line-height: 1.4;
            transition: color 0.3s;
        }
        .faq-item.open .faq-question h6 { color: var(--brown); }

        .faq-toggle {
            width: 32px; height: 32px; min-width: 32px;
            border: 1px solid var(--border2);
            border-radius: 2px;
            display: flex; align-items: center; justify-content: center;
            color: var(--light);
            transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
            background: var(--cream2);
        }
        .faq-toggle i { font-size: 0.7rem; transition: transform 0.35s ease; }
        .faq-item.open .faq-toggle {
            background: var(--gold);
            border-color: var(--gold);
            color: #fff;
        }
        .faq-item.open .faq-toggle i { transform: rotate(180deg); }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.45s cubic-bezier(0.4, 0, 0.2, 1),
                        padding 0.3s ease;
        }
        .faq-answer-inner {
            padding: 0 24px 22px 68px;
            border-top: 1px solid var(--border);
        }
        .faq-answer-inner p {
            color: var(--muted); font-size: 0.85rem; line-height: 1.9;
            font-weight: 300; margin: 14px 0 0;
        }
        .faq-answer-inner ul {
            margin: 12px 0 0 0; padding-left: 18px;
            color: var(--muted); font-size: 0.84rem; line-height: 1.9; font-weight: 300;
        }
        .faq-answer-inner ul li { margin-bottom: 4px; }
        .faq-answer-inner .note {
            display: inline-flex; align-items: flex-start; gap: 8px;
            background: var(--cream2); border-left: 3px solid var(--gold);
            padding: 10px 14px; border-radius: 0 2px 2px 0;
            font-size: 0.78rem; color: var(--muted); margin-top: 12px;
            line-height: 1.7;
        }
        .faq-answer-inner .note i { color: var(--gold); margin-top: 2px; font-size: 0.75rem; }

        /* ── STILL HAVE QUESTIONS ── */
        .contact-cta {
            background: var(--dark);
            border-radius: 4px;
            padding: 60px 50px;
            text-align: center;
            position: relative;
            overflow: hidden;
            margin-top: 60px;
        }
        .contact-cta::before {
            content: '';
            position: absolute; inset: 0;
            background: radial-gradient(ellipse at 50% -10%, rgba(184,132,42,0.15) 0%, transparent 60%);
            pointer-events: none;
        }
        .contact-cta h3 {
            font-size: 2rem; color: #f2ece0; margin-bottom: 12px;
        }
        .contact-cta h3 em { color: var(--gold2); font-style: italic; }
        .contact-cta p { color: rgba(242,236,224,0.55); font-size: 0.86rem; line-height: 1.85; max-width: 420px; margin: 0 auto 28px; font-weight: 300; }
        .btn-contact {
            display: inline-flex; align-items: center; gap: 8px;
            background: linear-gradient(135deg, var(--gold), var(--brown));
            color: #fff; text-decoration: none;
            padding: 12px 30px; border-radius: 2px;
            font-size: 0.7rem; font-weight: 600; letter-spacing: 2.5px;
            text-transform: uppercase; transition: all 0.35s ease;
            box-shadow: 0 8px 25px rgba(124,92,46,0.35); border: none; cursor: pointer;
        }
        .btn-contact:hover { transform: translateY(-3px); box-shadow: 0 15px 35px rgba(124,92,46,0.45); color: #fff; }

        /* No results */
        .no-results {
            text-align: center; padding: 60px 0; display: none;
        }
        .no-results i { font-size: 2.5rem; color: var(--border2); margin-bottom: 16px; display: block; }
        .no-results p { color: var(--light); font-size: 0.88rem; }

        /* ── FOOTER ── */
        footer { background: var(--black); padding: 50px 0 20px; border-top: 1px solid rgba(255,255,255,.06); position: relative; z-index: 1; }
        footer h6 { color:#ede5d3 !important; font-family:var(--fh); font-weight:600 !important; }
        footer p { color:#a89880; font-size:.8rem; }
        .flink { color:#a89880; font-size:.8rem; text-decoration:none; display:block; margin-bottom:6px; transition:all .2s; }
        .flink:hover { color:var(--gold); padding-left:5px; }
        .fbrand { font-family:var(--fh); font-size:1.4rem; color:#f2ece0 !important; letter-spacing:2px; font-weight:600; display:block; margin-bottom:8px; }

        /* ── SCROLL REVEAL ── */
        .rv { opacity: 0; transform: translateY(28px); transition: opacity 0.75s ease, transform 0.75s ease; }
        .rv.on { opacity: 1; transform: translateY(0); }
        .d1{transition-delay:.06s} .d2{transition-delay:.12s} .d3{transition-delay:.18s}
        .d4{transition-delay:.24s} .d5{transition-delay:.30s}

        @media(max-width:767px) {
            .page-hero h1 { font-size: 2.4rem; }
            .hero-bg-letter { font-size: 10rem; }
            .faq-answer-inner { padding-left: 24px; }
            .contact-cta { padding: 44px 28px; }
        }
    </style>
</head>
<body>

    <!-- ── NAVBAR ── -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid px-4 px-lg-5">
            <a class="navbar-brand" href="index.php">VISITEASE</a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
                <i class="fas fa-bars" style="color:var(--brown)"></i>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="nav">
                <div class="navbar-nav align-items-center">
                    <a href="index.php"        class="nav-link-custom">Home</a>
                    <a href="exhibits.php"     class="nav-link-custom">Explore</a>
                    <a href="gallery.php"      class="nav-link-custom">Gallery</a>
                    <a href="about.php"        class="nav-link-custom">About</a>
                    <a href="faqs.php"         class="nav-link-custom active">FAQs</a>
                    <a href="check_status.php" class="nav-link-custom">Track Status</a>
                    <a href="book.php"         class="btn-book">Book Visit</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- ── HERO ── -->
    <header class="page-hero">
        <div class="hero-bg-letter">FAQ</div>
        <div class="container position-relative rv">
            <div class="hero-eyebrow">Help Center</div>
            <h1>Frequently Asked <em>Questions</em></h1>
            <p class="lead">
                Got questions? We have answers. Browse our most commonly asked questions below,
                or use the search to find exactly what you're looking for.
            </p>
            <!-- Search -->
            <div class="faq-search-wrap rv d2">
                <input type="text" id="faqSearch" class="faq-search" placeholder="Search questions e.g. booking, payment, cancellation...">
                <i class="fas fa-search faq-search-icon"></i>
            </div>
        </div>
    </header>

    <!-- ── FAQ SECTION ── -->
    <section class="faq-section">
        <div class="container" style="max-width: 860px;">

            <!-- Category Tabs -->
            <div class="cat-tabs rv">
                <div class="cat-tab active" data-cat="all"><i class="fas fa-th-large"></i> All Topics</div>
                <div class="cat-tab" data-cat="booking"><i class="fas fa-ticket-alt"></i> Booking</div>
                <div class="cat-tab" data-cat="account"><i class="fas fa-user-circle"></i> Account</div>
                <div class="cat-tab" data-cat="payment"><i class="fas fa-credit-card"></i> Payment</div>
                <div class="cat-tab" data-cat="schedule"><i class="fas fa-calendar-alt"></i> Schedules</div>
                <div class="cat-tab" data-cat="cancel"><i class="fas fa-undo-alt"></i> Cancellation</div>
                <div class="cat-tab" data-cat="trouble"><i class="fas fa-tools"></i> Troubleshooting</div>
            </div>

            <div id="noResults" class="no-results">
                <i class="fas fa-search-minus"></i>
                <p>No questions matched your search. Try a different keyword.</p>
            </div>

            <!-- ───────────────────────────────────────
                 GROUP 1 — BOOKING
            ──────────────────────────────────────── -->
            <div class="faq-group rv d1" data-group="booking" id="group-booking">
                <div class="faq-group-label">
                    <div class="g-icon"><i class="fas fa-ticket-alt"></i></div>
                    <span>Booking a Visit</span>
                    <div class="faq-group-line"></div>
                </div>

                <div class="faq-item" data-cat="booking">
                    <div class="faq-question">
                        <div class="faq-q-left">
                            <span class="faq-num">01</span>
                            <h6>How do I book a museum visit through VisitEase?</h6>
                        </div>
                        <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-inner">
                            <p>Booking a visit is quick and easy. Simply follow these steps:</p>
                            <ul>
                                <li>Go to the <strong>Book Visit</strong> page from the navigation menu.</li>
                                <li>Select your preferred visit date and time slot.</li>
                                <li>Enter the number of visitors and fill in the required details.</li>
                                <li>Review your booking summary and confirm your reservation.</li>
                                <li>A confirmation will be sent to your registered email address.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-cat="booking">
                    <div class="faq-question">
                        <div class="faq-q-left">
                            <span class="faq-num">02</span>
                            <h6>Can I book tickets for a group or multiple visitors?</h6>
                        </div>
                        <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-inner">
                            <p>Yes! VisitEase supports group bookings. During the booking process, simply enter the total number of visitors in the designated field. Please note that group bookings are subject to availability, and some time slots may have a maximum capacity limit.</p>
                            <div class="note"><i class="fas fa-info-circle"></i> For large groups of 20 or more, we recommend booking at least 3–5 days in advance to ensure your preferred schedule is available.</div>
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-cat="booking">
                    <div class="faq-question">
                        <div class="faq-q-left">
                            <span class="faq-num">03</span>
                            <h6>How will I receive my booking confirmation?</h6>
                        </div>
                        <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-inner">
                            <p>Once your reservation is successfully submitted, a booking confirmation will be sent to the email address you provided during the booking process. The confirmation includes your booking reference number, visit date, time, and number of visitors.</p>
                            <p>You can also check your booking status anytime by visiting the <strong>Track Status</strong> page and entering your reference number.</p>
                            <div class="note"><i class="fas fa-info-circle"></i> If you do not receive a confirmation email within a few minutes, please check your spam or junk folder.</div>
                        </div>
                    </div>
                </div>

            </div><!-- /group booking -->

            <!-- ───────────────────────────────────────
                 GROUP 2 — ACCOUNT
            ──────────────────────────────────────── -->
            <div class="faq-group rv d2" data-group="account" id="group-account">
                <div class="faq-group-label">
                    <div class="g-icon"><i class="fas fa-user-circle"></i></div>
                    <span>Account & Registration</span>
                    <div class="faq-group-line"></div>
                </div>

                <div class="faq-item" data-cat="account">
                    <div class="faq-question">
                        <div class="faq-q-left">
                            <span class="faq-num">04</span>
                            <h6>Do I need to create an account to book a visit?</h6>
                        </div>
                        <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-inner">
                            <p>No, you do not need to create an account to make a reservation. VisitEase allows guest bookings — simply fill in your name, contact information, and visit details during checkout. However, creating an account gives you added benefits such as viewing your booking history and managing future reservations more conveniently.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-cat="account">
                    <div class="faq-question">
                        <div class="faq-q-left">
                            <span class="faq-num">05</span>
                            <h6>How do I register for a VisitEase account?</h6>
                        </div>
                        <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-inner">
                            <p>To register, click the <strong>Sign Up</strong> or <strong>Register</strong> button on the homepage or booking page. You will be asked to provide your full name, email address, and a secure password. Once submitted, verify your email address through the confirmation link sent to your inbox, and your account will be ready to use.</p>
                        </div>
                    </div>
                </div>

            </div><!-- /group account -->

            <!-- ───────────────────────────────────────
                 GROUP 3 — PAYMENT
            ──────────────────────────────────────── -->
            <div class="faq-group rv d3" data-group="payment" id="group-payment">
                <div class="faq-group-label">
                    <div class="g-icon"><i class="fas fa-credit-card"></i></div>
                    <span>Payment & Fees</span>
                    <div class="faq-group-line"></div>
                </div>

                <div class="faq-item" data-cat="payment">
                    <div class="faq-question">
                        <div class="faq-q-left">
                            <span class="faq-num">06</span>
                            <h6>What payment methods does VisitEase accept?</h6>
                        </div>
                        <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-inner">
                            <p>VisitEase currently accepts the following payment methods:</p>
                            <ul>
                                <li>Cash on arrival (pay at the museum entrance)</li>
                                <li>GCash and other local e-wallets</li>
                                <li>Online bank transfer</li>
                                <li>Credit or debit card (Visa / Mastercard)</li>
                            </ul>
                            <div class="note"><i class="fas fa-lock"></i> All online transactions are encrypted and secure. We do not store your card details on our servers.</div>
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-cat="payment">
                    <div class="faq-question">
                        <div class="faq-q-left">
                            <span class="faq-num">07</span>
                            <h6>Is my payment information safe on VisitEase?</h6>
                        </div>
                        <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-inner">
                            <p>Absolutely. VisitEase takes your security seriously. All payment transactions are processed through encrypted, industry-standard secure connections. Your financial information is never stored on our servers and is handled only by trusted payment gateways. You can book with complete peace of mind.</p>
                        </div>
                    </div>
                </div>

            </div><!-- /group payment -->

            <!-- ───────────────────────────────────────
                 GROUP 4 — SCHEDULE
            ──────────────────────────────────────── -->
            <div class="faq-group rv d4" data-group="schedule" id="group-schedule">
                <div class="faq-group-label">
                    <div class="g-icon"><i class="fas fa-calendar-alt"></i></div>
                    <span>Museum Schedules & Hours</span>
                    <div class="faq-group-line"></div>
                </div>

                <div class="faq-item" data-cat="schedule">
                    <div class="faq-question">
                        <div class="faq-q-left">
                            <span class="faq-num">08</span>
                            <h6>What are the museum's operating hours?</h6>
                        </div>
                        <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-inner">
                            <p>The museum is open <strong>Monday to Saturday, 9:00 AM – 6:00 PM</strong>. The museum is closed on <strong>Sundays</strong> and on selected public holidays. We recommend checking the VisitEase booking page for real-time availability before planning your visit.</p>
                            <div class="note"><i class="fas fa-info-circle"></i> Last entry is at 5:30 PM. Please arrive early to make the most of your visit.</div>
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-cat="schedule">
                    <div class="faq-question">
                        <div class="faq-q-left">
                            <span class="faq-num">09</span>
                            <h6>How do I know if my preferred date is still available?</h6>
                        </div>
                        <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-inner">
                            <p>VisitEase features <strong>real-time availability</strong> — when you access the booking page and select a date, you will immediately see which time slots are open, limited, or fully booked. Dates that are unavailable will be grayed out or marked as full, so you can easily choose an alternative.</p>
                        </div>
                    </div>
                </div>

            </div><!-- /group schedule -->

            <!-- ───────────────────────────────────────
                 GROUP 5 — CANCELLATION
            ──────────────────────────────────────── -->
            <div class="faq-group rv d5" data-group="cancel" id="group-cancel">
                <div class="faq-group-label">
                    <div class="g-icon"><i class="fas fa-undo-alt"></i></div>
                    <span>Cancellation & Rescheduling</span>
                    <div class="faq-group-line"></div>
                </div>

                <div class="faq-item" data-cat="cancel">
                    <div class="faq-question">
                        <div class="faq-q-left">
                            <span class="faq-num">10</span>
                            <h6>Can I cancel or reschedule my booking?</h6>
                        </div>
                        <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-inner">
                            <p>Yes, you may cancel or reschedule your reservation subject to our policy guidelines. To do so, visit the <strong>Track Status</strong> page, enter your booking reference number, and select the option to modify or cancel your booking. We recommend making any changes at least <strong>24 hours before</strong> your scheduled visit.</p>
                            <div class="note"><i class="fas fa-exclamation-triangle"></i> Cancellations or changes made less than 24 hours before the visit may not be eligible for rescheduling, depending on availability.</div>
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-cat="cancel">
                    <div class="faq-question">
                        <div class="faq-q-left">
                            <span class="faq-num">11</span>
                            <h6>Will I get a refund if I cancel my booking?</h6>
                        </div>
                        <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-inner">
                            <p>Refund eligibility depends on the type of payment made and the timing of your cancellation:</p>
                            <ul>
                                <li><strong>Cash on arrival</strong> — No charge is made in advance, so no refund is needed.</li>
                                <li><strong>Online payment</strong> — Cancellations made at least 48 hours in advance are eligible for a full refund processed within 3–5 business days.</li>
                                <li><strong>Same-day cancellations</strong> — Refunds are not guaranteed and will be evaluated on a case-by-case basis.</li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div><!-- /group cancel -->

            <!-- ───────────────────────────────────────
                 GROUP 6 — TROUBLESHOOTING
            ──────────────────────────────────────── -->
            <div class="faq-group rv" data-group="trouble" id="group-trouble">
                <div class="faq-group-label">
                    <div class="g-icon"><i class="fas fa-tools"></i></div>
                    <span>Troubleshooting</span>
                    <div class="faq-group-line"></div>
                </div>

                <div class="faq-item" data-cat="trouble">
                    <div class="faq-question">
                        <div class="faq-q-left">
                            <span class="faq-num">12</span>
                            <h6>I didn't receive a confirmation email. What should I do?</h6>
                        </div>
                        <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-inner">
                            <p>First, check your <strong>spam or junk mail folder</strong> — confirmation emails sometimes get filtered automatically. If it is not there, try the following:</p>
                            <ul>
                                <li>Go to <strong>Track Status</strong> and enter your booking reference number to verify your reservation was recorded.</li>
                                <li>Ensure you entered the correct email address during booking.</li>
                                <li>Wait a few more minutes and refresh your inbox, as emails may occasionally be delayed.</li>
                            </ul>
                            <div class="note"><i class="fas fa-info-circle"></i> If the issue persists, please contact us directly through our Help Center and provide your full name and booking date.</div>
                        </div>
                    </div>
                </div>

                <div class="faq-item" data-cat="trouble">
                    <div class="faq-question">
                        <div class="faq-q-left">
                            <span class="faq-num">13</span>
                            <h6>The booking page is not loading properly. How can I fix this?</h6>
                        </div>
                        <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-inner">
                            <p>If you are experiencing issues with the booking page, try these quick fixes:</p>
                            <ul>
                                <li>Refresh the page or clear your browser's cache and cookies.</li>
                                <li>Try using a different browser (Chrome, Firefox, or Edge are recommended).</li>
                                <li>Check your internet connection and ensure it is stable.</li>
                                <li>Disable any browser extensions that may be blocking the page.</li>
                            </ul>
                            <p>If the problem continues, the system may be undergoing maintenance. Please try again after a few minutes.</p>
                        </div>
                    </div>
                </div>

            </div><!-- /group trouble -->

            <!-- ── CONTACT CTA ── -->
            <div class="contact-cta rv">
                <h3>Still Have <em>Questions?</em></h3>
                <p>Can't find what you're looking for? Our team is happy to help. Reach out to us and we'll get back to you as soon as possible.</p>
                <a href="mailto:support@visitease.ph" class="btn-contact">
                    <i class="fas fa-envelope"></i> Contact Support
                </a>
            </div>
.2.01
        </div>
    </section>

    <!-- ── FOOTER ── -->
    <footer>
        <div class="container-fluid px-4 px-lg-5">
            <div class="row g-4 border-bottom pb-3 mb-3" style="border-color:rgba(255,255,255,.07)!important">
                <div class="col-md-4">
                    <span class="fbrand">VISITEASE</span>
                    <p style="color:#8a7e6e;line-height:1.6;max-width:270px">Making museum visits seamless and memorable. Explore history and culture with ease.</p>
                </div>
                <div class="col-6 col-md-2">
                    <h6 class="mb-2" style="font-family:var(--fb)!important;font-size:.65rem!important;letter-spacing:2px;text-transform:uppercase">Explore</h6>
                    <a class="flink" href="exhibits.php">Explore Exhibits</a>
                    <a class="flink" href="gallery.php">Gallery</a>
                </div>
                <div class="col-6 col-md-2">
                    <h6 class="mb-2" style="font-family:var(--fb)!important;font-size:.65rem!important;letter-spacing:2px;text-transform:uppercase">Visit</h6>
                    <a class="flink" href="book.php">Book Tickets</a>
                    <a class="flink" href="check_status.php">Track Status</a>
                </div>
                <div class="col-md-4">
                    <h6 class="mb-2" style="font-family:var(--fb)!important;font-size:.65rem!important;letter-spacing:2px;text-transform:uppercase">Location</h6>
                    <p style="color:#8a7e6e;line-height:1.6;margin-bottom:6px"><i class="fas fa-map-marker-alt me-2" style="color:var(--gold)"></i>Barangay Ilijan, Batangas City</p>
                    <p style="color:#8a7e6e;margin-bottom:4px"><i class="fas fa-clock me-2" style="color:var(--gold)"></i>Mon–Sat: 9:00 AM – 6:00 PM</p>
                </div>
            </div>
            <div class="text-center" style="color:#5a5045;font-size:.7rem">
                &copy; 2026 VisitEase Museum. All Rights Reserved.
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function () {

        // ── Scroll Reveal ──
        const observer = new IntersectionObserver(entries => {
            entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('on'); });
        }, { threshold: 0.06 });
        document.querySelectorAll('.rv').forEach(el => observer.observe(el));

        // ── Accordion ──
        document.querySelectorAll('.faq-question').forEach(q => {
            q.addEventListener('click', function () {
                const item   = this.closest('.faq-item');
                const answer = item.querySelector('.faq-answer');
                const isOpen = item.classList.contains('open');

                // Close all
                document.querySelectorAll('.faq-item.open').forEach(i => {
                    i.classList.remove('open');
                    i.querySelector('.faq-answer').style.maxHeight = '0';
                });

                // Open clicked
                if (!isOpen) {
                    item.classList.add('open');
                    answer.style.maxHeight = answer.scrollHeight + 'px';
                }
            });
        });

        // ── Category Filter ──
        const tabs   = document.querySelectorAll('.cat-tab');
        const groups = document.querySelectorAll('.faq-group');

        function showAll() {
            groups.forEach(g => {
                g.classList.add('visible');
                g.querySelectorAll('.faq-item').forEach(i => i.style.display = '');
            });
        }

        tabs.forEach(tab => {
            tab.addEventListener('click', function () {
                tabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                const cat = this.dataset.cat;

                // Clear search
                document.getElementById('faqSearch').value = '';
                document.getElementById('noResults').style.display = 'none';

                if (cat === 'all') {
                    showAll();
                    return;
                }
                groups.forEach(g => {
                    const matchingItems = g.querySelectorAll(`.faq-item[data-cat="${cat}"]`);
                    if (matchingItems.length) {
                        g.classList.add('visible');
                        g.querySelectorAll('.faq-item').forEach(i => {
                            i.style.display = i.dataset.cat === cat ? '' : 'none';
                        });
                    } else {
                        g.classList.remove('visible');
                    }
                });
            });
        });

        // Initial: show all
        showAll();

        // ── Live Search ──
        document.getElementById('faqSearch').addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();

            // Reset tabs
            tabs.forEach(t => t.classList.remove('active'));
            document.querySelector('[data-cat="all"]').classList.add('active');

            if (!query) {
                showAll();
                document.getElementById('noResults').style.display = 'none';
                return;
            }

            let anyMatch = false;
            groups.forEach(g => {
                let groupHit = false;
                g.querySelectorAll('.faq-item').forEach(item => {
                    const qText = item.querySelector('h6').textContent.toLowerCase();
                    const aText = item.querySelector('.faq-answer-inner').textContent.toLowerCase();
                    const match = qText.includes(query) || aText.includes(query);
                    item.style.display = match ? '' : 'none';
                    if (match) { groupHit = true; anyMatch = true; }
                });
                g.classList.toggle('visible', groupHit);
            });

            document.getElementById('noResults').style.display = anyMatch ? 'none' : 'block';
        });
    });
    </script>
</body>
</html>