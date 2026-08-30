<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Miss Miracle Cosmetics · Miracle Lab</title>
  @vite(['resources/css/style.css'])
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="landing-page">
  <nav class="landing-nav">
    <div class="logo-container" style="display: flex; align-items: center;">
      <img src="/logo.png.png" alt="Miracle Lab Logo" style="height: 80px; width: auto; transform: scale(1.3); transform-origin: left center; mix-blend-mode: darken;" />
    </div>
    <div class="nav-links">
      <a class="nav-link" id="nav-pricing">S'abonner</a>
      <button class="btn-login" id="btn-goto-login">Connexion</button>
    </div>
    <!-- Hamburger (visible mobile/tablet only via CSS) -->
    <button class="landing-hamburger" id="landing-hamburger" aria-label="Menu">☰</button>
  </nav>

  <!-- Mobile Drawer Menu -->
  <div class="landing-mobile-menu" id="landing-mobile-menu">
    <div class="landing-mobile-drawer">
      <button class="landing-mobile-close" id="landing-mobile-close" aria-label="Fermer">✕</button>
      <a class="landing-mobile-link" href="#features-section">Fonctionnalités</a>
      <a class="landing-mobile-link" href="#roadmap-section">Comment ça marche</a>
      <a class="landing-mobile-link" href="#pricing-section">S'abonner</a>
      <a class="landing-mobile-cta" href="/login">Connexion</a>
      <a class="landing-mobile-cta" href="/register" style="background:#FFD700; color:#1A1A00; box-shadow: 0 4px 12px rgba(255,215,0,0.3);">Accéder au Miracle Lab</a>
    </div>
  </div>

  <header class="hero-section">
    <span class="hero-tag">Laboratoire Révolutionnaire</span>
    <h1 class="hero-title">Perfectionnez vos <span>formulations cosmétiques</span></h1>
    <p class="hero-subtitle">
      Calculez vos pourcentages instantanément, gérez vos phases de fabrication, évitez les conflits d'actifs et exportez vos fiches de fabrication en un clic.
    </p>
    <div class="hero-ctas">
      <button class="btn-primary" id="btn-hero-cta">Accéder au Miracle Lab</button>
    </div>
  </header>

  <section class="features-grid" id="features-section">
    <div class="feature-card">
      <div class="feature-icon">
        <i data-lucide="calculator" style="width: 28px; height: 28px; color: #7C3AED;"></i>
      </div>
      <h3>Calculette Intelligente</h3>
      <p>Ajustement automatique des proportions au gramme près en fonction du poids total de votre lot cosmétique.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">
        <i data-lucide="shield-alert" style="width: 28px; height: 28px; color: #7C3AED;"></i>
      </div>
      <h3>Sécurité & Compatibilité</h3>
      <p>Un moteur chimique intelligent vous alerte en temps réel en cas d'incompatibilité d'actifs (ex: Vitamine C + Rétinol).</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon">
        <i data-lucide="coins" style="width: 28px; height: 28px; color: #7C3AED;"></i>
      </div>
      <h3>Coût de revient</h3>
      <p>Évaluez en temps réel le coût de fabrication de vos préparations à partir du prix au kilo de vos ingrédients.</p>
    </div>
  </section>

  <section class="roadmap-section" id="roadmap-section">
    <div class="roadmap-container">
      <div class="roadmap-badge">SIMPLE & RAPIDE</div>
      <h2 class="roadmap-title">Comment ça marche ?</h2>
      <p class="roadmap-subtitle">3 étapes pour formuler vos produits cosmétiques.</p>
      
      <div class="roadmap-steps">
        <div class="roadmap-step">
          <div class="step-circle-wrapper">
            <div class="step-circle">1</div>
          </div>
          <h3 class="step-title">Choisissez</h3>
          <p class="step-desc">Sélectionnez le type de produit (Skincare ou Haircare) et ajoutez vos matières premières dans la bibliothèque.</p>
        </div>
        
        <div class="roadmap-step">
          <div class="step-circle-wrapper">
            <div class="step-circle">2</div>
          </div>
          <h3 class="step-title">Formulez</h3>
          <p class="step-desc">Ajustez vos pourcentages. Le moteur calcule au gramme près et signale les éventuelles incompatibilités.</p>
        </div>
        
        <div class="roadmap-step">
          <div class="step-circle-wrapper">
            <div class="step-circle">3</div>
          </div>
          <h3 class="step-title">Produisez</h3>
          <p class="step-desc">Validez votre composition et générez instantanément votre fiche de fabrication au format PDF.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="pricing-section" id="pricing-section" style="padding: 80px 20px; background: linear-gradient(180deg, #F8F5FF 0%, #FFFFFF 100%); color: #1C0F32; position: relative;">
    
    <div class="pricing-container" style="max-width: 1140px; margin: 0 auto; text-align: center; position: relative; z-index: 2;">
      
      <!-- Badge Essai Gratuit -->
      <div style="margin-bottom: 16px;">
        <span style="background: #F3E8FF; color: #7C3AED; font-size: 13px; font-weight: 800; padding: 8px 22px; border-radius: 30px; text-transform: uppercase; letter-spacing: 0.8px; display: inline-block; box-shadow: 0 2px 10px rgba(124, 58, 237, 0.1);">
          ESSAI GRATUIT DE 3 JOURS · SANS ENGAGEMENT
        </span>
      </div>

      <h2 class="pricing-title" style="font-size: clamp(28px, 4.5vw, 42px); font-weight: 900; color: #1C0F32; margin-bottom: 12px; letter-spacing: -0.5px;">
        Des formules adaptées à vos besoins
      </h2>
      <p style="color: #4B5563; font-size: 16px; max-width: 580px; margin: 0 auto 50px; line-height: 1.6; font-weight: 500;">
        Accédez à la calculette de formulation cosmétique, au moteur anti-conflits et au générateur de fiches PDF.
      </p>

      <!-- Cartes de prix -->
      <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 28px; align-items: stretch; margin-top: 20px;">
        
        <!-- Carte 1 Mois -->
        <div style="flex: 1; min-width: 280px; max-width: 350px; background: #FFFFFF; border: none; border-radius: 24px; padding: 36px 28px; display: flex; flex-direction: column; justify-content: space-between; text-align: left; box-shadow: 0 10px 30px rgba(0,0,0,0.06); transition: transform 0.3s ease;">
          <div>
            <div style="font-weight: 800; font-size: 13px; color: #7C3AED; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 12px;">1 MOIS</div>
            
            <div style="display: flex; align-items: baseline; gap: 8px; margin-bottom: 4px;">
              <span style="font-size: 42px; font-weight: 900; color: #1C0F32; line-height: 1;">2 900</span>
              <span style="font-size: 18px; font-weight: 800; color: #1C0F32;">FCFA</span>
            </div>
            <div style="font-size: 13px; color: #6B7280; margin-bottom: 24px; font-weight: 600;">2 900 FCFA / mois</div>

            <hr style="border: 0; border-top: 1px solid #F3F4F6; margin-bottom: 24px;" />

            <ul style="list-style: none; padding: 0; margin: 0 0 28px; display: flex; flex-direction: column; gap: 14px;">
              <li style="display: flex; align-items: center; gap: 12px; font-size: 14px; color: #374151; font-weight: 500;">
                <i data-lucide="check-circle-2" style="width: 18px; height: 18px; color: #7C3AED; flex-shrink: 0;"></i> Calculette Miracle Lab illimitée
              </li>
              <li style="display: flex; align-items: center; gap: 12px; font-size: 14px; color: #374151; font-weight: 500;">
                <i data-lucide="check-circle-2" style="width: 18px; height: 18px; color: #7C3AED; flex-shrink: 0;"></i> Détection des conflits d'actifs
              </li>
              <li style="display: flex; align-items: center; gap: 12px; font-size: 14px; color: #374151; font-weight: 500;">
                <i data-lucide="check-circle-2" style="width: 18px; height: 18px; color: #7C3AED; flex-shrink: 0;"></i> Export Fiches de fabrication PDF
              </li>
              <li style="display: flex; align-items: center; gap: 12px; font-size: 14px; color: #374151; font-weight: 500;">
                <i data-lucide="check-circle-2" style="width: 18px; height: 18px; color: #7C3AED; flex-shrink: 0;"></i> Support technique par chat
              </li>
            </ul>
          </div>

          <a href="/register" style="background: #F3E8FF; color: #7C3AED; padding: 14px 20px; border-radius: 14px; font-weight: 700; font-size: 14px; text-decoration: none; display: block; text-align: center; transition: all 0.2s ease;">
            Essayer gratuitement (3 jours)
          </a>
        </div>

        <!-- Carte 3 Mois (POPULAIRE - FEATURED DARK PURPLE & GOLD HERO CARD) -->
        <div style="flex: 1; min-width: 280px; max-width: 360px; background: linear-gradient(145deg, #1C0F32 0%, #2A174A 100%); border: none; border-radius: 24px; padding: 40px 28px 36px; display: flex; flex-direction: column; justify-content: space-between; text-align: left; position: relative; box-shadow: 0 20px 50px rgba(28, 15, 50, 0.35); transform: scale(1.03); z-index: 3;">
          
          <!-- Badge Flottant Top -->
          <div style="position: absolute; top: -16px; left: 50%; transform: translateX(-50%); background: linear-gradient(135deg, #FFD700, #F59E0B); color: #1A1000; font-size: 11px; font-weight: 900; padding: 5px 18px; border-radius: 20px; text-transform: uppercase; letter-spacing: 1px; box-shadow: 0 4px 14px rgba(255, 215, 0, 0.4); white-space: nowrap;">
            POPULAIRE · ÉCONOMIE -32%
          </div>

          <div>
            <div style="font-weight: 800; font-size: 13px; color: #FFD700; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 12px;">3 MOIS</div>
            
            <div style="display: flex; align-items: baseline; gap: 8px; margin-bottom: 4px;">
              <span style="font-size: 44px; font-weight: 900; color: #FFD700; line-height: 1; text-shadow: 0 2px 10px rgba(255,215,0,0.3);">5 900</span>
              <span style="font-size: 18px; font-weight: 800; color: #FFD700;">FCFA</span>
            </div>
            <div style="font-size: 13px; color: #FDE047; margin-bottom: 24px; font-weight: 700;">soit 1 967 FCFA / mois</div>

            <hr style="border: 0; border-top: 1px solid rgba(255, 215, 0, 0.25); margin-bottom: 24px;" />

            <ul style="list-style: none; padding: 0; margin: 0 0 28px; display: flex; flex-direction: column; gap: 14px;">
              <li style="display: flex; align-items: center; gap: 12px; font-size: 14px; color: #FFFFFF; font-weight: 600;">
                <i data-lucide="check-circle-2" style="width: 18px; height: 18px; color: #FFD700; flex-shrink: 0;"></i> Calculette Miracle Lab illimitée
              </li>
              <li style="display: flex; align-items: center; gap: 12px; font-size: 14px; color: #FFFFFF; font-weight: 600;">
                <i data-lucide="check-circle-2" style="width: 18px; height: 18px; color: #FFD700; flex-shrink: 0;"></i> Détection des conflits d'actifs
              </li>
              <li style="display: flex; align-items: center; gap: 12px; font-size: 14px; color: #FFFFFF; font-weight: 600;">
                <i data-lucide="check-circle-2" style="width: 18px; height: 18px; color: #FFD700; flex-shrink: 0;"></i> Export Fiches de fabrication PDF
              </li>
              <li style="display: flex; align-items: center; gap: 12px; font-size: 14px; color: #FFFFFF; font-weight: 600;">
                <i data-lucide="check-circle-2" style="width: 18px; height: 18px; color: #FFD700; flex-shrink: 0;"></i> Support technique par chat
              </li>
            </ul>
          </div>

          <a href="/register" style="background: linear-gradient(135deg, #FFD700 0%, #F59E0B 100%); color: #1A1000; padding: 15px 20px; border-radius: 14px; font-weight: 900; font-size: 15px; text-decoration: none; display: block; text-align: center; box-shadow: 0 6px 20px rgba(255, 215, 0, 0.4); transition: transform 0.2s ease;">
            Essayer gratuitement (3 jours)
          </a>
        </div>

        <!-- Carte 1 An -->
        <div style="flex: 1; min-width: 280px; max-width: 350px; background: #FFFFFF; border: none; border-radius: 24px; padding: 36px 28px; display: flex; flex-direction: column; justify-content: space-between; text-align: left; position: relative; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06); transition: transform 0.3s ease;">
          
          <!-- Badge Flottant Top -->
          <div style="position: absolute; top: -14px; left: 50%; transform: translateX(-50%); background: #10B981; color: #FFFFFF; font-size: 11px; font-weight: 800; padding: 4px 14px; border-radius: 20px; text-transform: uppercase; letter-spacing: 1px; white-space: nowrap;">
            MEILLEURE OFFRE · -48%
          </div>

          <div>
            <div style="font-weight: 800; font-size: 13px; color: #059669; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 12px;">1 AN (12 MOIS)</div>
            
            <div style="display: flex; align-items: baseline; gap: 8px; margin-bottom: 4px;">
              <span style="font-size: 42px; font-weight: 900; color: #1C0F32; line-height: 1;">17 900</span>
              <span style="font-size: 18px; font-weight: 800; color: #1C0F32;">FCFA</span>
            </div>
            <div style="font-size: 13px; color: #059669; margin-bottom: 24px; font-weight: 600;">soit 1 492 FCFA / mois</div>

            <hr style="border: 0; border-top: 1px solid #ECFDF5; margin-bottom: 24px;" />

            <ul style="list-style: none; padding: 0; margin: 0 0 28px; display: flex; flex-direction: column; gap: 14px;">
              <li style="display: flex; align-items: center; gap: 12px; font-size: 14px; color: #374151; font-weight: 500;">
                <i data-lucide="check-circle-2" style="width: 18px; height: 18px; color: #10B981; flex-shrink: 0;"></i> Calculette Miracle Lab illimitée
              </li>
              <li style="display: flex; align-items: center; gap: 12px; font-size: 14px; color: #374151; font-weight: 500;">
                <i data-lucide="check-circle-2" style="width: 18px; height: 18px; color: #10B981; flex-shrink: 0;"></i> Détection des conflits d'actifs
              </li>
              <li style="display: flex; align-items: center; gap: 12px; font-size: 14px; color: #374151; font-weight: 500;">
                <i data-lucide="check-circle-2" style="width: 18px; height: 18px; color: #10B981; flex-shrink: 0;"></i> Export Fiches de fabrication PDF
              </li>
              <li style="display: flex; align-items: center; gap: 12px; font-size: 14px; color: #374151; font-weight: 500;">
                <i data-lucide="check-circle-2" style="width: 18px; height: 18px; color: #10B981; flex-shrink: 0;"></i> Support technique par chat
              </li>
            </ul>
          </div>

          <a href="/register" style="background: #ECFDF5; color: #059669; padding: 14px 20px; border-radius: 14px; font-weight: 700; font-size: 14px; text-decoration: none; display: block; text-align: center; transition: all 0.2s ease;">
            Essayer gratuitement (3 jours)
          </a>
        </div>

      </div>
    </div>
  </section>

  <footer style="padding: 40px 20px; border-top: 1px solid rgba(124, 58, 237, 0.1); background: #ffffff; text-align: center;">
    <div style="display: flex; flex-direction: column; align-items: center; gap: 15px;">
      <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
        <img src="/logo.png.png" alt="Miracle Lab Logo" style="height: 100px; width: auto; transform: scale(1.4); mix-blend-mode: darken;" />
      </div>
      <div style="font-size: 13px; color: #64748B; font-weight: 500;">© 2026 The Miracle Lab. Tous droits réservés.</div>
    </div>
  </footer>

  @vite(['resources/js/shared.js'])
  <script>
    // Event listeners
    document.getElementById("btn-goto-login")?.addEventListener("click", () => { window.location.href = "/login"; });
    document.getElementById("nav-pricing")?.addEventListener("click", () => { document.getElementById("pricing-section")?.scrollIntoView({ behavior: 'smooth' }); });
    document.getElementById("btn-hero-cta")?.addEventListener("click", () => { window.location.href = "/register"; });
    document.getElementById("btn-pricing-buy")?.addEventListener("click", () => { window.location.href = "/register"; });

    // Mobile Hamburger Menu
    const hamburger = document.getElementById("landing-hamburger");
    const mobileMenu = document.getElementById("landing-mobile-menu");
    const mobileClose = document.getElementById("landing-mobile-close");
    
    hamburger?.addEventListener("click", () => { mobileMenu?.classList.add("open"); });
    mobileClose?.addEventListener("click", () => { mobileMenu?.classList.remove("open"); });
    mobileMenu?.addEventListener("click", (e) => { if (e.target === mobileMenu) mobileMenu.classList.remove("open"); });

    // Close drawer when clicking a link and scroll smoothly
    document.querySelectorAll(".landing-mobile-link").forEach(link => {
      link.addEventListener("click", (e) => {
        mobileMenu?.classList.remove("open");
        const href = link.getAttribute("href");
        if (href && href.startsWith("#")) {
          e.preventDefault();
          document.querySelector(href)?.scrollIntoView({ behavior: 'smooth' });
        }
      });
    });
  </script>
  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>
  <script>
    if (window.lucide) {
      lucide.createIcons();
    }
  </script>
</body>
</html>
