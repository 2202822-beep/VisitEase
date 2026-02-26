<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VisitEase | Gallery</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;0,700;1,300;1,400;1,600&family=DM+Sans:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        :root {
            --cream:   #f7f2ea;
            --cream2:  #efe8d8;
            --cream3:  #e8dfc8;
            --white:   #ffffff;
            --dark:    #231e14;
            --muted:   #6b6050;
            --light:   #a39078;
            --gold:    #b8842a;
            --gold2:   #d4a84b;
            --brown:   #7c5c2e;
            --terra:   #c4704a;
            --black:   #1e1a14;
            --border:  rgba(184,132,42,0.18);
            --border2: rgba(184,132,42,0.35);
            --shadow:  rgba(124,92,46,0.10);
            --fd: 'Playfair Display', serif;
            --fh: 'Cormorant Garamond', serif;
            --fb: 'DM Sans', sans-serif;
        }
        
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body { 
            font-family: var(--fb); 
            color: var(--dark); 
            background-color: var(--cream); 
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        /* Subtle texture overlay */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='4' height='4'%3E%3Crect width='4' height='4' fill='%23f7f2ea'/%3E%3Ccircle cx='1' cy='1' r='0.4' fill='%23d4a84b' opacity='0.08'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
        }

        h1, h2, h3, h4, h5 { font-family: var(--fd); color: var(--dark); font-weight: 700; }
        .text-gold { color: var(--gold) !important; }

        /* ── NAVBAR ── */
        .navbar { background: rgba(247,242,234,0.97); backdrop-filter: blur(20px); padding: 0.8rem 0; border-bottom: 1px solid var(--border); z-index: 1000; position: fixed; width: 100%; top: 0; }
        .navbar-brand { font-family: var(--fh); font-size: 1.6rem; letter-spacing: 3px; color: var(--brown) !important; font-weight: 600; }
        .nav-link-custom { color: var(--dark); font-weight: 500; font-size: 0.75rem; margin-left: 20px; text-decoration: none; transition: color .3s; text-transform: uppercase; letter-spacing: 2px; position: relative; }
        .nav-link-custom::after { content:''; position:absolute; bottom:-4px; left:0; width:0; height:1px; background:var(--gold); transition:width .3s; }
        .nav-link-custom:hover::after, .nav-link-custom.active::after { width:100%; }
        .nav-link-custom:hover, .nav-link-custom.active { color: var(--gold) !important; }
        .btn-book { background:var(--brown); color:#fff !important; padding:8px 20px; border-radius:2px; font-size:0.75rem; font-weight:500; text-transform:uppercase; letter-spacing:2px; border:1px solid var(--brown); text-decoration:none; transition:all .3s; margin-left: 20px;}
        .btn-book:hover { background:transparent; color:var(--brown) !important; }

        /* ── PAGE HEADER ── */
        .page-header { 
            padding: 160px 0 80px; 
            text-align: center; 
            background: linear-gradient(165deg, var(--cream2) 0%, var(--cream) 60%, var(--cream3) 100%);
            border-bottom: 1px solid var(--border);
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: -80px; left: 50%;
            transform: translateX(-50%);
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(184,132,42,0.07) 0%, transparent 70%);
            pointer-events: none;
        }

        /* Decorative corner ornament */
        .header-ornament {
            position: absolute;
            font-family: var(--fh);
            font-size: 12rem;
            color: rgba(184,132,42,0.04);
            font-style: italic;
            font-weight: 700;
            line-height: 1;
            pointer-events: none;
            user-select: none;
        }
        .header-ornament.left { left: -20px; top: 60px; }
        .header-ornament.right { right: -20px; bottom: 20px; }

        .header-subtitle {
            display:inline-flex; align-items:center; gap:10px; font-family:var(--fb); font-size:.62rem; font-weight:600; text-transform:uppercase; letter-spacing:4px; color:var(--terra) !important; margin-bottom:18px;
        }
        .header-subtitle::before, .header-subtitle::after {
            content:''; width:30px; height:1px; background:var(--terra);
        }
        .page-header h1 { font-size: 4rem; margin-bottom: 15px; line-height: 1.15; letter-spacing: -0.5px; }
        .page-header h1 em { color: var(--brown); font-style: italic; }
        .page-header p {
            color: var(--muted) !important; 
            font-size: 0.92rem;
            line-height: 1.9;
            font-weight: 300;
        }

        /* ── GALLERY COUNT STRIP ── */
        .gallery-strip {
            background: var(--dark);
            padding: 14px 0;
            text-align: center;
        }
        .gallery-strip span {
            font-family: var(--fh);
            font-size: 0.85rem;
            color: rgba(242,236,224,0.55);
            letter-spacing: 3px;
            text-transform: uppercase;
        }
        .gallery-strip strong {
            color: var(--gold2);
            font-family: var(--fh);
        }

        /* ── GALLERY SECTION ── */
        .gallery-section {
            padding: 70px 0 90px;
            position: relative;
        }

        /* Section divider text */
        .gallery-label {
            font-family: var(--fh);
            font-size: 0.7rem;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: var(--light);
            margin-bottom: 35px;
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .gallery-label::after { content:''; flex:1; height:1px; background: var(--border2); }

        /* ── MASONRY-STYLE LAYOUT ── */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            grid-template-rows: auto;
            gap: 18px;
        }

        /* Card slot assignments — museum editorial layout */
        .g-slot-1  { grid-column: 1 / 6;  grid-row: 1; }   /* wide */
        .g-slot-2  { grid-column: 6 / 10; grid-row: 1; }   /* medium */
        .g-slot-3  { grid-column: 10 / 13; grid-row: 1; }  /* narrow */
        .g-slot-4  { grid-column: 1 / 4;  grid-row: 2; }   /* narrow */
        .g-slot-5  { grid-column: 4 / 8;  grid-row: 2; }   /* medium */
        .g-slot-6  { grid-column: 8 / 13; grid-row: 2; }   /* wide */
        .g-slot-7  { grid-column: 1 / 5;  grid-row: 3; }   /* medium */
        .g-slot-8  { grid-column: 5 / 9;  grid-row: 3; }   /* medium */
        .g-slot-9  { grid-column: 9 / 13; grid-row: 3; }   /* medium */
        .g-slot-10 { grid-column: 1 / 4;  grid-row: 4; }   /* narrow */
        .g-slot-11 { grid-column: 4 / 8;  grid-row: 4; }   /* medium */
        .g-slot-12 { grid-column: 8 / 11; grid-row: 4; }   /* medium */
        .g-slot-13 { grid-column: 11 / 13; grid-row: 4; }  /* small */

        /* ── GALLERY ITEM ── */
        .gallery-item { 
            position: relative; 
            overflow: hidden; 
            background: var(--dark); 
            cursor: pointer; 
            border-radius: 3px;
            border: 1px solid var(--border);
            box-shadow: 0 6px 20px var(--shadow); 
            transition: all 0.55s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        
        .gallery-item:hover { 
            transform: translateY(-6px) scale(1.005); 
            box-shadow: 0 20px 50px rgba(124,92,46,0.22); 
            border-color: var(--gold2);
            z-index: 10;
        }
        
        .gallery-item img { 
            width: 100%; 
            height: 100%; 
            object-fit: cover; 
            display: block;
            transition: transform 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94), filter 0.5s ease; 
            min-height: 220px;
        }

        /* Tall slots */
        .g-slot-1 img, .g-slot-6 img { min-height: 320px; }

        /* Overlay gradient */
        .gallery-item::after {
            content: ''; 
            position: absolute; inset: 0;
            background: linear-gradient(
                to top, 
                rgba(18,14,8,0.92) 0%, 
                rgba(18,14,8,0.3) 40%, 
                rgba(18,14,8,0.05) 70%,
                transparent 100%
            );
            opacity: 0; 
            transition: opacity 0.45s ease; 
            pointer-events: none;
        }
        
        .gallery-item:hover img { transform: scale(1.08); filter: brightness(0.85); }
        .gallery-item:hover::after { opacity: 1; }

        /* Number badge */
        .gallery-num {
            position: absolute;
            top: 12px; left: 14px;
            font-family: var(--fh);
            font-size: 0.65rem;
            font-weight: 600;
            color: rgba(255,255,255,0.9);
            letter-spacing: 2px;
            background: rgba(184,132,42,0.85);
            padding: 3px 10px;
            border-radius: 1px;
            z-index: 3;
            opacity: 0;
            transform: translateY(-6px);
            transition: all 0.35s ease;
            backdrop-filter: blur(4px);
        }
        .gallery-item:hover .gallery-num { opacity: 1; transform: translateY(0); }
        
        /* Zoom icon */
        .gallery-zoom {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%) scale(0.5);
            width: 52px; height: 52px;
            border: 1.5px solid rgba(255,255,255,0.7);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            z-index: 4;
            pointer-events: none;
        }
        .gallery-zoom i { color: #fff; font-size: 1rem; }
        .gallery-item:hover .gallery-zoom { opacity: 1; transform: translate(-50%, -50%) scale(1); }

        /* Caption */
        .gallery-caption { 
            position: absolute; 
            bottom: 0; left: 0; width: 100%;
            padding: 20px 18px 16px;
            opacity: 0; 
            transition: all 0.45s ease; 
            z-index: 3;
            transform: translateY(10px);
            background: linear-gradient(to top, rgba(18,14,8,0.7) 0%, transparent 100%);
        }
        .gallery-caption h5 { 
            font-family: var(--fh);
            font-size: 1.1rem; 
            color: #f2ece0;
            letter-spacing: 0.5px; 
            margin: 0 0 5px; 
            font-style: italic;
            font-weight: 600;
            line-height: 1.3;
        }
        .gallery-caption p {
            font-size: 0.6rem;
            color: rgba(242,236,224,0.6);
            text-transform: uppercase;
            letter-spacing: 2.5px;
            margin: 0;
        }
        .gold-line {
            width: 28px; height: 1.5px; background: var(--gold2); margin-bottom: 8px;
            transition: width 0.4s ease;
        }

        .gallery-item:hover .gallery-caption { opacity: 1; transform: translateY(0); }
        .gallery-item:hover .gold-line { width: 55px; }

        /* ── LIGHTBOX ── */
        .lightbox-overlay {
            position: fixed;
            inset: 0;
            background: rgba(14,11,7,0.97);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.45s ease;
            backdrop-filter: blur(10px);
        }
        .lightbox-overlay.active {
            opacity: 1;
            pointer-events: all;
        }

        /* Decorative frame corners */
        .lb-frame {
            position: relative;
            display: inline-block;
        }
        .lb-frame::before, .lb-frame::after,
        .lb-frame-br::before, .lb-frame-br::after {
            content: '';
            position: absolute;
            width: 30px; height: 30px;
            border-color: var(--gold2);
            border-style: solid;
            opacity: 0.6;
        }
        .lb-frame::before { top: -10px; left: -10px; border-width: 2px 0 0 2px; }
        .lb-frame::after  { top: -10px; right: -10px; border-width: 2px 2px 0 0; }
        .lb-frame-br::before { bottom: -10px; left: -10px; border-width: 0 0 2px 2px; }
        .lb-frame-br::after  { bottom: -10px; right: -10px; border-width: 0 2px 2px 0; }

        .lightbox-inner {
            position: relative;
            max-width: 88vw;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 24px;
            transform: scale(0.88);
            transition: transform 0.45s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .lightbox-overlay.active .lightbox-inner {
            transform: scale(1);
        }

        #lb-image {
            max-width: 100%;
            max-height: 75vh;
            object-fit: contain;
            display: block;
            border: 1px solid rgba(184,132,42,0.25);
            border-radius: 3px;
            box-shadow: 0 30px 80px rgba(0,0,0,0.7), 0 0 0 1px rgba(184,132,42,0.08);
        }

        .lb-info {
            text-align: center;
        }
        .lb-info h3 {
            font-family: var(--fh);
            font-size: 1.8rem;
            color: #f2ece0;
            font-style: italic;
            font-weight: 600;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }
        .lb-info p {
            font-size: 0.62rem;
            color: rgba(242,236,224,0.4);
            text-transform: uppercase;
            letter-spacing: 3px;
            margin: 0;
        }
        .lb-divider {
            width: 50px; height: 1px; background: var(--gold2); 
            margin: 8px auto;
            opacity: 0.6;
        }

        /* Nav arrows */
        .lb-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(184,132,42,0.12);
            border: 1px solid rgba(184,132,42,0.3);
            color: #f2ece0;
            width: 48px; height: 48px;
            border-radius: 2px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1rem;
        }
        .lb-nav:hover { background: var(--gold); border-color: var(--gold); color: #fff; }
        .lb-prev { left: -70px; }
        .lb-next { right: -70px; }

        /* Counter */
        .lb-counter {
            position: absolute;
            bottom: -40px;
            left: 50%;
            transform: translateX(-50%);
            font-family: var(--fh);
            font-size: 0.7rem;
            color: rgba(242,236,224,0.3);
            letter-spacing: 3px;
            white-space: nowrap;
        }

        /* Close button */
        .lb-close {
            position: fixed;
            top: 28px; right: 35px;
            background: none;
            border: 1px solid rgba(242,236,224,0.2);
            color: rgba(242,236,224,0.7);
            width: 42px; height: 42px;
            border-radius: 2px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1rem;
            z-index: 10001;
        }
        .lb-close:hover { background: var(--terra); border-color: var(--terra); color: #fff; }

        /* Thumbnail strip */
        .lb-thumbs {
            display: flex;
            gap: 6px;
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            padding: 8px 12px;
            background: rgba(18,14,8,0.7);
            border: 1px solid rgba(184,132,42,0.15);
            border-radius: 3px;
            backdrop-filter: blur(10px);
            z-index: 10001;
        }
        .lb-thumb {
            width: 38px; height: 28px;
            object-fit: cover;
            border-radius: 1px;
            border: 1.5px solid transparent;
            cursor: pointer;
            transition: all 0.3s ease;
            opacity: 0.45;
        }
        .lb-thumb:hover { opacity: 0.8; }
        .lb-thumb.active { border-color: var(--gold2); opacity: 1; }

        /* ── FOOTER ── */
        footer { background:var(--black); padding:50px 0 20px; border-top:1px solid rgba(255,255,255,.06); margin-top: 40px; position: relative; z-index: 1;}
        footer h6 { color:#ede5d3 !important; font-family:var(--fh); font-weight:600 !important; }
        footer p { color:#a89880; font-size:.8rem; }
        .flink { color:#a89880; font-size:.8rem; text-decoration:none; display:block; margin-bottom:6px; transition:all .2s; }
        .flink:hover { color:var(--gold); padding-left:5px; }
        .fbrand { font-family:var(--fh); font-size:1.4rem; color:#f2ece0 !important; letter-spacing:2px; font-weight:600; display:block; margin-bottom:8px; }

        /* ── ANIMATIONS ── */
        .rv { opacity: 0; transform: translateY(28px); transition: opacity 0.75s ease, transform 0.75s ease; }
        .rv.on { opacity: 1; transform: translateY(0); }
        .d1 { transition-delay: 0.05s; }
        .d2 { transition-delay: 0.12s; }
        .d3 { transition-delay: 0.19s; }
        .d4 { transition-delay: 0.26s; }
        .d5 { transition-delay: 0.33s; }
        .d6 { transition-delay: 0.40s; }
        .d7 { transition-delay: 0.47s; }

        /* ── RESPONSIVE ── */
        @media (max-width: 1199px) {
            .gallery-grid {
                grid-template-columns: repeat(6, 1fr);
                grid-template-rows: auto;
                gap: 14px;
            }
            .g-slot-1  { grid-column: 1 / 4; grid-row: 1; }
            .g-slot-2  { grid-column: 4 / 7; grid-row: 1; }
            .g-slot-3  { grid-column: 1 / 3; grid-row: 2; }
            .g-slot-4  { grid-column: 3 / 5; grid-row: 2; }
            .g-slot-5  { grid-column: 5 / 7; grid-row: 2; }
            .g-slot-6  { grid-column: 1 / 4; grid-row: 3; }
            .g-slot-7  { grid-column: 4 / 7; grid-row: 3; }
            .g-slot-8  { grid-column: 1 / 3; grid-row: 4; }
            .g-slot-9  { grid-column: 3 / 5; grid-row: 4; }
            .g-slot-10 { grid-column: 5 / 7; grid-row: 4; }
            .g-slot-11 { grid-column: 1 / 3; grid-row: 5; }
            .g-slot-12 { grid-column: 3 / 5; grid-row: 5; }
            .g-slot-13 { grid-column: 5 / 7; grid-row: 5; }
            .lb-prev { left: -55px; }
            .lb-next { right: -55px; }
        }

        @media (max-width: 767px) {
            .gallery-grid {
                grid-template-columns: 1fr 1fr;
                gap: 12px;
            }
            .g-slot-1, .g-slot-2, .g-slot-3, .g-slot-4, .g-slot-5, .g-slot-6,
            .g-slot-7, .g-slot-8, .g-slot-9, .g-slot-10, .g-slot-11, .g-slot-12, .g-slot-13 {
                grid-column: span 1 !important;
                grid-row: auto !important;
            }
            .g-slot-1 { grid-column: 1 / -1 !important; }
            .page-header h1 { font-size: 2.5rem; }
            .lb-prev, .lb-next { display: none; }
            .lb-thumbs { max-width: 90vw; flex-wrap: wrap; justify-content: center; }
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid px-4 px-lg-5">
            <a class="navbar-brand" href="index.php">VISITEASE</a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
                <i class="fas fa-bars" style="color:var(--brown)"></i>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="nav">
                <div class="navbar-nav align-items-center">
                    <a href="index.php" class="nav-link-custom">Home</a>
                    <a href="exhibits.php" class="nav-link-custom">Explore</a>
                    <a href="gallery.php" class="nav-link-custom active">Gallery</a>
                    <a href="check_status.php" class="nav-link-custom">Track Status</a>
                    <a href="book.php" class="btn-book">Book Visit</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- HEADER -->
    <header class="page-header rv">
        <span class="header-ornament left">G</span>
        <span class="header-ornament right">A</span>
        <div class="container position-relative">
            <div class="header-subtitle">Visual Collection</div>
            <h1>Museum <em>Gallery</em></h1>
            <p class="mx-auto" style="max-width: 580px;">
                Step back in time and explore the carefully preserved artifacts, photographs, 
                and historical documents that define our rich cultural heritage.
            </p>
        </div>
    </header>

    <!-- COUNT STRIP -->
    <div class="gallery-strip">
        <span>Displaying <strong>13</strong> &nbsp;·&nbsp; Curated Works &nbsp;·&nbsp; Museum Collection</span>
    </div>

    <!-- GALLERY SECTION -->
    <section class="gallery-section">
        <div class="container-fluid px-4 px-lg-5">

            <div class="gallery-label rv">
                Permanent Exhibition
            </div>

            <div class="gallery-grid">

                <!-- Item 1 -->
                <div class="gallery-item g-slot-1 rv d1"
                     data-img="494813889_1218375400006712_3528886370220596685_n.jpg"
                     data-title="Ancient Ceramics"
                     data-sub="Permanent Collection">
                    <img src="494813889_1218375400006712_3528886370220596685_n.jpg" alt="Ceramics">
                    <div class="gallery-num">NO. 01</div>
                    <div class="gallery-zoom"><i class="fas fa-expand-alt"></i></div>
                    <div class="gallery-caption">
                        <div class="gold-line"></div>
                        <h5>Ancient Ceramics</h5>
                        <p>Permanent Collection</p>
                    </div>
                </div>

                <!-- Item 2 -->
                <div class="gallery-item g-slot-2 rv d2"
                     data-img="494826199_1763960804490430_7579041577827863987_n.jpg"
                     data-title="Royal Armory"
                     data-sub="Heritage Wing">
                    <img src="494826199_1763960804490430_7579041577827863987_n.jpg" alt="Royal Armory">
                    <div class="gallery-num">NO. 02</div>
                    <div class="gallery-zoom"><i class="fas fa-expand-alt"></i></div>
                    <div class="gallery-caption">
                        <div class="gold-line"></div>
                        <h5>Royal Armory</h5>
                        <p>Heritage Wing</p>
                    </div>
                </div>

                <!-- Item 3 -->
                <div class="gallery-item g-slot-3 rv d3"
                     data-img="494359485_2155062051631886_4951905305416589935_n.jpg"
                     data-title="Abstract Forms"
                     data-sub="Modern Art">
                    <img src="494359485_2155062051631886_4951905305416589935_n.jpg" alt="Abstract Art">
                    <div class="gallery-num">NO. 03</div>
                    <div class="gallery-zoom"><i class="fas fa-expand-alt"></i></div>
                    <div class="gallery-caption">
                        <div class="gold-line"></div>
                        <h5>Abstract Forms</h5>
                        <p>Modern Art</p>
                    </div>
                </div>

                <!-- Item 4 -->
                <div class="gallery-item g-slot-4 rv d1"
                     data-img="494823189_472537129256301_8369976507955445871_n.jpg"
                     data-title="Stone Sculpture"
                     data-sub="Classical Period">
                    <img src="494823189_472537129256301_8369976507955445871_n.jpg" alt="Sculpture">
                    <div class="gallery-num">NO. 04</div>
                    <div class="gallery-zoom"><i class="fas fa-expand-alt"></i></div>
                    <div class="gallery-caption">
                        <div class="gold-line"></div>
                        <h5>Stone Sculpture</h5>
                        <p>Classical Period</p>
                    </div>
                </div>

                <!-- Item 5 -->
                <div class="gallery-item g-slot-5 rv d2"
                     data-img="494825054_1641213353259511_8978938757165686738_n.jpg"
                     data-title="Sacred Scriptures"
                     data-sub="Religious Archives">
                    <img src="494825054_1641213353259511_8978938757165686738_n.jpg" alt="Scriptures">
                    <div class="gallery-num">NO. 05</div>
                    <div class="gallery-zoom"><i class="fas fa-expand-alt"></i></div>
                    <div class="gallery-caption">
                        <div class="gold-line"></div>
                        <h5>Sacred Scriptures</h5>
                        <p>Religious Archives</p>
                    </div>
                </div>

                <!-- Item 6 -->
                <div class="gallery-item g-slot-6 rv d3"
                     data-img="494358990_1047988036664015_4979249687492318735_n.jpg"
                     data-title="Historical Maps"
                     data-sub="Cartography Collection">
                    <img src="494358990_1047988036664015_4979249687492318735_n.jpg" alt="Maps">
                    <div class="gallery-num">NO. 06</div>
                    <div class="gallery-zoom"><i class="fas fa-expand-alt"></i></div>
                    <div class="gallery-caption">
                        <div class="gold-line"></div>
                        <h5>Historical Maps</h5>
                        <p>Cartography Collection</p>
                    </div>
                </div>

                <!-- Item 7 — New (reuse images with different captions for placeholders) -->
                <div class="gallery-item g-slot-7 rv d4"
                     data-img="494813889_1218375400006712_3528886370220596685_n.jpg"
                     data-title="Earthen Vessels"
                     data-sub="Archaeology Wing">
                    <img src="494813889_1218375400006712_3528886370220596685_n.jpg" alt="Earthen Vessels">
                    <div class="gallery-num">NO. 07</div>
                    <div class="gallery-zoom"><i class="fas fa-expand-alt"></i></div>
                    <div class="gallery-caption">
                        <div class="gold-line"></div>
                        <h5>Earthen Vessels</h5>
                        <p>Archaeology Wing</p>
                    </div>
                </div>

                <!-- Item 8 -->
                <div class="gallery-item g-slot-8 rv d5"
                     data-img="494826199_1763960804490430_7579041577827863987_n.jpg"
                     data-title="Warrior Relics"
                     data-sub="Military History">
                    <img src="494826199_1763960804490430_7579041577827863987_n.jpg" alt="Warrior Relics">
                    <div class="gallery-num">NO. 08</div>
                    <div class="gallery-zoom"><i class="fas fa-expand-alt"></i></div>
                    <div class="gallery-caption">
                        <div class="gold-line"></div>
                        <h5>Warrior Relics</h5>
                        <p>Military History</p>
                    </div>
                </div>

                <!-- Item 9 -->
                <div class="gallery-item g-slot-9 rv d6"
                     data-img="494359485_2155062051631886_4951905305416589935_n.jpg"
                     data-title="Pigment Studies"
                     data-sub="Fine Arts">
                    <img src="494359485_2155062051631886_4951905305416589935_n.jpg" alt="Pigment Studies">
                    <div class="gallery-num">NO. 09</div>
                    <div class="gallery-zoom"><i class="fas fa-expand-alt"></i></div>
                    <div class="gallery-caption">
                        <div class="gold-line"></div>
                        <h5>Pigment Studies</h5>
                        <p>Fine Arts</p>
                    </div>
                </div>

                <!-- Item 10 -->
                <div class="gallery-item g-slot-10 rv d1"
                     data-img="494823189_472537129256301_8369976507955445871_n.jpg"
                     data-title="Carved Reliefs"
                     data-sub="Sculptural Works">
                    <img src="494823189_472537129256301_8369976507955445871_n.jpg" alt="Carved Reliefs">
                    <div class="gallery-num">NO. 10</div>
                    <div class="gallery-zoom"><i class="fas fa-expand-alt"></i></div>
                    <div class="gallery-caption">
                        <div class="gold-line"></div>
                        <h5>Carved Reliefs</h5>
                        <p>Sculptural Works</p>
                    </div>
                </div>

                <!-- Item 11 -->
                <div class="gallery-item g-slot-11 rv d2"
                     data-img="494825054_1641213353259511_8978938757165686738_n.jpg"
                     data-title="Illuminated Texts"
                     data-sub="Manuscript Room">
                    <img src="494825054_1641213353259511_8978938757165686738_n.jpg" alt="Illuminated Texts">
                    <div class="gallery-num">NO. 11</div>
                    <div class="gallery-zoom"><i class="fas fa-expand-alt"></i></div>
                    <div class="gallery-caption">
                        <div class="gold-line"></div>
                        <h5>Illuminated Texts</h5>
                        <p>Manuscript Room</p>
                    </div>
                </div>

                <!-- Item 12 -->
                <div class="gallery-item g-slot-12 rv d3"
                     data-img="494358990_1047988036664015_4979249687492318735_n.jpg"
                     data-title="Trade Routes"
                     data-sub="Maritime History">
                    <img src="494358990_1047988036664015_4979249687492318735_n.jpg" alt="Trade Routes">
                    <div class="gallery-num">NO. 12</div>
                    <div class="gallery-zoom"><i class="fas fa-expand-alt"></i></div>
                    <div class="gallery-caption">
                        <div class="gold-line"></div>
                        <h5>Trade Routes</h5>
                        <p>Maritime History</p>
                    </div>
                </div>

                <!-- Item 13 -->
                <div class="gallery-item g-slot-13 rv d4"
                     data-img="494813889_1218375400006712_3528886370220596685_n.jpg"
                     data-title="Clay Artifacts"
                     data-sub="Pre-Colonial Era">
                    <img src="494813889_1218375400006712_3528886370220596685_n.jpg" alt="Clay Artifacts">
                    <div class="gallery-num">NO. 13</div>
                    <div class="gallery-zoom"><i class="fas fa-expand-alt"></i></div>
                    <div class="gallery-caption">
                        <div class="gold-line"></div>
                        <h5>Clay Artifacts</h5>
                        <p>Pre-Colonial Era</p>
                    </div>
                </div>

            </div><!-- /gallery-grid -->
        </div><!-- /container -->
    </section>

    <!-- FOOTER -->
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

    <!-- ── CUSTOM LIGHTBOX ── -->
    <div class="lightbox-overlay" id="lightbox" role="dialog" aria-modal="true">
        <button class="lb-close" id="lb-close" aria-label="Close"><i class="fas fa-times"></i></button>
        
        <div class="lightbox-inner">
            <div class="lb-frame lb-frame-br">
                <img id="lb-image" src="" alt="Gallery Image">
            </div>
            <div class="lb-info">
                <h3 id="lb-title"></h3>
                <div class="lb-divider"></div>
                <p id="lb-sub"></p>
            </div>
            <button class="lb-nav lb-prev" id="lb-prev" aria-label="Previous"><i class="fas fa-chevron-left"></i></button>
            <button class="lb-nav lb-next" id="lb-next" aria-label="Next"><i class="fas fa-chevron-right"></i></button>
            <div class="lb-counter" id="lb-counter"></div>
        </div>

        <!-- Thumbnail strip -->
        <div class="lb-thumbs" id="lb-thumbs"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    document.addEventListener("DOMContentLoaded", function () {

        // ── Scroll Reveal ──
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) entry.target.classList.add('on');
            });
        }, { threshold: 0.06 });
        document.querySelectorAll('.rv').forEach(el => observer.observe(el));

        // ── Lightbox ──
        const items = Array.from(document.querySelectorAll('.gallery-item'));
        const lightbox = document.getElementById('lightbox');
        const lbImage  = document.getElementById('lb-image');
        const lbTitle  = document.getElementById('lb-title');
        const lbSub    = document.getElementById('lb-sub');
        const lbCounter= document.getElementById('lb-counter');
        const lbThumbsEl= document.getElementById('lb-thumbs');
        let current = 0;

        // Build thumbnail strip
        items.forEach((item, i) => {
            const src = item.dataset.img;
            const thumb = document.createElement('img');
            thumb.className = 'lb-thumb';
            thumb.src = src;
            thumb.alt = item.dataset.title || '';
            thumb.addEventListener('click', () => openAt(i));
            lbThumbsEl.appendChild(thumb);
        });

        function updateThumbs() {
            document.querySelectorAll('.lb-thumb').forEach((t, i) => {
                t.classList.toggle('active', i === current);
            });
        }

        function openAt(index) {
            current = index;
            const item = items[index];
            lbImage.src = item.dataset.img;
            lbTitle.textContent = item.dataset.title || '';
            lbSub.textContent   = item.dataset.sub   || '';
            lbCounter.textContent = `${index + 1} / ${items.length}`;
            lightbox.classList.add('active');
            document.body.style.overflow = 'hidden';
            updateThumbs();
        }

        function close() {
            lightbox.classList.remove('active');
            document.body.style.overflow = '';
            setTimeout(() => { lbImage.src = ''; }, 450);
        }

        function navigate(dir) {
            current = (current + dir + items.length) % items.length;
            lbImage.style.opacity = '0';
            lbImage.style.transform = `translateX(${dir > 0 ? '-40px' : '40px'})`;
            setTimeout(() => {
                openAt(current);
                lbImage.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                lbImage.style.opacity = '1';
                lbImage.style.transform = 'translateX(0)';
            }, 180);
        }

        // Open on click
        items.forEach((item, i) => item.addEventListener('click', () => openAt(i)));

        // Close
        document.getElementById('lb-close').addEventListener('click', close);
        lightbox.addEventListener('click', function(e) {
            if (e.target === lightbox) close();
        });

        // Arrow nav
        document.getElementById('lb-prev').addEventListener('click', () => navigate(-1));
        document.getElementById('lb-next').addEventListener('click', () => navigate(1));

        // Keyboard
        document.addEventListener('keydown', function(e) {
            if (!lightbox.classList.contains('active')) return;
            if (e.key === 'Escape')       close();
            if (e.key === 'ArrowRight')   navigate(1);
            if (e.key === 'ArrowLeft')    navigate(-1);
        });

        // Touch/swipe
        let touchStartX = 0;
        lightbox.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; });
        lightbox.addEventListener('touchend', e => {
            const diff = touchStartX - e.changedTouches[0].clientX;
            if (Math.abs(diff) > 50) navigate(diff > 0 ? 1 : -1);
        });
    });
    </script>
</body>
</html>
