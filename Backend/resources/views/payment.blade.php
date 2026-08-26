<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Choix de l'Abonnement · Miss Miracle</title>
  @vite(['resources/css/style.css'])
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    .plan-card {
      border: 2px solid #E5E7EB;
      border-radius: 16px;
      padding: 18px 20px;
      margin-bottom: 14px;
      cursor: pointer;
      transition: all 0.2s ease;
      display: flex;
      align-items: center;
      justify-content: space-between;
      text-align: left;
      position: relative;
      background: #FAFAFA;
    }
    .plan-card:hover {
      border-color: #A855F7;
      background: #FDF4FF;
    }
    .plan-card.selected {
      border-color: #9333EA;
      background: #F5F3FF;
      box-shadow: 0 4px 14px rgba(147, 51, 234, 0.15);
    }
    .plan-badge {
      position: absolute;
      top: -10px;
      right: 18px;
      background: linear-gradient(135deg, #9333EA, #7E22CE);
      color: #FFF;
      font-size: 11px;
      font-weight: 700;
      padding: 3px 10px;
      border-radius: 20px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .radio-circle {
      width: 22px;
      height: 22px;
      border-radius: 50%;
      border: 2px solid #D1D5DB;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s ease;
      flex-shrink: 0;
    }
    .plan-card.selected .radio-circle {
      border-color: #9333EA;
      background-color: #9333EA;
    }
    .radio-circle-inner {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: #FFF;
      display: none;
    }
    .plan-card.selected .radio-circle-inner {
      display: block;
    }
  </style>
</head>
<body style="background: linear-gradient(135deg, #F3F0FF 0%, #EDE9FE 50%, #F5F3FF 100%); min-height: 100vh; display:flex; flex-direction:column; align-items:center; justify-content:center; padding:20px; font-family:'Plus Jakarta Sans',sans-serif;">
  
  <div style="background:#fff; border-radius:24px; padding:36px 32px; box-shadow:0 20px 40px rgba(0,0,0,0.1); max-width:480px; width:100%; text-align:center;">
    
    <div style="margin-bottom: 24px;">
      <span style="background: #F3E8FF; color: #7E22CE; font-size: 13px; font-weight: 700; padding: 6px 14px; border-radius: 20px;">Accès Miracle Lab</span>
      <h2 style="color:#1C0F32; font-size:24px; font-weight:800; margin-top:12px; margin-bottom:8px;">Choisissez votre formule</h2>
      <p style="color:#6B7280; font-size:14px; line-height:1.5;">Débloquez la création illimitée de formules cosmétiques et l'accès complet au laboratoire.</p>
    </div>

    <!-- Options de Plan -->
    <div style="margin-bottom: 24px;">
      
      <!-- Option 1 Mois -->
      <div class="plan-card" data-plan="1_mois" data-price="2 900 FCFA">
        <div style="display:flex; align-items:center; gap:14px;">
          <div class="radio-circle"><div class="radio-circle-inner"></div></div>
          <div>
            <div style="font-weight:700; font-size:15px; color:#1F2937;">1 Mois</div>
            <div style="font-size:12px; color:#6B7280;">Flexibilité mensuelle</div>
          </div>
        </div>
        <div style="text-align:right;">
          <div style="font-weight:800; font-size:16px; color:#1C0F32;">2 900 FCFA</div>
          <div style="font-size:11px; color:#6B7280;">2 900 FCFA / mois</div>
        </div>
      </div>

      <!-- Option 3 Mois (Sélectionné par défaut) -->
      <div class="plan-card selected" data-plan="3_mois" data-price="5 900 FCFA">
        <span class="plan-badge">Populaire</span>
        <div style="display:flex; align-items:center; gap:14px;">
          <div class="radio-circle"><div class="radio-circle-inner"></div></div>
          <div>
            <div style="font-weight:700; font-size:15px; color:#1F2937;">3 Mois</div>
            <div style="font-size:12px; color:#059669; font-weight:600;">Économisez ~32%</div>
          </div>
        </div>
        <div style="text-align:right;">
          <div style="font-weight:800; font-size:16px; color:#9333EA;">5 900 FCFA</div>
          <div style="font-size:11px; color:#6B7280;">1 967 FCFA / mois</div>
        </div>
      </div>

      <!-- Option 1 An -->
      <div class="plan-card" data-plan="12_mois" data-price="17 900 FCFA">
        <span class="plan-badge" style="background: linear-gradient(135deg, #059669, #047857);">Meilleure Offre</span>
        <div style="display:flex; align-items:center; gap:14px;">
          <div class="radio-circle"><div class="radio-circle-inner"></div></div>
          <div>
            <div style="font-weight:700; font-size:15px; color:#1F2937;">1 An (12 mois)</div>
            <div style="font-size:12px; color:#059669; font-weight:600;">Économisez ~48%</div>
          </div>
        </div>
        <div style="text-align:right;">
          <div style="font-weight:800; font-size:16px; color:#1C0F32;">17 900 FCFA</div>
          <div style="font-size:11px; color:#6B7280;">1 492 FCFA / mois</div>
        </div>
      </div>

    </div>
    
    <div id="payment-status-message" style="display:none; margin-bottom:20px; padding:12px; border-radius:12px; font-size:14px; font-weight:600; background:#EFF6FF; color:#1D4ED8; border:1px solid #BFDBFE;">
      ⏳ Préparation du paiement...
    </div>

    <button id="btn-reopen-paytech" style="width:100%; background: linear-gradient(135deg, #9333EA 0%, #7E22CE 100%); color:white; padding:16px; border:none; border-radius:14px; font-weight:700; font-size:16px; cursor:pointer; box-shadow:0 4px 14px rgba(147,51,234,0.3); transition: transform 0.1s ease;">
      Activer la formule (5 900 FCFA)
    </button>
    
    <form action="{{ route('logout') }}" method="POST" style="margin-top:24px;">
        @csrf
        <button type="submit" style="background:none; border:none; color:#6B7280; font-size:14px; text-decoration:underline; cursor:pointer;">Se déconnecter</button>
    </form>
  </div>

  <script src="https://paytech.sn/cdn/paytech.min.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const statusMsg = document.getElementById("payment-status-message");
      const reopenBtn = document.getElementById("btn-reopen-paytech");
      const planCards = document.querySelectorAll(".plan-card");
      const csrfToken = "{{ csrf_token() }}";
      
      let selectedPlan = "3_mois";
      let selectedPrice = "5 900 FCFA";

      // Sélection des cartes de plan
      planCards.forEach(card => {
        card.addEventListener("click", function() {
          planCards.forEach(c => c.classList.remove("selected"));
          this.classList.add("selected");
          selectedPlan = this.getAttribute("data-plan");
          selectedPrice = this.getAttribute("data-price");
          reopenBtn.innerHTML = `Activer la formule (${selectedPrice})`;
        });
      });

      function triggerPayment() {
        statusMsg.style.display = "block";
        reopenBtn.style.display = "none";
        statusMsg.style.background = "#EFF6FF";
        statusMsg.style.color = "#1D4ED8";
        statusMsg.style.border = "1px solid #BFDBFE";
        statusMsg.innerHTML = `Initialisation du paiement (${selectedPrice})...`;

        fetch("/paytech/process", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": csrfToken,
            "ngrok-skip-browser-warning": "true"
          },
          body: JSON.stringify({
            plan: selectedPlan
          })
        })
        .then(async r => {
          const text = await r.text();
          if (!r.ok) throw new Error("Erreur HTTP " + r.status);
          return JSON.parse(text);
        })
        .then(data => {
          if (data.success && data.redirect_url) {
            statusMsg.style.background = "#FEF3C7";
            statusMsg.style.color = "#92400E";
            statusMsg.style.border = "1px solid #FCD34D";
            statusMsg.innerHTML = "Veuillez patienter, validation de votre transaction en cours...";

            try {
              (new PayTech()).withOptions({
                tokenUrl: data.redirect_url,
                presentationMode: PayTech.OPEN_IN_POPUP
              }).send();
            } catch(err) {
              window.location.href = data.redirect_url;
              return;
            }

            // Polling de confirmation
            const ref = data.reference;
            if (ref) {
              const pollId = setInterval(() => {
                fetch(`/payment/status/${ref}`, { headers: { "Accept": "application/json", "ngrok-skip-browser-warning": "true" } })
                  .then(r => r.json())
                  .then(st => {
                    if (st.status === "REUSSI") {
                      clearInterval(pollId);
                      statusMsg.style.background = "#D1FAE5";
                      statusMsg.style.color = "#065F46";
                      statusMsg.style.border = "1px solid #6EE7B7";
                      statusMsg.innerHTML = "Paiement confirmé ! Ouverture de votre espace...";
                      setTimeout(() => { window.location.href = '/dashboard'; }, 300);
                    } else if (st.status === "ECHOUE" || st.status === "ANNULE") {
                      clearInterval(pollId);
                      statusMsg.style.background = "#FEE2E2";
                      statusMsg.style.color = "#991B1B";
                      statusMsg.style.border = "1px solid #FCA5A5";
                      statusMsg.innerHTML = "Le paiement a échoué ou a été annulé.";
                      setTimeout(() => { reopenBtn.style.display = "block"; }, 1000);
                    }
                  }).catch(() => {});
              }, 1500);
            }

          } else {
            statusMsg.style.background = "#FEE2E2";
            statusMsg.style.color = "#991B1B";
            statusMsg.style.border = "1px solid #FCA5A5";
            statusMsg.innerHTML = `Erreur : ${data.message || 'Erreur lors du paiement'}`;
            reopenBtn.style.display = "block";
          }
        })
        .catch(err => {
          statusMsg.style.background = "#FEE2E2";
          statusMsg.style.color = "#991B1B";
          statusMsg.style.border = "1px solid #FCA5A5";
          statusMsg.innerHTML = "Erreur de connexion.";
          reopenBtn.style.display = "block";
        });
      }

      reopenBtn.addEventListener("click", triggerPayment);
    });
  </script>
</body>
</html>
