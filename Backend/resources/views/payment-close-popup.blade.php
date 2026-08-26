<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Traitement du paiement...</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
      background: #f8fafc;
      color: #334155;
    }
    .loader {
      border: 4px solid #e2e8f0;
      border-top: 4px solid #10b981;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      animation: spin 1s linear infinite;
      margin-bottom: 20px;
    }
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  </style>
</head>
<body>
  <div class="loader"></div>
  <h2>Paiement transmis !</h2>
  <p>Veuillez patienter, cette fenêtre va se fermer automatiquement...</p>
  <p style="font-size: 13px; color: #94a3b8; margin-top: 20px;">Si la fenêtre ne se ferme pas, vous pouvez la fermer manuellement.</p>

  <script>
    if (window.opener) {
        // C'est bien une popup
        setTimeout(function() {
            window.close();
        }, 1500);
    } else {
        // Fallback si on est dans l'onglet principal
        setTimeout(function() {
            window.location.href = "/dashboard";
        }, 2000);
    }
  </script>
</body>
</html>
