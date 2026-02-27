<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>visitEase | Home</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=DM+Sans:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --bg-body: #f7f2ea;
            --bg-secondary: #efe8d8;
            --bg-card: #ffffff;
            --text-main: #231e14;
            --text-muted: #4a4235;
            --text-light: #7c6851;
            --accent-gold: #b8842a;
            --accent-brown: #7c5c2e;
            --accent-terracotta: #c4704a;
            --accent-sage: #6b7c5c;
            --accent-dark: #1e1a14;
            --border-soft: rgba(184, 132, 42, 0.18);
            --shadow-warm: rgba(124, 92, 46, 0.1);
            --font-heading: 'Cormorant Garamond', serif;
            --font-display: 'Playfair Display', serif;
            --font-body: 'DM Sans', sans-serif;
        }

        * { box-sizing: border-box; }
        body { font-family: var(--font-body); color: var(--text-main); overflow-x: hidden; background-color: var(--bg-body); }
        h1, h2, h3, h4, h5, h6 { font-family: var(--font-heading); color: var(--text-main) !important; font-weight: 600; }
        .text-muted { color: var(--text-muted) !important; }

        /* ===== NAVBAR ===== */
        .navbar { background: rgba(247,242,234,0.95); backdrop-filter: blur(20px); padding: 1.2rem 0; border-bottom: 1px solid var(--border-soft); z-index: 1000; }
        .navbar-brand { font-family: var(--font-heading); font-size: 1.85rem; letter-spacing: 4px; color: var(--accent-brown) !important; font-weight: 600; }
        .nav-link-custom { color: var(--text-main); font-weight: 500; font-size: 0.78rem; margin-left: 30px; text-decoration: none; transition: 0.3s; text-transform: uppercase; letter-spacing: 2.5px; position: relative; }
        .nav-link-custom::after { content: ''; position: absolute; width: 0; height: 1px; bottom: -4px; left: 0; background-color: var(--accent-gold); transition: width 0.3s; }
        .nav-link-custom:hover::after, .nav-link-custom.active::after { width: 100%; }
        .nav-link-custom:hover, .nav-link-custom.active { color: var(--accent-gold) !important; }
        .btn-book { background-color: var(--accent-brown); color: #fff !important; padding: 10px 26px; border-radius: 2px; text-decoration: none; font-weight: 500; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 2px; transition: all 0.3s; border: 1px solid var(--accent-brown); display: inline-block; }
        .btn-book:hover { background-color: transparent; color: var(--accent-brown) !important; box-shadow: 0 4px 14px var(--shadow-warm); }

        /* ===== HERO SLIDER ===== */
        .cinematic-slider { position: relative; width: 100%; height: 92vh; margin-top: 74px; overflow: hidden; display: flex; align-items: center; }
        .bg-layer-container { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; }
        .bg-layer { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-size: cover; background-position: center; animation: slowZoom 20s infinite alternate; }
        @keyframes slowZoom { from { transform: scale(1); } to { transform: scale(1.05); } }
        #bgBack { z-index: 1; }
        #bgFront { z-index: 2; opacity: 0; filter: blur(10px); transition: opacity 0.8s ease-in-out, filter 0.8s ease-out; }
        #bgFront.active-fade { opacity: 1; filter: blur(0px); }

        .slider-overlay {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(108deg, rgba(247,242,234,0.85) 0%, rgba(247,242,234,0.65) 25%, rgba(247,242,234,0.20) 50%, rgba(247,242,234,0.00) 100%);
            z-index: 3;
        }
        .slider-overlay-bottom { position: absolute; bottom: 0; left: 0; width: 100%; height: 130px; background: linear-gradient(to top, var(--bg-body), transparent); z-index: 4; }

        .slider-content { position: relative; z-index: 5; padding-left: 6vw; max-width: 670px; }
        .slide-eyebrow { color: var(--accent-terracotta) !important; text-transform: uppercase; letter-spacing: 4px; font-size: 0.72rem; font-weight: 700; margin-bottom: 16px; display: flex; align-items: center; gap: 10px; font-family: var(--font-body); }
        .slide-eyebrow::before { content: ''; display: inline-block; width: 28px; height: 1px; background: var(--accent-terracotta); }
        .slide-title { font-size: 4.2rem; line-height: 1.05; font-family: var(--font-display); margin-bottom: 22px; font-weight: 700; color: var(--text-main) !important; }
        .slide-desc { font-size: 1.05rem; color: var(--text-muted) !important; line-height: 1.75; margin-bottom: 36px; font-weight: 500; max-width: 88%; font-family: var(--font-body); border-left: 3px solid var(--accent-gold); padding: 14px 18px; border-radius: 0 4px 4px 0; background: rgba(247,242,234,0.85); backdrop-filter: blur(6px); }
        .btn-explore { background-color: var(--accent-brown); color: #fff !important; padding: 13px 34px; border-radius: 2px; text-decoration: none; font-weight: 600; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 2px; transition: all 0.3s; border: 1px solid var(--accent-brown); display: inline-flex; align-items: center; gap: 10px; }
        .btn-explore:hover { background-color: transparent; color: var(--accent-brown) !important; transform: translateY(-2px); box-shadow: 0 8px 20px var(--shadow-warm); }

        /* ===== THUMBNAILS ===== */
        .thumbnail-container { position: absolute; bottom: 55px; right: 0; z-index: 10; display: flex; gap: 10px; overflow-x: auto; padding: 0 40px 0 20px; max-width: 50%; }
        .thumbnail-container::-webkit-scrollbar { display: none; }
        .thumb-card { min-width: 148px; height: 200px; border-radius: 3px; overflow: hidden; position: relative; cursor: pointer; transition: all 0.35s; border: 2px solid rgba(255,255,255,0.4); flex-shrink: 0; background: #1a1208; box-shadow: 0 6px 24px rgba(0,0,0,0.35); }
        .thumb-card img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s, filter 0.3s; filter: brightness(0.65) saturate(0.85); }
        .thumb-card:hover { transform: translateY(-8px); box-shadow: 0 16px 32px rgba(0,0,0,0.35); border-color: rgba(255,255,255,0.85); }
        .thumb-card:hover img { transform: scale(1.08); filter: brightness(0.88) saturate(1.1); }
        .thumb-info { position: absolute; bottom: 0; left: 0; width: 100%; background: linear-gradient(to top, rgba(0,0,0,0.88) 0%, transparent 100%); padding: 22px 12px 12px; }
        .thumb-title { font-size: 0.78rem; font-weight: 600; display: block; font-family: var(--font-body); color: #fff; }
        .thumb-loc { font-size: 0.65rem; color: rgba(255,255,255,0.55); text-transform: uppercase; letter-spacing: 1px; font-weight: 500;}
        .thumb-card.active { border: 2px solid var(--accent-gold); box-shadow: 0 6px 20px rgba(184,132,42,0.4); }
        .thumb-card.active img { filter: brightness(0.85) saturate(1.1); }

        /* ===== ANNOUNCEMENT RIBBON ===== */
        .announcement-ribbon { background: var(--accent-dark); padding: 13px 0; overflow: hidden; border-top: 1px solid rgba(184,132,42,0.25); border-bottom: 1px solid rgba(184,132,42,0.25); }
        .ribbon-track { display: flex; white-space: nowrap; animation: scrollRibbon 30s linear infinite; }
        .ribbon-track span { font-size: 0.68rem; font-family: var(--font-body); text-transform: uppercase; letter-spacing: 3px; font-weight: 500; padding: 0 36px; color: rgba(255,255,255,0.8); }
        .ribbon-track span.dot { color: var(--accent-gold); font-size: 0.45rem; padding: 0; }
        @keyframes scrollRibbon { from { transform: translateX(0); } to { transform: translateX(-50%); } }

        /* ===== SECTION LAYOUT ===== */
        .section-padding { padding: 100px 0; }
        .section-label { font-family: var(--font-body); font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 4px; color: var(--accent-terracotta) !important; display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
        .section-label::after { content: ''; width: 36px; height: 1px; background: var(--accent-terracotta); }
        .ornament { display: flex; align-items: center; justify-content: center; gap: 14px; margin: 18px auto 0; color: var(--accent-gold); font-size: 0.75rem; }
        .ornament::before, .ornament::after { content: ''; width: 55px; height: 1px; background: linear-gradient(90deg, transparent, var(--accent-gold)); }
        .ornament::after { background: linear-gradient(90deg, var(--accent-gold), transparent); }

        /* ===== QUICK FACTS STRIP ===== */
        .quick-facts { background: var(--bg-secondary); border-top: 1px solid var(--border-soft); border-bottom: 1px solid var(--border-soft); padding: 60px 0; }
        .fact-item { text-align: center; padding: 0 20px; border-right: 1px solid var(--border-soft); }
        .fact-item:last-child { border-right: none; }
        .fact-icon { width: 48px; height: 48px; background: var(--bg-card); border: 1px solid var(--border-soft); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; color: var(--accent-gold); font-size: 1.1rem; box-shadow: 0 2px 10px var(--shadow-warm); }
        .fact-value { font-family: var(--font-display); font-size: 1.8rem; font-weight: 700; color: var(--accent-brown); line-height: 1; }
        .fact-label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; color: var(--text-light); margin-top: 6px; font-weight: 700; }

        /* ===== VISITING INFO ===== */
        #visiting-info { background: var(--bg-body); }
        .info-card { background: var(--bg-card); border-radius: 4px; padding: 46px; box-shadow: 0 4px 30px var(--shadow-warm); border: 1px solid rgba(184,132,42,0.1); position: relative; overflow: hidden; }
        .info-card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 3px; background: linear-gradient(90deg, var(--accent-gold), var(--accent-terracotta), var(--accent-sage)); }
        .info-header { display: flex; align-items: center; gap: 16px; margin-bottom: 32px; padding-bottom: 24px; border-bottom: 1px solid rgba(184,132,42,0.12); }
        .info-header-icon { width: 50px; height: 50px; background: var(--bg-secondary); color: var(--accent-gold); border-radius: 3px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; border: 1px solid var(--border-soft); }
        .info-item { display: flex; align-items: flex-start; padding: 18px 20px; border-radius: 3px; transition: all 0.25s; background: var(--bg-secondary); border: 1px solid transparent; gap: 14px; }
        .info-item:hover { transform: translateY(-2px); background: #fff; border-color: var(--border-soft); box-shadow: 0 4px 16px var(--shadow-warm); }
        .info-icon { width: 38px; height: 38px; border-radius: 2px; display: flex; align-items: center; justify-content: center; font-size: 0.88rem; flex-shrink: 0; color: #fff; }
        .info-item h6 { font-family: var(--font-body) !important; font-size: 0.86rem !important; font-weight: 700 !important; color: var(--text-main) !important; text-transform: none !important; letter-spacing: 0.3px; margin-bottom: 4px; }
        .info-item p { color: var(--text-muted) !important; font-size: 0.85rem; font-weight: 500; margin: 0; }
        .icon-gold { background-color: var(--accent-gold); }
        .icon-brown { background-color: var(--accent-brown); }
        .icon-terracotta { background-color: var(--accent-terracotta); }
        .icon-sage { background-color: var(--accent-sage); }
        .icon-dark { background-color: var(--accent-dark); }

        .side-info-panel { display: flex; flex-direction: column; gap: 18px; height: 100%; }
        .side-info-block { background: var(--bg-card); border: 1px solid var(--border-soft); border-radius: 4px; padding: 26px; box-shadow: 0 2px 14px var(--shadow-warm); flex: 1; position: relative; overflow: hidden; }
        .side-info-block::before { content: ''; position: absolute; left: 0; top: 0; height: 100%; width: 3px; }
        .side-info-block.hours::before { background: var(--accent-gold); }
        .side-info-block.rules::before { background: var(--accent-sage); }
        .side-info-block h5 { font-family: var(--font-body) !important; font-size: 0.72rem !important; font-weight: 700 !important; text-transform: uppercase; letter-spacing: 2.5px; color: var(--text-light) !important; margin-bottom: 16px; }
        .hours-row { display: flex; justify-content: space-between; align-items: center; padding: 9px 0; border-bottom: 1px solid rgba(184,132,42,0.1); font-size: 0.85rem; }
        .hours-row:last-child { border-bottom: none; padding-bottom: 0; }
        .hours-row .day { color: var(--text-muted); font-weight: 500; }
        .hours-row .time { color: var(--text-main); font-weight: 700; }
        .rule-item { display: flex; align-items: center; gap: 10px; font-size: 0.85rem; font-weight: 500; color: var(--text-muted); padding: 6px 0; }
        .rule-item i { color: var(--accent-sage); font-size: 0.68rem; flex-shrink: 0; }

        /* ===== LOCATION ===== */
        #location { background: var(--bg-secondary); border-top: 1px solid var(--border-soft); border-bottom: 1px solid var(--border-soft); }
        .map-container { border-radius: 6px; overflow: hidden; box-shadow: 0 8px 40px var(--shadow-warm); border: 3px solid var(--accent-brown); }
        .btn-map { background-color: var(--accent-dark); color: #fff !important; padding: 13px 36px; border-radius: 2px; text-decoration: none; font-weight: 600; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 2px; transition: all 0.3s; border: 1px solid var(--accent-dark); display: inline-flex; align-items: center; gap: 10px; margin-top: 24px; }
        .btn-map:hover { background-color: transparent; color: var(--accent-dark) !important; box-shadow: 0 6px 18px var(--shadow-warm); transform: translateY(-2px); }
        .location-details { display: flex; flex-direction: column; gap: 14px; height: 100%; }
        .loc-block { background: var(--bg-card); border: 1px solid var(--border-soft); border-radius: 3px; padding: 20px 22px; box-shadow: 0 2px 10px var(--shadow-warm); display: flex; align-items: flex-start; gap: 14px; }
        .loc-icon { width: 36px; height: 36px; background: var(--bg-secondary); border: 1px solid var(--border-soft); border-radius: 2px; display: flex; align-items: center; justify-content: center; color: var(--accent-gold); font-size: 0.9rem; flex-shrink: 0; }
        .loc-block h6 { font-family: var(--font-body) !important; font-size: 0.7rem !important; font-weight: 700 !important; text-transform: uppercase; letter-spacing: 2px; color: var(--text-light) !important; margin-bottom: 5px; }
        .loc-block p { font-size: 0.9rem; font-weight: 500; color: var(--text-main); margin: 0; line-height: 1.55; }

        /* ===== ABOUT ===== */
        #about { background: var(--bg-body); }
        .about-img-frame { position: relative; }
        .about-img-frame::before { content: ''; position: absolute; top: 18px; right: -18px; width: 100%; height: 100%; border: 1px solid var(--border-soft); border-radius: 3px; z-index: 0; }
        .about-img { border-radius: 3px; width: 100%; position: relative; z-index: 1; box-shadow: 0 12px 50px var(--shadow-warm); border: 1px solid rgba(184,132,42,0.15); }
        .stat-box { background: var(--bg-card); padding: 26px 16px; border-radius: 3px; text-align: center; border: 1px solid rgba(184,132,42,0.12); transition: all 0.3s; box-shadow: 0 2px 12px var(--shadow-warm); }
        .stat-box:hover { transform: translateY(-4px); border-color: var(--accent-gold); box-shadow: 0 8px 25px var(--shadow-warm); }
        .stat-number { font-family: var(--font-display); color: var(--accent-brown); font-size: 2.5rem; font-weight: 700; line-height: 1; }
        .stat-label { font-family: var(--font-body); font-size: 0.65rem; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: var(--text-light); margin-top: 6px; }
        .highlight-list { list-style: none; padding: 0; margin: 26px 0; }
        .highlight-list li { display: flex; align-items: flex-start; gap: 12px; padding: 10px 0; border-bottom: 1px solid rgba(184,132,42,0.1); font-size: 0.95rem; font-weight: 500; color: var(--text-muted); line-height: 1.5; }
        .highlight-list li:last-child { border-bottom: none; }
        .highlight-list li i { color: var(--accent-gold); margin-top: 5px; font-size: 0.72rem; flex-shrink: 0; }

        /* ===== CHATBOT ===== */
        .chat-btn { position: fixed; bottom: 32px; right: 32px; background-color: var(--accent-brown); color: #fff; border: none; border-radius: 50%; width: 56px; height: 56px; font-size: 20px; box-shadow: 0 4px 20px rgba(124,92,46,0.35); cursor: pointer; z-index: 1000; transition: all 0.3s; }
        .chat-btn:hover { transform: scale(1.1) translateY(-2px); background-color: var(--accent-dark); }
        .chat-box { position: fixed; bottom: 102px; right: 32px; width: 350px; height: 500px; background: #fff; border-radius: 6px; box-shadow: 0 20px 60px rgba(0,0,0,0.12); display: none; flex-direction: column; z-index: 1001; overflow: hidden; border: 1px solid var(--border-soft); }
        .chat-header { background: var(--accent-brown); color: #fff; padding: 16px 20px; font-family: var(--font-body); font-weight: 600; letter-spacing: 1.5px; text-transform: uppercase; font-size: 0.75rem; display: flex; justify-content: space-between; align-items: center; }
        .chat-header-info { display: flex; flex-direction: column; gap: 2px; }
        .chat-header-name { font-weight: 700; font-size: 0.8rem; letter-spacing: 1.5px; }
        .chat-header-status { font-size: 0.65rem; opacity: 0.8; letter-spacing: 1px; font-weight: 400; }
        .chat-body { flex: 1; padding: 18px; overflow-y: auto; background: var(--bg-secondary); }
        .message { margin-bottom: 10px; padding: 11px 15px; border-radius: 4px; font-size: 0.86rem; font-weight: 500; max-width: 86%; font-family: var(--font-body); line-height: 1.55; }
        .bot-msg { background: #fff; color: var(--text-main); border-bottom-left-radius: 0; border: 1px solid rgba(184,132,42,0.12); box-shadow: 0 1px 4px var(--shadow-warm); }
        .user-msg { background: var(--accent-brown); color: #fff; margin-left: auto; border-bottom-right-radius: 0; }
        .chat-suggestions { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 10px; }
        .chat-suggestion-btn { background: var(--bg-secondary); border: 1px solid rgba(184,132,42,0.3); color: var(--accent-brown); font-size: 0.72rem; font-weight: 600; padding: 5px 10px; border-radius: 20px; cursor: pointer; font-family: var(--font-body); transition: all 0.2s; white-space: nowrap; }
        .chat-suggestion-btn:hover { background: var(--accent-brown); color: #fff; }
        .typing-indicator { display: flex; align-items: center; gap: 4px; padding: 11px 15px; }
        .typing-indicator span { width: 7px; height: 7px; border-radius: 50%; background: var(--accent-gold); animation: typing 1.2s infinite; }
        .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
        .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }
        @keyframes typing { 0%, 60%, 100% { transform: translateY(0); opacity: 0.4; } 30% { transform: translateY(-6px); opacity: 1; } }
        .chat-footer { padding: 12px; border-top: 1px solid rgba(184,132,42,0.12); display: flex; background: #fff; gap: 8px; }
        .chat-footer input { flex: 1; padding: 10px 13px; border: 1px solid rgba(184,132,42,0.2); border-radius: 2px; outline: none; background: var(--bg-secondary); color: var(--text-main); font-size: 0.86rem; font-weight: 500; font-family: var(--font-body); }
        .chat-footer input::placeholder { color: var(--text-light); }
        .chat-footer input:focus { border-color: var(--accent-gold); }
        .chat-footer button { background: var(--accent-brown); border: none; color: #fff; padding: 0 15px; border-radius: 2px; font-size: 0.9rem; cursor: pointer; transition: 0.2s; }
        .chat-footer button:hover { background: var(--accent-dark); }

        /* ===== FOOTER ===== */
        footer { background-color: var(--accent-dark); color: #c8bfad; padding: 70px 0 30px; border-top: 1px solid rgba(255,255,255,0.06); }
        footer h5, footer h6 { color: #ede5d3 !important; font-family: var(--font-heading); }
        footer p, footer li { color: #a89880; font-size: 0.88rem; font-weight: 500; }
        footer a { color: #a89880; text-decoration: none; transition: color 0.2s; font-weight: 500; }
        footer a:hover { color: var(--accent-gold); }

        /* ===== SCROLL REVEAL ===== */
        .reveal { opacity: 0; transform: translateY(28px); transition: opacity 0.65s ease, transform 0.65s ease; }
        .reveal.revealed { opacity: 1; transform: translateY(0); }
        .reveal-delay-1 { transition-delay: 0.12s; }
        .reveal-delay-2 { transition-delay: 0.22s; }
        .reveal-delay-3 { transition-delay: 0.32s; }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 991px) { .side-info-panel, .location-details { margin-top: 28px; } .about-img-frame::before { display: none; } }
        @media (max-width: 768px) { .slide-title { font-size: 2.6rem; } .thumbnail-container { width: 100%; right: 0; bottom: 16px; padding-left: 20px; max-width: 100%; } .slider-content { padding-left: 22px; padding-right: 22px; } .slide-desc { font-size: 0.9rem; max-width: 100%; } .info-card { padding: 26px 20px; } .fact-item { border-right: none; border-bottom: 1px solid var(--border-soft); padding: 20px 0; } .fact-item:last-child { border-bottom: none; } .chat-box { width: calc(100vw - 40px); right: 20px; } }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid px-4 px-lg-5">
            <a class="navbar-brand" href="index.php">VISITEASE</a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fas fa-bars" style="color: var(--accent-brown);"></i>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <div class="navbar-nav align-items-center">
                    <a href="index.php" class="nav-link-custom active">Home</a>
                    <a href="exhibits.php" class="nav-link-custom">Explore</a>
                    <a href="gallery.php" class="nav-link-custom">Gallery</a>
                    <a href="about.php" class="nav-link-custom">About</a> 
                    <a href="faqs.php" class="nav-link-custom">FAQs</a> 
                    <a href="check_status.php" class="nav-link-custom">Track Status</a>
                    <a href="book.php" class="btn-book ms-4"><i class="fas fa-bookmark me-2"></i>Book Visit</a>
                </div>
            </div>
        </div>
    </nav>

    <section class="cinematic-slider">
        <div class="bg-layer-container">
            <div class="bg-layer" id="bgBack"></div>
            <div class="bg-layer" id="bgFront"></div>
        </div>
        <div class="slider-overlay"></div>
        <div class="slider-overlay-bottom"></div>
        <div class="container-fluid px-0 h-100 position-relative">
            <div class="row h-100 align-items-center g-0">
                <div class="col-lg-6">
                    <div class="slider-content">
                        <div class="slide-eyebrow animate__animated" id="slideLoc">Barangay Ilijan, Batangas City</div>
                        <h1 class="slide-title animate__animated" id="slideTitle">Pedro S. Tolentino Museum</h1>
                        <p class="slide-desc animate__animated" id="slideDesc">“Sulyap sa ating kasaysayan at sining. Tuklasin ang kwento sa likod ng bawat obra sa Pedro S. Tolentino Museum”.</p>
                        <a href="exhibits.php" class="btn-explore animate__animated" id="slideBtn"><i class="fas fa-arrow-right"></i> Explore Artifact</a>
                    </div>
                </div>
            </div>
            <div class="thumbnail-container" id="thumbnailRow"></div>
        </div>
    </section>

    <div class="announcement-ribbon">
        <div class="ribbon-track">
            <span>Open Mon–Sat 9AM – 6PM</span><span class="dot">◆</span>
            <span>Free Admission for Children Under 5</span><span class="dot">◆</span>
            <span>Advance Booking Required</span><span class="dot">◆</span>
            <span>Photography in Designated Zones</span><span class="dot">◆</span>
            <span>Wheelchair Accessible</span><span class="dot">◆</span>
            <span>Max 10 Visitors Per Slot</span><span class="dot">◆</span>
            <span>Pedro S. Tolentino Museum — Barangay Ilijan, Batangas City</span><span class="dot">◆</span>
            <span>Open Mon–Sat 9AM – 6PM</span><span class="dot">◆</span>
            <span>Free Admission for Children Under 5</span><span class="dot">◆</span>
            <span>Advance Booking Required</span><span class="dot">◆</span>
            <span>Photography in Designated Zones</span><span class="dot">◆</span>
            <span>Wheelchair Accessible</span><span class="dot">◆</span>
            <span>Max 10 Visitors Per Slot</span><span class="dot">◆</span>
            <span>Pedro S. Tolentino Museum — Barangay Ilijan, Batangas City</span><span class="dot">◆</span>
        </div>
    </div>

    <section class="quick-facts">
        <div class="container-fluid px-4 px-lg-5">
            <div class="row g-0 text-center">
                <div class="col-6 col-md-3 fact-item reveal">
                    <div class="fact-icon"><i class="fas fa-clock"></i></div>
                    <div class="fact-value">9AM</div>
                    <div class="fact-label">Opens Daily</div>
                </div>
                <div class="col-6 col-md-3 fact-item reveal reveal-delay-1">
                    <div class="fact-icon"><i class="fas fa-users"></i></div>
                    <div class="fact-value">10</div>
                    <div class="fact-label">Max Per Slot</div>
                </div>
                <div class="col-6 col-md-3 fact-item reveal reveal-delay-2">
                    <div class="fact-icon"><i class="fas fa-landmark"></i></div>
                    <div class="fact-value">5k+</div>
                    <div class="fact-label">Artifacts</div>
                </div>
                <div class="col-6 col-md-3 fact-item reveal reveal-delay-3">
                    <div class="fact-icon"><i class="fas fa-ticket-alt"></i></div>
                    <div class="fact-value">Free</div>
                    <div class="fact-label">Under 5 yrs</div>
                </div>
            </div>
        </div>
    </section>

    <section id="visiting-info" class="section-padding">
        <div class="container-fluid px-4 px-lg-5">
            <div class="text-center mb-5 reveal">
                <div class="section-label" style="justify-content: center;">Guidelines</div>
                <h2 style="font-family: var(--font-display); font-size: 2.8rem; margin-bottom: 12px;">Visiting Information</h2>
                <p style="max-width: 500px; margin: 0 auto; font-size: 1.05rem; font-weight: 500; color: var(--text-muted);">Everything you need to know before your visit to ensure a smooth and enjoyable experience.</p>
                <div class="ornament"><i class="fas fa-leaf fa-xs"></i></div>
            </div>
            <div class="row g-4 align-items-stretch">
                <div class="col-lg-7 reveal">
                    <div class="info-card h-100">
                        <div class="info-header">
                            <div class="info-header-icon"><i class="fas fa-landmark"></i></div>
                            <div>
                                <h4 style="font-family: var(--font-body) !important; font-size: 1.05rem !important; font-weight: 700 !important; margin-bottom: 4px;">Museum Guidelines</h4>
                                <p style="font-size: 0.85rem; font-weight: 500; color: var(--text-muted); margin: 0;">Please review before planning your visit</p>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-icon icon-gold"><i class="far fa-calendar-check"></i></div>
                                    <div><h6>Advance Booking</h6><p>Booking recommended in advance to secure your slot</p></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-icon icon-brown"><i class="fas fa-users"></i></div>
                                    <div><h6>Group Size</h6><p>Up to 10 visitors per time slot allowed</p></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-icon icon-terracotta"><i class="fas fa-star"></i></div>
                                    <div><h6>Free Admission</h6><p>Children under 5 years old enter for free</p></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-icon icon-sage"><i class="fas fa-camera"></i></div>
                                    <div><h6>Photography</h6><p>Personal photos allowed in designated areas</p></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="info-item">
                                    <div class="info-icon icon-dark"><i class="fas fa-wheelchair"></i></div>
                                    <div><h6>Accessibility</h6><p>Fully wheelchair accessible throughout all museum galleries and facilities</p></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 reveal reveal-delay-1">
                    <div class="side-info-panel h-100">
                        <div class="side-info-block hours">
                            <h5>Opening Hours</h5>
                            <div class="hours-row"><span class="day">Monday – Friday</span><span class="time">9:00 AM – 6:00 PM</span></div>
                            <div class="hours-row"><span class="day">Saturday</span><span class="time">9:00 AM – 6:00 PM</span></div>
                            <div class="hours-row"><span class="day">Sunday</span><span class="time">10:00 AM – 5:00 PM</span></div>
                            <div class="hours-row"><span class="day">Public Holidays</span><span class="time">Check Schedule</span></div>
                        </div>
                        <div class="side-info-block rules">
                            <h5>Museum Rules</h5>
                            <div class="rule-item"><i class="fas fa-check-circle"></i> No food or drinks inside the galleries</div>
                            <div class="rule-item"><i class="fas fa-check-circle"></i> Flash photography is strictly prohibited</div>
                            <div class="rule-item"><i class="fas fa-check-circle"></i> Please maintain silence in exhibit areas</div>
                            <div class="rule-item"><i class="fas fa-check-circle"></i> Children must be accompanied by adults</div>
                            <div class="rule-item"><i class="fas fa-check-circle"></i> Do not touch the displayed artifacts</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="location" class="section-padding">
        <div class="container-fluid px-4 px-lg-5">
            <div class="text-center mb-5 reveal">
                <div class="section-label" style="justify-content: center;">Find Us</div>
                <h2 style="font-family: var(--font-display); font-size: 2.8rem; margin-bottom: 12px;">Visit Us</h2>
                <p style="max-width: 460px; margin: 0 auto; font-size: 1.05rem; font-weight: 500; color: var(--text-muted);">Pedro S. Tolentino Museum, Barangay Ilijan, Batangas City</p>
                <div class="ornament"><i class="fas fa-map-marker-alt fa-xs"></i></div>
            </div>
            <div class="row g-4 align-items-stretch">
                <div class="col-lg-8 reveal">
                    <div class="map-container">
                        <iframe src="https://maps.google.com/maps?q=Pedro%20S.%20Tolentino%20Museum,%20Barangay%20Ilijan,%20Batangas%20City&t=&z=15&ie=UTF8&iwloc=&output=embed" width="100%" height="420" style="border:0; display:block;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                    <div class="text-center">
                        <a href="https://maps.google.com/maps?q=Ilijan,%20Batangas%20City" target="_blank" class="btn-map"><i class="fas fa-map-marker-alt"></i> Get Directions</a>
                    </div>
                </div>
                <div class="col-lg-4 reveal reveal-delay-1">
                    <div class="location-details h-100">
                        <div class="loc-block">
                            <div class="loc-icon"><i class="fas fa-map-marker-alt"></i></div>
                            <div><h6>Address</h6><p>Pedro S. Tolentino Museum<br>Barangay Ilijan, Batangas City<br>Batangas, Philippines</p></div>
                        </div>
                        <div class="loc-block">
                            <div class="loc-icon"><i class="fas fa-bus"></i></div>
                            <div><h6>Getting Here</h6><p>Accessible by jeepney, tricycle, or private vehicle from Batangas City proper.</p></div>
                        </div>
                        <div class="loc-block">
                            <div class="loc-icon"><i class="fas fa-parking"></i></div>
                            <div><h6>Parking</h6><p>Free on-site parking available. Limited slots — early arrival recommended on weekends.</p></div>
                        </div>
                        <div class="loc-block">
                            <div class="loc-icon"><i class="fas fa-phone"></i></div>
                            <div><h6>Inquiries</h6><p>(043) 123-4567 &nbsp;|&nbsp; info@tolentinomuseum.ph</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="about" class="section-padding">
        <div class="container-fluid px-4 px-lg-5">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 order-lg-2 reveal">
                    <div class="about-img-frame">
                        
                </div>
                <div class="col-lg-6 order-lg-1 reveal reveal-delay-1">
                    <div class="section-label">About the Museum</div>
                    <h2 style="font-family: var(--font-display); font-size: 3rem; line-height: 1.15; margin-bottom: 18px;">
                        Curated for<br><em style="font-style: italic; color: var(--accent-brown) !important;">Clarity</em>
                    </h2>
                    <p style="color: var(--text-muted); font-size: 1.05rem; line-height: 1.85; font-weight: 500; margin-bottom: 0;">
                        The VisitEase Museum strips away the clutter to focus on the essence of every artifact. We believe history should be experienced through a modern lens — accessible, immersive, and deeply meaningful for every visitor.
                    </p>
                    <ul class="highlight-list">
                        <li><i class="fas fa-circle"></i> Home to over 5,000 carefully preserved historical artifacts</li>
                        <li><i class="fas fa-circle"></i> Dedicated to the legacy of Mayor Pedro S. Tolentino</li>
                        <li><i class="fas fa-circle"></i> Guided tours available for groups and school visits</li>
                        <li><i class="fas fa-circle"></i> Preserving the cultural heritage of Batangas City</li>
                    </ul>
                    <div class="row g-3 mt-1">
                        <div class="col-4"><div class="stat-box"><div class="stat-number">500+</div><div class="stat-label">Artifacts</div></div></div>
                        <div class="col-4"><div class="stat-box"><div class="stat-number">12</div><div class="stat-label">Galleries</div></div></div>
                        <div class="col-4"><div class="stat-box"><div class="stat-number">98%</div><div class="stat-label">Satisfaction</div></div></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'footer_modal.php'; ?>

    <!-- ===== CHATBOT ===== -->
    <button class="chat-btn" onclick="toggleChat()" title="Chat with us">
        <i class="fas fa-comment-dots"></i>
    </button>

    <div class="chat-box" id="chatBox">
        <div class="chat-header">
            <div class="chat-header-info">
                <span class="chat-header-name"><i class="fas fa-circle me-2" style="color: #90ee90; font-size: 0.5rem;"></i>VisitEase Assistant</span>
                <span class="chat-header-status">Pedro S. Tolentino Museum · Online</span>
            </div>
            <span onclick="toggleChat()" style="cursor: pointer; font-size: 1.3rem; opacity: 0.7; line-height: 1;">×</span>
        </div>
        <div class="chat-body" id="chatBody">
            <div class="message bot-msg">
                Mabuhay! 👋 Welcome to <strong>VisitEase</strong> — your guide to the <strong>Pedro S. Tolentino Museum</strong>. How can I help you today?
                <div class="chat-suggestions">
                    <span class="chat-suggestion-btn" onclick="quickAsk('How do I book a visit?')">📅 Book a Visit</span>
                    <span class="chat-suggestion-btn" onclick="quickAsk('What are your opening hours?')">🕘 Hours</span>
                    <span class="chat-suggestion-btn" onclick="quickAsk('Where is the museum located?')">📍 Location</span>
                    <span class="chat-suggestion-btn" onclick="quickAsk('Who is Pedro S. Tolentino?')">🏛️ About Mayor</span>
                </div>
            </div>
        </div>
        <div class="chat-footer">
            <input type="text" id="userInput" placeholder="Type your question here..." onkeypress="handleEnter(event)">
            <button onclick="sendMessage()"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>

    <script>
        // ===== SLIDER =====
        const artifacts = [
            { id: 0, title: "Pedro S. Tolentino Museum", location: "Main Gallery Hub", desc: "Juan Luna's magnum opus. A harrowing glimpse into the bloodied floors of the Roman Colosseum, symbolizing the plight of the Filipino people under colonial rule.", img: "pics/pedro landing.jpg" },
            { id: 1, title: "Achievements and Certificate", location: "Archive Section", desc: "Recognizing his vital contributions to preserving local heritage. This collection features important manuscripts, historical records, and academic accolades compiled for future generations.", img: "pics/certificate.jpg" },
            { id: 2, title: "The Mayor's Attire", location: "VIP Quarters", desc: "A collection of formal wear and official suits worn by Pedro S. Tolentino during his tenure. These preserved garments represent the dignity of a dedicated public servant.", img: "pics/formal.jpg" },
            { id: 3, title: "A Legacy of Public Service", location: "Heritage Vault", desc: "An intimate collection of personal memorabilia, accolades, and historical documents defining the life of Mayor Pedro S. Tolentino. A tribute to his enduring dedication.", img: "pics/painting.jpg" }
        ];

        let currentIndex = 0, isAnimating = false;

        function initSlider() {
            document.getElementById('bgBack').style.backgroundImage = `url('${artifacts[0].img}')`;
            ['slideTitle','slideDesc','slideLoc','slideBtn'].forEach(id => document.getElementById(id).classList.add('animate__fadeInLeft'));
            renderThumbnails();
            artifacts.forEach(a => { const i = new Image(); i.src = a.img; });
        }

        function renderSlide(index) {
            if (isAnimating && index === currentIndex) return;
            isAnimating = true;
            const bgBack = document.getElementById('bgBack'), bgFront = document.getElementById('bgFront');
            const els = ['slideLoc','slideTitle','slideDesc','slideBtn'].map(id => document.getElementById(id));
            bgFront.style.backgroundImage = `url('${artifacts[index].img}')`;
            bgFront.classList.add('active-fade');
            els.forEach(el => { el.classList.remove('animate__fadeInLeft'); el.classList.add('animate__fadeOut'); });
            setTimeout(() => {
                document.getElementById('slideTitle').innerText = artifacts[index].title;
                document.getElementById('slideLoc').innerText = artifacts[index].location;
                document.getElementById('slideDesc').innerText = artifacts[index].desc;
                els.forEach(el => { el.classList.remove('animate__fadeOut'); el.classList.add('animate__fadeInLeft'); });
            }, 400);
            setTimeout(() => {
                bgBack.style.backgroundImage = `url('${artifacts[index].img}')`;
                bgFront.classList.remove('active-fade');
                bgFront.style.backgroundImage = 'none';
                isAnimating = false;
            }, 800);
        }

        function renderThumbnails() {
            const container = document.getElementById('thumbnailRow');
            container.innerHTML = '';
            artifacts.forEach((item, index) => {
                const card = document.createElement('div');
                card.className = `thumb-card ${index === currentIndex ? 'active' : ''}`;
                card.onclick = () => { if (index !== currentIndex) { currentIndex = index; renderSlide(index); renderThumbnails(); } };
                card.innerHTML = `<img src="${item.img}" alt="${item.title}"><div class="thumb-info"><span class="thumb-title">${item.title}</span><span class="thumb-loc">${item.location}</span></div>`;
                container.appendChild(card);
            });
        }

        window.onload = function() {
            initSlider();
            const observer = new IntersectionObserver(entries => entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('revealed'); }), { threshold: 0.08 });
            document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
        };

        // ===== CHATBOT =====
        function toggleChat() {
            const cb = document.getElementById("chatBox");
            cb.style.display = (cb.style.display === "none" || cb.style.display === "") ? "flex" : "none";
        }

        function handleEnter(e) { if (e.key === 'Enter') sendMessage(); }

        function quickAsk(text) {
            document.getElementById("userInput").value = text;
            sendMessage();
        }

        function showTyping() {
            const chatBody = document.getElementById("chatBody");
            const typingDiv = document.createElement("div");
            typingDiv.className = "message bot-msg typing-indicator";
            typingDiv.id = "typingIndicator";
            typingDiv.innerHTML = '<span></span><span></span><span></span>';
            chatBody.appendChild(typingDiv);
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        function removeTyping() {
            const t = document.getElementById("typingIndicator");
            if (t) t.remove();
        }

        function sendMessage() {
            const input = document.getElementById("userInput");
            const msg = input.value.trim();
            const chatBody = document.getElementById("chatBody");
            if (!msg) return;

            const userDiv = document.createElement("div");
            userDiv.className = "message user-msg";
            userDiv.textContent = msg;
            chatBody.appendChild(userDiv);
            input.value = "";
            chatBody.scrollTop = chatBody.scrollHeight;

            showTyping();

            setTimeout(() => {
                removeTyping();
                const botDiv = document.createElement("div");
                botDiv.className = "message bot-msg";
                botDiv.innerHTML = getBotResponse(msg);
                chatBody.appendChild(botDiv);
                chatBody.scrollTop = chatBody.scrollHeight;
            }, 800);
        }

        function getBotResponse(input) {
            const i = input.toLowerCase();
            if (i.includes("hello") || i.includes("hi")) return "Hello! Welcome to VisitEase. How can I help you today?";
            if (i.includes("open") || i.includes("hour")) return "We're open <strong>Mon–Sat: 9AM–6PM</strong> and <strong>Sun: 10AM–5PM</strong>.";
            if (i.includes("price") || i.includes("ticket") || i.includes("free")) return "Admission is complimentary. Advance booking is required to reserve your slot.";
            if (i.includes("book")) return "Click the <strong>'Book Visit'</strong> button on the navigation bar to reserve a slot.";
            if (i.includes("location") || i.includes("where")) return "We are at <strong>Pedro S. Tolentino Museum, Barangay Ilijan, Batangas City</strong>.";
            if (i.includes("park")) return "Free parking is available on-site. Arrive early on weekends for availability.";
            if (i.includes("track")) return "Go to <strong>'Track Status'</strong> in the navigation menu to monitor your booking.";
            if (i.includes("Who is your developer") || i.includes("system")) return "Ace Earl Jairus Natividad";
            return "I'm happy to help! You can ask about our hours, location, booking, parking, or admission.";
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
