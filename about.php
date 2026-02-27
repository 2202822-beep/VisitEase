<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VisitEase | About Us</title>

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
            --border2:rgba(184,132,42,0.35);
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

        /* Grain texture */
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
            padding: 160px 0 100px;
            background: linear-gradient(165deg, var(--cream2) 0%, var(--cream) 55%, var(--cream3) 100%);
            border-bottom: 1px solid var(--border);
            position: relative;
            overflow: hidden;
            text-align: center;
        }
        .page-hero::before {
            content: '';
            position: absolute; top: -100px; left: 50%; transform: translateX(-50%);
            width: 700px; height: 700px;
            background: radial-gradient(circle, rgba(184,132,42,0.08) 0%, transparent 65%);
            pointer-events: none;
        }
        /* Big decorative letter */
        .hero-bg-letter {
            position: absolute;
            font-family: var(--fh);
            font-size: 22rem;
            font-weight: 700;
            font-style: italic;
            color: rgba(184,132,42,0.04);
            line-height: 1;
            pointer-events: none;
            user-select: none;
            top: -30px; left: 50%; transform: translateX(-50%);
            white-space: nowrap;
        }
        .hero-eyebrow {
            display: inline-flex; align-items: center; gap: 10px;
            font-family: var(--fb); font-size: .62rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: 4px; color: var(--terra);
            margin-bottom: 18px;
        }
        .hero-eyebrow::before, .hero-eyebrow::after { content:''; width:30px; height:1px; background:var(--terra); }
        .page-hero h1 { font-size: 4.2rem; line-height: 1.12; letter-spacing: -0.5px; margin-bottom: 20px; }
        .page-hero h1 em { color: var(--brown); font-style: italic; }
        .page-hero .lead {
            color: var(--muted); font-size: 0.95rem; line-height: 1.9;
            font-weight: 300; max-width: 580px; margin: 0 auto;
        }

        /* ── INTRO STRIP ── */
        .intro-strip {
            background: var(--dark);
            padding: 18px 0;
            position: relative; z-index: 1;
        }
        .intro-strip-inner {
            display: flex; align-items: center; justify-content: center; gap: 40px;
            flex-wrap: wrap;
        }
        .strip-stat {
            text-align: center;
        }
        .strip-stat strong {
            display: block;
            font-family: var(--fh); font-size: 2rem; font-weight: 700;
            color: var(--gold2); line-height: 1;
        }
        .strip-stat span {
            font-size: 0.6rem; letter-spacing: 3px; text-transform: uppercase;
            color: rgba(242,236,224,0.45);
        }
        .strip-divider { width: 1px; height: 38px; background: rgba(255,255,255,0.08); }

        /* ── ABOUT MAIN CONTENT ── */
        .about-section { padding: 100px 0; position: relative; z-index: 1; }

        /* Mission block */
        .mission-block {
            position: relative;
            padding: 60px;
            background: var(--white);
            border: 1px solid var(--border2);
            border-radius: 4px;
            box-shadow: 0 10px 40px var(--shadow);
        }
        .mission-block::before {
            content: '\201C';
            font-family: var(--fh);
            font-size: 9rem;
            color: rgba(184,132,42,0.08);
            position: absolute;
            top: 10px; left: 30px;
            line-height: 1;
            pointer-events: none;
            font-style: italic;
        }
        .section-eyebrow {
            display: inline-flex; align-items: center; gap: 10px;
            font-family: var(--fb); font-size: .6rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: 4px; color: var(--terra);
            margin-bottom: 14px;
        }
        .section-eyebrow::before { content:''; width:22px; height:1px; background:var(--terra); }
        .mission-block h2 {
            font-size: 2.4rem; line-height: 1.2; margin-bottom: 22px; letter-spacing: -0.3px;
        }
        .mission-block h2 em { font-style: italic; color: var(--brown); }
        .mission-block p {
            color: var(--muted); font-size: 0.92rem; line-height: 1.95;
            font-weight: 300; margin-bottom: 16px;
        }
        .mission-block p:last-of-type { margin-bottom: 0; }

        /* Gold accent bar */
        .gold-bar {
            width: 55px; height: 3px;
            background: linear-gradient(90deg, var(--gold), var(--gold2));
            border-radius: 2px; margin-bottom: 22px;
        }

        /* ── FEATURES GRID ── */
        .features-section { padding: 80px 0 90px; background: var(--cream2); position: relative; z-index: 1; border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }

        .section-header { text-align: center; margin-bottom: 60px; }
        .section-header h2 { font-size: 2.6rem; margin-bottom: 12px; }
        .section-header h2 em { color: var(--brown); font-style: italic; }
        .section-header p { color: var(--muted); font-size: 0.88rem; line-height: 1.8; max-width: 480px; margin: 0 auto; font-weight: 300; }

        .feature-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 4px;
            padding: 40px 32px;
            height: 100%;
            position: relative;
            overflow: hidden;
            transition: all 0.45s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            box-shadow: 0 4px 16px var(--shadow);
        }
        .feature-card::after {
            content: '';
            position: absolute; bottom: 0; left: 0;
            width: 100%; height: 3px;
            background: linear-gradient(90deg, var(--gold), var(--gold2));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.4s ease;
        }
        .feature-card:hover { transform: translateY(-8px); box-shadow: 0 20px 50px rgba(124,92,46,0.18); border-color: var(--gold2); }
        .feature-card:hover::after { transform: scaleX(1); }

        .feature-icon {
            width: 56px; height: 56px;
            background: linear-gradient(135deg, var(--cream3), var(--cream2));
            border: 1px solid var(--border2);
            border-radius: 3px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 24px;
            transition: all 0.4s ease;
        }
        .feature-card:hover .feature-icon { background: linear-gradient(135deg, var(--gold), var(--brown)); border-color: var(--gold); }
        .feature-icon i { font-size: 1.2rem; color: var(--brown); transition: color 0.4s; }
        .feature-card:hover .feature-icon i { color: #fff; }

        .feature-card h5 { font-size: 1.05rem; margin-bottom: 12px; letter-spacing: 0.2px; }
        .feature-card p { color: var(--muted); font-size: 0.82rem; line-height: 1.85; font-weight: 300; margin: 0; }

        /* Feature number */
        .feature-num {
            position: absolute; top: 18px; right: 22px;
            font-family: var(--fh); font-size: 3.5rem;
            color: rgba(184,132,42,0.07); font-weight: 700; line-height: 1;
            pointer-events: none;
        }

        /* ── VISION / MISSION COLUMNS ── */
        .vm-section { padding: 90px 0; position: relative; z-index: 1; }

        .vm-card {
            padding: 50px 42px;
            height: 100%;
            border-radius: 4px;
            position: relative;
            overflow: hidden;
        }
        .vm-card.vision { background: var(--dark); }
        .vm-card.mission-v { background: var(--white); border: 1px solid var(--border2); }

        .vm-card::before {
            content: attr(data-letter);
            font-family: var(--fh); font-style: italic;
            font-size: 11rem; font-weight: 700;
            position: absolute; bottom: -20px; right: -10px;
            line-height: 1; pointer-events: none; user-select: none;
        }
        .vm-card.vision::before { color: rgba(212,168,75,0.07); }
        .vm-card.mission-v::before { color: rgba(184,132,42,0.05); }

        .vm-card .section-eyebrow { margin-bottom: 12px; }
        .vm-card.vision .section-eyebrow { color: var(--gold2); }
        .vm-card.vision .section-eyebrow::before { background: var(--gold2); }

        .vm-card h3 { font-size: 2rem; margin-bottom: 18px; line-height: 1.25; }
        .vm-card.vision h3 { color: #f2ece0; }
        .vm-card p { font-size: 0.88rem; line-height: 1.9; font-weight: 300; margin-bottom: 12px; }
        .vm-card.vision p { color: rgba(242,236,224,0.65); }
        .vm-card.mission-v p { color: var(--muted); }

        /* ── TEAM / CLOSING CTA ── */
        .cta-section {
            padding: 100px 0;
            background: linear-gradient(135deg, var(--dark) 0%, #2e2416 100%);
            text-align: center;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        .cta-section::before {
            content: '';
            position: absolute; inset: 0;
            background: radial-gradient(ellipse at 50% 0%, rgba(184,132,42,0.12) 0%, transparent 60%);
            pointer-events: none;
        }
        /* Decorative lines */
        .cta-section::after {
            content: '';
            position: absolute;
            top: 0; left: 50%; transform: translateX(-50%);
            width: 1px; height: 60px;
            background: linear-gradient(to bottom, var(--gold2), transparent);
        }
        .cta-section h2 { font-size: 3rem; color: #f2ece0; margin-bottom: 18px; line-height: 1.2; }
        .cta-section h2 em { color: var(--gold2); font-style: italic; }
        .cta-section p { color: rgba(242,236,224,0.55); font-size: 0.9rem; line-height: 1.9; max-width: 500px; margin: 0 auto 38px; font-weight: 300; }

        .btn-primary-ve {
            display: inline-flex; align-items: center; gap: 10px;
            background: linear-gradient(135deg, var(--gold), var(--brown));
            color: #fff; text-decoration: none;
            padding: 14px 36px; border-radius: 2px;
            font-size: 0.72rem; font-weight: 600;
            letter-spacing: 2.5px; text-transform: uppercase;
            border: none; cursor: pointer;
            transition: all 0.35s ease;
            box-shadow: 0 8px 25px rgba(124,92,46,0.35);
        }
        .btn-primary-ve:hover { transform: translateY(-3px); box-shadow: 0 15px 35px rgba(124,92,46,0.45); color: #fff; }

        .btn-outline-ve {
            display: inline-flex; align-items: center; gap: 10px;
            background: transparent;
            color: rgba(242,236,224,0.8); text-decoration: none;
            padding: 13px 32px; border-radius: 2px;
            font-size: 0.72rem; font-weight: 500;
            letter-spacing: 2.5px; text-transform: uppercase;
            border: 1px solid rgba(242,236,224,0.25); cursor: pointer;
            transition: all 0.35s ease;
            margin-left: 16px;
        }
        .btn-outline-ve:hover { border-color: var(--gold2); color: var(--gold2); transform: translateY(-3px); }

        /* ── FOOTER ── */
        footer { background: var(--black); padding: 50px 0 20px; border-top: 1px solid rgba(255,255,255,.06); position: relative; z-index: 1; }
        footer h6 { color:#ede5d3 !important; font-family:var(--fh); font-weight:600 !important; }
        footer p { color:#a89880; font-size:.8rem; }
        .flink { color:#a89880; font-size:.8rem; text-decoration:none; display:block; margin-bottom:6px; transition:all .2s; }
        .flink:hover { color:var(--gold); padding-left:5px; }
        .fbrand { font-family:var(--fh); font-size:1.4rem; color:#f2ece0 !important; letter-spacing:2px; font-weight:600; display:block; margin-bottom:8px; }

        /* ── DIVIDER ORNAMENT ── */
        .ornament-divider {
            text-align: center; margin: 0 auto 50px;
            display: flex; align-items: center; gap: 18px;
        }
        .ornament-divider::before, .ornament-divider::after {
            content: ''; flex: 1; height: 1px; background: var(--border2);
        }
        .ornament-divider span {
            font-family: var(--fh); font-size: 1.4rem;
            color: var(--gold); font-style: italic; letter-spacing: 2px;
        }

        /* ── SCROLL REVEAL ── */
        .rv { opacity: 0; transform: translateY(32px); transition: opacity 0.8s ease, transform 0.8s ease; }
        .rv.on { opacity: 1; transform: translateY(0); }
        .d1{transition-delay:.08s} .d2{transition-delay:.16s} .d3{transition-delay:.24s} .d4{transition-delay:.32s}
        .rv-left { opacity:0; transform:translateX(-40px); transition: opacity 0.8s ease, transform 0.8s ease; }
        .rv-left.on { opacity:1; transform:translateX(0); }
        .rv-right { opacity:0; transform:translateX(40px); transition: opacity 0.8s ease, transform 0.8s ease; }
        .rv-right.on { opacity:1; transform:translateX(0); }

        @media(max-width:991px) {
            .page-hero h1 { font-size: 2.8rem; }
            .mission-block { padding: 40px 30px; }
            .strip-divider { display: none; }
            .btn-outline-ve { margin-left: 0; margin-top: 12px; }
        }
        @media(max-width:767px) {
            .page-hero h1 { font-size: 2.2rem; }
            .vm-card { padding: 40px 28px; }
            .cta-section h2 { font-size: 2rem; }
            .hero-bg-letter { font-size: 12rem; }
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
                    <a href="index.php" class="nav-link-custom">Home</a>
                    <a href="exhibits.php" class="nav-link-custom">Explore</a>
                    <a href="gallery.php" class="nav-link-custom">Gallery</a>
                    <a href="about.php" class="nav-link-custom active">About</a>
                    <a href="faqs.php"      class="nav-link-custom">FAQs</a>
                    <a href="check_status.php" class="nav-link-custom">Track Status</a>
                    <a href="book.php" class="btn-book">Book Visit</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- ── HERO ── -->
    <header class="page-hero">
        <div class="hero-bg-letter">About</div>
        <div class="container position-relative rv">
            <div class="hero-eyebrow">Our Story</div>
            <h1>About <em>VisitEase</em></h1>
            <p class="lead">
                We believe every museum visit should begin with ease — long before you step through the door.
                Discover the story, purpose, and people behind your favourite reservation platform.
            </p>
        </div>
    </header>

    <!-- ── STATS STRIP ── -->
    <div class="intro-strip">
        <div class="container">
            <div class="intro-strip-inner">
                <div class="strip-stat rv d1">
                    <strong>5,000+</strong>
                    <span>Visits Booked</span>
                </div>
                <div class="strip-divider"></div>
                <div class="strip-stat rv d2">
                    <strong>100%</strong>
                    <span>Secure Transactions</span>
                </div>
                <div class="strip-divider"></div>
                <div class="strip-stat rv d3">
                    <strong>Real-Time</strong>
                    <span>Availability Updates</span>
                </div>
                <div class="strip-divider"></div>
                <div class="strip-stat rv d4">
                    <strong>24 / 7</strong>
                    <span>Online Access</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ── ABOUT MAIN ── -->
    <section class="about-section">
        <div class="container">
            <div class="row align-items-center g-5">

                <div class="col-lg-6 rv-left">
                    <div class="mission-block">
                        <div class="section-eyebrow">Who We Are</div>
                        <h2>Where Culture Meets <em>Convenience</em></h2>
                        <div class="gold-bar"></div>
                        <p>
                            VisitEase is a modern online museum reservation system designed to bridge the gap between cultural institutions and the visitors who love them. Our platform empowers guests to effortlessly browse museum information, explore upcoming exhibits, and check real-time schedule availability — all from the comfort of their own device.
                        </p>
                        <p>
                            We built VisitEase with one goal in mind: to remove the barriers between people and the stories that history has to tell. By digitizing the entire reservation process, we help museums operate more efficiently while giving visitors the seamless, stress-free experience they deserve.
                        </p>
                        <p>
                            Whether you are a first-time visitor or a lifelong museum enthusiast, VisitEase is your trusted companion for a smarter, more connected cultural experience.
                        </p>
                    </div>
                </div>

                <div class="col-lg-6 rv-right">
                    <!-- Decorative info panel -->
                    <div style="padding: 0 0 0 20px;">
                        <div class="ornament-divider"><span>est. 2024</span></div>

                        <div style="border-left: 2px solid var(--border2); padding-left: 28px; margin-bottom: 36px;">
                            <div class="section-eyebrow">Our Purpose</div>
                            <p style="color:var(--muted); font-size:0.9rem; line-height:1.9; font-weight:300;">
                                To make heritage more accessible, one reservation at a time. VisitEase was born from the belief that technology should enrich cultural experiences — not complicate them.
                            </p>
                        </div>

                        <div style="border-left: 2px solid var(--border2); padding-left: 28px; margin-bottom: 36px;">
                            <div class="section-eyebrow">Our Commitment</div>
                            <p style="color:var(--muted); font-size:0.9rem; line-height:1.9; font-weight:300;">
                                We are committed to continuous improvement — enhancing our platform with the latest digital innovations so that every interaction with VisitEase feels effortless, modern, and trustworthy.
                            </p>
                        </div>

                        <div style="border-left: 2px solid var(--border2); padding-left: 28px;">
                            <div class="section-eyebrow">Our Location</div>
                            <p style="color:var(--muted); font-size:0.9rem; line-height:1.9; font-weight:300;">
                                <i class="fas fa-map-marker-alt me-2" style="color:var(--gold)"></i>Barangay Ilijan, Batangas City<br>
                                <i class="fas fa-clock me-2" style="color:var(--gold)"></i>Monday – Saturday &nbsp;·&nbsp; 9:00 AM – 6:00 PM
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ── KEY FEATURES ── -->
    <section class="features-section">
        <div class="container">

            <div class="section-header rv">
                <div class="section-eyebrow" style="justify-content:center;">What We Offer</div>
                <h2>Key <em>Features</em></h2>
                <p>Everything you need for a seamless museum visit, thoughtfully designed and built for every kind of visitor.</p>
            </div>

            <div class="row g-4">

                <div class="col-md-6 col-lg-3 rv d1">
                    <div class="feature-card">
                        <div class="feature-num">01</div>
                        <div class="feature-icon"><i class="fas fa-ticket-alt"></i></div>
                        <h5>Online Ticket Booking</h5>
                        <p>Reserve your visit anytime, anywhere, in just a few clicks — no long queues, no wasted time.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3 rv d2">
                    <div class="feature-card">
                        <div class="feature-num">02</div>
                        <div class="feature-icon"><i class="fas fa-calendar-check"></i></div>
                        <h5>Real-Time Availability</h5>
                        <p>View up-to-date schedules and live capacity at a glance, so you can plan your visit with confidence.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3 rv d3">
                    <div class="feature-card">
                        <div class="feature-num">03</div>
                        <div class="feature-icon"><i class="fas fa-hand-pointer"></i></div>
                        <h5>User-Friendly Interface</h5>
                        <p>A clean, intuitive design built for visitors of all ages and tech backgrounds — simple and welcoming.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3 rv d4">
                    <div class="feature-card">
                        <div class="feature-num">04</div>
                        <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                        <h5>Secure Transactions</h5>
                        <p>Your personal information and booking details are always protected with the highest security standards.</p>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- ── VISION & MISSION ── -->
    <section class="vm-section">
        <div class="container">
            <div class="row g-4">

                <div class="col-lg-6 rv-left">
                    <div class="vm-card vision" data-letter="V">
                        <div class="section-eyebrow">Looking Forward</div>
                        <h3 style="color:#f2ece0; font-size:2.1rem;">Our <em style="color:var(--gold2);">Vision</em></h3>
                        <div style="width:45px;height:2px;background:var(--gold2);margin-bottom:22px;opacity:.6;"></div>
                        <p>To become the leading digital gateway for cultural and museum experiences in the Philippines — making world-class heritage accessible to every Filipino, regardless of location or circumstance.</p>
                        <p>We envision a future where the first step of every meaningful museum visit begins with a single, effortless tap.</p>
                    </div>
                </div>

                <div class="col-lg-6 rv-right">
                    <div class="vm-card mission-v" data-letter="M">
                        <div class="section-eyebrow">Driving Us Forward</div>
                        <h3 style="font-size:2.1rem;">Our <em style="color:var(--brown);">Mission</em></h3>
                        <div style="width:45px;height:2px;background:var(--gold);margin-bottom:22px;opacity:.5;"></div>
                        <p>To deliver an innovative, accessible, and efficient online reservation system that empowers visitors to connect with culture on their own terms.</p>
                        <p>We are dedicated to embracing digital innovation, improving visitor experience, and promoting the value of heritage preservation through technology.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ── CLOSING CTA ── -->
    <section class="cta-section">
        <div class="container position-relative rv">
            <div class="section-eyebrow" style="justify-content:center; color:var(--gold2);">
                <span style="width:30px;height:1px;background:var(--gold2);display:inline-block;"></span>
                Ready to Visit?
                <span style="width:30px;height:1px;background:var(--gold2);display:inline-block;"></span>
            </div>
            <h2>Start Your <em>Journey</em> Today</h2>
            <p>Experience history, culture, and art without the hassle. Book your museum visit online in minutes and step into a world of discovery.</p>
            <div class="d-flex flex-wrap justify-content-center">
                <a href="book.php" class="btn-primary-ve">
                    <i class="fas fa-ticket-alt"></i> Book a Visit
                </a>
                <a href="exhibits.php" class="btn-outline-ve">
                    <i class="fas fa-compass"></i> Explore Exhibits
                </a>
            </div>
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
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('on'); });
            }, { threshold: 0.08 });
            document.querySelectorAll('.rv, .rv-left, .rv-right').forEach(el => observer.observe(el));
        });
    </script>
</body>
</html>     