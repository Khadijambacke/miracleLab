<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion · Miss Miracle</title>
  @vite(['resources/css/style.css'])
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
    rel="stylesheet">
</head>

<body style="background: linear-gradient(135deg, #F3F0FF 0%, #EDE9FE 50%, #F5F3FF 100%); min-height: 100vh;">

  <div class="auth-overlay" style="position: static; min-height: 100vh; padding: 40px 20px;">
    <div class="auth-card">
      <div class="auth-hdr">
        <div class="auth-title">Connexion</div>
        <div class="auth-sub">Accédez à votre laboratoire</div>
      </div>
      
      @if ($errors->any())
        <div style="background:#FEE2E2; border-radius: 8px; padding:12px; color:#B91C1C; margin-bottom: 20px; font-size: 13px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
      @endif

      <form action="{{ route('login.post') }}" method="POST">
        @csrf
        <div class="auth-group">
          <label class="auth-label" for="email">Adresse E-mail</label>
          <input class="auth-input-field" id="email" name="email" type="email" placeholder="nom@exemple.com" value="{{ old('email') }}" required />
        </div>
        <div class="auth-group">
          <label class="auth-label" for="password">Mot de passe</label>
          <input class="auth-input-field" id="password" name="password" type="password" placeholder="••••••••" required />
        </div>
        <button type="submit" class="btn-auth-submit">Se connecter</button>
      </form>

      <a href="/register" class="auth-footer-link" style="display:block; margin-top:20px; text-align:center;">Pas encore membre ? S'inscrire</a>

      <div
        style="background:#F3F4F6; border-radius: 12px; padding:14px; font-size:11px; color:#4B5563; line-height:1.5; margin-top: 15px;">
        💡 <b>Comptes de démo :</b><br />
        • Client : <code>awa.diop@example.com</code> (mdp: <code>client123</code>)<br />
        • Admin : <code>admin@example.com</code> (mdp: <code>admin123</code>)
      </div>
    </div>
  </div>

</body>
</html>