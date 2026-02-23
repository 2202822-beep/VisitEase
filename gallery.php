<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VisitEase | Gallery</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=DM+Sans:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        /* --- GLOBAL STYLES (MATCHING EXHIBITS.PHP) --- */
        :root {
            --cream:   #f7f2ea;
            --cream2:  #efe8d8;
            --cream3:  #e8dfc8;
            --white:   #ffffff;
            --dark:    #231e14;
            --muted:   #6b6050;
            --light:   #a39078;
            --gold:    #b8842a;
            --brown:   #7c5c2e;
            --terra:   #c4704a;
            --black:   #1e1a14;
            --border:  rgba(184,132,42,0.18);
            --shadow:  rgba(124,92,46,0.10);
            --fd: 'Playfair Display', serif;
            --fh: 'Cormorant Garamond', serif;
            --fb: 'DM Sans', sans-serif;
        }
        
        body { 
            font-family: var(--fb); 
            color: var(--dark); 
            background-color: var(--cream); 
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        h1, h2, h3, h4, h5 { font-family: var(--fd); color: var(--dark); font-weight: 700; }
        .text-gold { color: var(--gold) !important; }

        /* ── NAVBAR ── */
        .navbar { background: rgba(247,242,234,0.97); backdrop-filter: blur(20px); padding: 0.8rem 0; border-bottom: 1px solid var(--border); z-index: 1000; }
        .navbar-brand { font-family: var(--fh); font-size: 1.6rem; letter-spacing: 3px; color: var(--brown) !important; font-weight: 600; }
        .nav-link-custom { color: var(--dark); font-weight: 500; font-size: 0.75rem; margin-left: 20px; text-decoration: none; transition: color .3s; text-transform: uppercase; letter-spacing: 2px; position: relative; }
        .nav-link-custom::after { content:''; position:absolute; bottom:-4px; left:0; width:0; height:1px; background:var(--gold); transition:width .3s; }
        .nav-link-custom:hover::after, .nav-link-custom.active::after { width:100%; }
        .nav-link-custom:hover, .nav-link-custom.active { color: var(--gold) !important; }
        .btn-book { background:var(--brown); color:#fff !important; padding:8px 20px; border-radius:2px; font-size:0.75rem; font-weight:500; text-transform:uppercase; letter-spacing:2px; border:1px solid var(--brown); text-decoration:none; transition:all .3s; margin-left: 20px;}
        .btn-book:hover { background:transparent; color:var(--brown) !important; }

        /* --- HEADER STYLES --- */
        .page-header { 
            padding: 160px 0 60px; 
            text-align: center; 
            background: linear-gradient(to bottom, var(--cream2), var(--cream));
            border-bottom: 1px solid var(--border);
        }
        .header-subtitle {
            display:inline-flex; align-items:center; gap:8px; font-family:var(--fb); font-size:.65rem; font-weight:600; text-transform:uppercase; letter-spacing:3px; color:var(--terra) !important; margin-bottom:15px;
        }
        .header-subtitle::before, .header-subtitle::after {
            content:''; width:25px; height:1px; background:var(--terra);
        }
        .page-header h1 { font-size: 3.5rem; margin-bottom: 15px; line-height: 1.2; }
        .page-header h1 em { color: var(--brown); font-style: italic; }
        .page-header p {
            color: var(--muted) !important; 
            font-size: 0.95rem;
            line-height: 1.8;
        }

        /* --- GALLERY GRID --- */
        .gallery-section {
            padding: 70px 0; 
        }
        
        .gallery-item { 
            position: relative; 
            overflow: hidden; 
            background: var(--cream3); 
            cursor: pointer; 
            margin-bottom: 30px; 
            border-radius: 4px; 
            border: 1px solid var(--border);
            box-shadow: 0 4px 15px var(--shadow); 
            transition: all 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        
        .gallery-item:hover { 
            transform: translateY(-8px); 
            box-shadow: 0 15px 35px rgba(124,92,46,0.2); 
            border-color: var(--gold);
        }
        
        .gallery-item img { 
            width: 100%; 
            height: 380px; 
            object-fit: cover; 
            transition: transform 0.7s ease; 
        }
        
        .gallery-item::after {
            content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(to top, rgba(30,26,20,0.85) 0%, rgba(30,26,20,0.2) 50%, transparent 100%);
            opacity: 0; transition: opacity 0.4s ease; pointer-events: none;
        }
        
        .gallery-item:hover img { transform: scale(1.05); }
        .gallery-item:hover::after { opacity: 1; }
        
        .gallery-caption { 
            position: absolute; bottom: 30px; left: 0; width: 100%;
            opacity: 0; transition: all 0.4s ease; color: var(--cream); text-align: center; z-index: 2;
            transform: translateY(20px);
        }
        .gallery-caption h5 { 
            font-size: 1.5rem; text-transform: none; color: var(--cream);
            letter-spacing: 1px; margin: 0; font-style: italic;
        }
        .gallery-caption .line {
            width: 40px; height: 2px; background: var(--gold); margin: 8px auto 0;
            transition: width 0.4s ease;
        }

        .gallery-item:hover .gallery-caption { opacity: 1; transform: translateY(0); }
        .gallery-item:hover .line { width: 80px; }

        /* --- MODAL STYLES --- */
        .modal-backdrop.show {
            opacity: 0.9;
            background-color: var(--black);
        }
        .modal-content {
            background: transparent;
            border: none;
        }
        .btn-close-custom {
            position: absolute;
            top: -40px;
            right: 0;
            background: none;
            border: none;
            color: var(--cream);
            font-size: 2rem;
            cursor: pointer;
            transition: color 0.3s ease;
            z-index: 1055;
        }
        .btn-close-custom:hover { color: var(--gold); }
        
        #modalImage {
            border: 2px solid var(--gold);
            border-radius: 4px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        }
        
        #modalCaption {
            font-size: 2rem;
            color: var(--cream);
            margin-top: 20px;
            font-style: italic;
        }
        #modalCaption::after {
            content: ''; display: block; width: 50px; height: 2px; background: var(--gold); margin: 10px auto 0;
        }

        /* --- FOOTER --- */
        footer { background:var(--black); padding:50px 0 20px; border-top:1px solid rgba(255,255,255,.06); margin-top: 40px;}
        footer h6 { color:#ede5d3 !important; font-family:var(--fh); font-weight:600 !important; }
        footer p { color:#a89880; font-size:.8rem; }
        .flink { color:#a89880; font-size:.8rem; text-decoration:none; display:block; margin-bottom:6px; transition:all .2s; }
        .flink:hover { color:var(--gold); padding-left:5px; }
        .fbrand { font-family:var(--fh); font-size:1.4rem; color:#f2ece0 !important; letter-spacing:2px; font-weight:600; display:block; margin-bottom:8px; }

        /* --- ANIMATIONS --- */
        .rv { opacity: 0; transform: translateY(30px); transition: opacity 0.8s ease, transform 0.8s ease; }
        .rv.on { opacity: 1; transform: translateY(0); }
        .d1 { transition-delay: 0.1s; }
        .d2 { transition-delay: 0.2s; }
        .d3 { transition-delay: 0.3s; }
    </style>
</head>
<body>

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
                    <a href="gallery.php" class="nav-link-custom active">Gallery</a>
                    <a href="check_status.php" class="nav-link-custom">Track Status</a>
                    <a href="book.php" class="btn-book">Book Visit</a>
                </div>
            </div>
        </div>
    </nav>

    <header class="page-header rv">
        <div class="container">
            <div class="header-subtitle">Visual Collection</div>
            <h1>Museum <em>Gallery</em></h1>
            <p class="mx-auto" style="max-width: 600px;">Step back in time and explore the carefully preserved artifacts, photographs, and historical documents that define our rich heritage.</p>
        </div>
    </header>

    <section class="gallery-section">
        <div class="container-fluid px-4 px-lg-5">
            <div class="row g-4">
                
                <div class="col-md-6 col-lg-4 rv d1">
                    <div class="gallery-item">
                        <img src="494813889_1218375400006712_3528886370220596685_n.jpg" alt="Ceramics">
                        <div class="gallery-caption">
                            <h5>Ancient Ceramics</h5>
                            <div class="line"></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4 rv d2">
                    <div class="gallery-item">
                        <img src="494826199_1763960804490430_7579041577827863987_n.jpg" alt="Royal Armory">
                        <div class="gallery-caption">
                            <h5>Royal Armory</h5>
                            <div class="line"></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4 rv d3">
                    <div class="gallery-item">
                        <img src="494359485_2155062051631886_4951905305416589935_n.jpg" alt="Abstract Art">
                        <div class="gallery-caption">
                            <h5>Abstract Painting</h5>
                            <div class="line"></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4 rv d1">
                    <div class="gallery-item">
                        <img src="494823189_472537129256301_8369976507955445871_n.jpg" alt="Sculpture">
                        <div class="gallery-caption">
                            <h5>Historical Sculpture</h5>
                            <div class="line"></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4 rv d2">
                    <div class="gallery-item">
                        <img src="494825054_1641213353259511_8978938757165686738_n.jpg" alt="Scriptures">
                        <div class="gallery-caption">
                            <h5>Preserved Scriptures</h5>
                            <div class="line"></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4 rv d3">
                    <div class="gallery-item">
                        <img src="494358990_1047988036664015_4979249687492318735_n.jpg" alt="Maps">
                        <div class="gallery-caption">
                            <h5>Antique Maps</h5>
                            <div class="line"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

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

    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl position-relative">
            <div class="modal-content">
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
                <div class="modal-body text-center p-0">
                    <img id="modalImage" src="" class="img-fluid" alt="Popup Image" style="max-height: 80vh; object-fit: contain; background: var(--cream);">
                    <h3 id="modalCaption"></h3>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Scroll Reveal Animation Logic
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if(entry.isIntersecting) {
                        entry.target.classList.add('on');
                    }
                });
            }, { threshold: 0.1 });
            
            document.querySelectorAll('.rv').forEach(el => observer.observe(el));

            // Gallery Modal Logic
            const galleryItems = document.querySelectorAll('.gallery-item');
            const modalImage = document.getElementById('modalImage');
            const modalCaption = document.getElementById('modalCaption');
            const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));

            galleryItems.forEach(item => {
                item.addEventListener('click', function() {
                    const imgSource = this.querySelector('img').src;
                    const captionText = this.querySelector('h5').innerText;
                    
                    modalImage.src = imgSource;
                    modalCaption.innerText = captionText;
                    
                    imageModal.show();
                });
            });
        });
    </script>
</body>
</html>