<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Accès Réservé · Miss Miracle</title>
  @vite(['resources/css/style.css'])
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body style="background: linear-gradient(135deg, #F3F0FF 0%, #EDE9FE 50%, #F5F3FF 100%); min-height: 100vh; display:flex; flex-direction:column; align-items:center; justify-content:center; padding:20px; font-family:'Plus Jakarta Sans',sans-serif;">
  
  <div style="background:#fff; border-radius:24px; padding:40px; box-shadow:0 20px 40px rgba(0,0,0,0.1); max-width:450px; width:100%; text-align:center;">
    <h2 style="color:#1C0F32; font-size:24px; font-weight:800; margin-bottom:15px;">Accès Réservé</h2>
    <p style="color:#6B7280; margin-bottom:30px; font-size:15px; line-height:1.5;">Pour accéder au Miracle Lab et commencer à formuler, vous devez activer votre abonnement (15 000 FCFA).</p>
    
    <div id="payment-status-message" style="margin-bottom:20px; padding:12px; border-radius:12px; font-size:14px; font-weight:600; background:#EFF6FF; color:#1D4ED8; border:1px solid #BFDBFE;">
      ⏳ Préparation du paiement...
    </div>

    <button id="btn-reopen-paytech" style="display:none; width:100%; background:#059669; color:white; padding:16px; border:none; border-radius:14px; font-weight:700; font-size:16px; cursor:pointer; box-shadow:0 4px 12px rgba(5,150,105,0.2);">
      💳 Payer 15 000 FCFA
    </button>
    
    <form action="{{ route('logout') }}" method="POST" style="margin-top:30px;">
        @csrf
        <button type="submit" style="background:none; border:none; color:#6B7280; font-size:14px; text-decoration:underline; cursor:pointer;">Se déconnecter</button>
    </form>
  </div>

  <script src="https://paytech.sn/cdn/paytech.min.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const statusMsg = document.getElementById("payment-status-message");
      const reopenBtn = document.getElementById("btn-reopen-paytech");
      const csrfToken = "{{ csrf_token() }}";

      function triggerPayment() {
        statusMsg.style.display = "block";
        reopenBtn.style.display = "none";
        statusMsg.style.background = "#EFF6FF";
        statusMsg.style.color = "#1D4ED8";
        statusMsg.style.border = "1px solid #BFDBFE";
        statusMsg.innerHTML = "⏳ Initialisation PayTech...";

        fetch("/paytech/process", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": csrfToken,
            "ngrok-skip-browser-warning": "true"
          }
        })
        .then(async r => {
          const text = await r.text();
          if (!r.ok) throw new Error("Erreur HTTP " + r.status);
          return JSON.parse(text);
        })
        .then(data => {
          if (data.success && data.redirect_url) {
            statusMsg.style.background = "#D1FAE5";
            statusMsg.style.color = "#065F46";
            statusMsg.innerHTML = "✅ Guichet de paiement ouvert !";

            try {
              (new PayTech()).withOptions({
                tokenUrl: data.redirect_url,
                presentationMode: PayTech.OPEN_IN_POPUP
              }).send();
            } catch(err) {
              window.location.href = data.redirect_url;
              return;
            }

            // Polling
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
                      statusMsg.innerHTML = "🎉 Paiement confirmé ! Redirection...";
                      setTimeout(() => { window.location.href = '/dashboard'; }, 1500);
                    }
                  }).catch(() => {});
              }, 3000);
            }

            setTimeout(() => { reopenBtn.style.display = "block"; }, 5000);
          } else {
            statusMsg.innerHTML = `❌ ${data.message || 'Erreur PayTech'}`;
            reopenBtn.style.display = "block";
          }
        })
        .catch(err => {
          statusMsg.style.background = "#FEE2E2";
          statusMsg.style.color = "#991B1B";
          statusMsg.style.border = "1px solid #FCA5A5";
          statusMsg.innerHTML = "❌ " + err.message;
          reopenBtn.style.display = "block";
        });
      }

      // Auto-trigger au chargement de la page
      setTimeout(triggerPayment, 500);

      reopenBtn.addEventListener("click", triggerPayment);
    });
  </script>
</body>
</html>
