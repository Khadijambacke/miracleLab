<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Paiement PayTech · Miss Miracle</title>
  @vite(['resources/css/style.css'])
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    .auth-card {
      padding: 24px !important;
      gap: 16px !important;
      border-radius: 20px !important;
      box-shadow: 0 12px 32px rgba(124, 58, 237, 0.06) !important;
    }
    .paydunya-panel {
      gap: 14px !important;
    }
    .payment-methods-grid {
      gap: 8px !important;
      margin-bottom: 10px !important;
    }
    .pay-method-card {
      padding: 10px 8px !important;
      gap: 4px !important;
      border-radius: 12px !important;
    }
    .pay-method-logo {
      height: 24px !important;
      margin-bottom: 0 !important;
    }
    .pay-method-logo svg {
      height: 24px !important;
      width: auto !important;
    }
    .btn-auth-submit {
      padding: 12px !important;
      font-size: 14px !important;
      border-radius: 10px !important;
    }
    .btn-cancel {
      padding: 10px !important;
      font-size: 13px !important;
      border-radius: 10px !important;
      margin-top: 5px !important;
    }
    .auth-label {
      font-size: 11px !important;
      margin-bottom: 4px !important;
    }
    .auth-input-field {
      padding: 10px 12px !important;
      font-size: 13px !important;
      border-radius: 8px !important;
    }
  </style>
</head>
<body style="background: linear-gradient(135deg, #F3F0FF 0%, #EDE9FE 50%, #F5F3FF 100%); min-height: 100vh;">

  <div class="auth-overlay" style="position: static; min-height: 100vh; padding: 40px 20px;">
    <div class="auth-card" style="max-width: 480px;" id="payment-card-body">
      <div class="paydunya-header" style="background: linear-gradient(135deg, #059669 0%, #047857 100%); color:#fff; padding:12px 16px; border-radius:12px; font-weight:800; display:flex; align-items:center; gap:8px; margin-bottom:15px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        <span>PAYTECH SECURE PAYMENT (MODE SANDBOX)</span>
      </div>
      
      <div class="paydunya-panel" id="paydunya-panel">
        <div style="display:flex; justify-content:space-between; border-bottom:1px solid #E5E7EB; padding-bottom:10px; margin-bottom: 14px;">
          <span style="color:#4B5563; font-weight:600">Formule Accès Lab</span>
          <span style="font-weight:800; color:#111827">15 000 FCFA</span>
        </div>
        
        <div class="auth-label" style="margin-bottom: 10px;">Choisir un moyen de paiement (Wave, Orange Money, Free, Carte)</div>
        <div class="payment-methods-grid" style="margin-bottom: 20px;">
          <div class="pay-method-card selected" data-method="wave">
            <span class="pay-method-logo">
              <svg width="48" height="32" viewBox="0 0 100 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="100" height="64" rx="8" fill="#1D9BF0"/>
                <path d="M50 14C42 14 36 20 36 28C36 33.6 38.4 37.6 42.4 40.8C42.4 43.2 40 45.6 36.8 47.2C42.4 47.2 47.2 44 48.8 41.6C49.6 41.6 50.4 41.6 50.4 41.6C50.4 41.6 51.2 41.6 52 41.6C53.6 44 58.4 47.2 64 47.2C60.8 45.6 58.4 43.2 58.4 40.8C62.4 37.6 64.8 33.6 64.8 28C64.8 20 58.8 14 50 14Z" fill="white"/>
                <circle cx="44.4" cy="26" r="2.4" fill="#1D9BF0"/>
                <circle cx="55.6" cy="26" r="2.4" fill="#1D9BF0"/>
                <path d="M50 30.8L46 33.2H54L50 30.8Z" fill="#FFA500"/>
              </svg>
            </span>
            <span>Wave</span>
          </div>
          <div class="pay-method-card" data-method="om">
            <span class="pay-method-logo">
              <svg width="48" height="32" viewBox="0 0 48 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="48" height="32" rx="6" fill="#FF6600"/>
                <text x="6" y="22" fill="white" font-family="'Plus Jakarta Sans', Arial, sans-serif" font-weight="900" font-size="10" letter-spacing="-0.3px">orange</text>
              </svg>
            </span>
            <span>Orange Money</span>
          </div>
          <div class="pay-method-card" data-method="card">
            <span class="pay-method-logo">
              <svg width="48" height="32" viewBox="0 0 60 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="2" y="10" width="26" height="20" rx="3" fill="#1A1F71"/>
                <text x="5" y="23" fill="white" font-family="sans-serif" font-weight="bold" font-style="italic" font-size="9">VISA</text>
                <rect x="32" y="10" width="26" height="20" rx="3" fill="#3A3A3A"/>
                <circle cx="41" cy="20" r="7" fill="#EB001B"/>
                <circle cx="49" cy="20" r="7" fill="#F79E1B" fill-opacity="0.8"/>
              </svg>
            </span>
            <span>Carte Bancaire</span>
          </div>
          <div class="pay-method-card" data-method="free">
            <span class="pay-method-logo">
              <svg width="48" height="32" viewBox="0 0 48 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="48" height="32" rx="6" fill="#E21A22"/>
                <text x="10" y="21" fill="white" font-family="Georgia, serif" font-style="italic" font-weight="bold" font-size="14">free</text>
              </svg>
            </span>
            <span>Free Money</span>
          </div>
        </div>
 
        @if ($errors->has('subscription'))
            <div style="background:#FEE2E2; color:#991B1B; border:1px solid #FCA5A5; padding:12px; border-radius:10px; font-size:12.5px; font-weight:700; margin-bottom:14px; text-align:center;">
                {{ $errors->first('subscription') }}
            </div>
        @endif

        @if(session('success'))
            <div style="background:#D1FAE5; color:#065F46; border:1px solid #6EE7B7; padding:12px; border-radius:10px; font-size:13px; font-weight:700; margin-bottom:14px; text-align:center;">
                {{ session('success') }}
            </div>
        @endif

        <div id="payment-status-message" style="display:none; padding:12px; margin-top:0; margin-bottom:14px; border-radius:10px; font-size:13px; font-weight:700; text-align:center;"></div>

        <button type="button" id="btn-paytech-submit" style="width:100%; padding:15px; background:linear-gradient(135deg,#059669,#047857); color:#fff; border:none; border-radius:12px; font-weight:800; font-size:15px; cursor:pointer; box-shadow:0 4px 16px rgba(5,150,105,0.3); margin-bottom:12px; letter-spacing:0.3px;">
          💳 Payer 15 000 FCFA avec PayTech
        </button>

        @if(strtoupper(auth()->user()->statut_abonnement ?? '') !== 'ACTIF')
            <form action="{{ route('logout') }}" method="POST" style="margin-top: 4px;">
                @csrf
                <button type="submit" style="display:block; text-align:center; width:100%; border:1px solid #D1D5DB; padding:12px; border-radius:10px; background:none; cursor:pointer; color:#6B7280; font-weight:600; font-size:13px;">Se déconnecter</button>
            </form>
        @else
            <a href="{{ route('dashboard') }}" style="display:block; text-align:center; width:100%; border:1px solid #D1D5DB; padding:12px; margin-top:4px; text-decoration:none; color:inherit; border-radius:10px; font-weight:600; font-size:13px;">Accéder au tableau de bord</a>
        @endif
      </div>
    </div>
  </div>

  @vite(['resources/js/shared.js'])
  <script>
    // Sélection du moyen de paiement (visuel uniquement)
    document.querySelectorAll(".pay-method-card").forEach(card => {
      card.addEventListener("click", e => {
        document.querySelectorAll(".pay-method-card").forEach(c => c.classList.remove("selected"));
        e.currentTarget.classList.add("selected");
      });
    });

    // PayTech Paiement
    const paytechBtn = document.getElementById("btn-paytech-submit");
    const statusMsg  = document.getElementById("payment-status-message");
    const csrfToken  = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    paytechBtn?.addEventListener("click", function() {
      paytechBtn.disabled = true;
      paytechBtn.innerText = "⏳ Initialisation PayTech...";
      statusMsg.style.display = "block";
      statusMsg.style.background = "#EFF6FF";
      statusMsg.style.color = "#1D4ED8";
      statusMsg.style.border = "1px solid #BFDBFE";
      statusMsg.innerHTML = "⏳ Redirection vers le guichet de paiement PayTech...";

      fetch("/paytech/process", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "Accept": "application/json",
          "X-CSRF-TOKEN": csrfToken
        }
      })
      .then(r => r.json())
      .then(data => {
        if (data.success && data.redirect_url) {
          statusMsg.style.background = "#D1FAE5";
          statusMsg.style.color = "#065F46";
          statusMsg.innerHTML = "✅ Redirection vers PayTech...";
          window.location.href = data.redirect_url;
        } else {
          paytechBtn.disabled = false;
          paytechBtn.innerText = "💳 Payer 15 000 FCFA avec PayTech";
          statusMsg.style.background = "#FEE2E2";
          statusMsg.style.color = "#991B1B";
          statusMsg.style.border = "1px solid #FCA5A5";
          statusMsg.innerHTML = `❌ ${data.message || 'Erreur PayTech'}`;
        }
      })
      .catch(err => {
        console.error("PayTech fetch error:", err);
        paytechBtn.disabled = false;
        paytechBtn.innerText = "💳 Payer 15 000 FCFA avec PayTech";
        statusMsg.style.background = "#FEE2E2";
        statusMsg.style.color = "#991B1B";
        statusMsg.style.border = "1px solid #FCA5A5";
        statusMsg.innerHTML = "❌ Erreur de connexion. Vérifiez votre réseau.";
      });
    });
  </script>
</body>
</html>
