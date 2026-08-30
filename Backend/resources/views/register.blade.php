<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Créer mon compte · Miss Miracle</title>
  @vite(['resources/css/style.css'])
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body style="background: linear-gradient(135deg, #F3F0FF 0%, #EDE9FE 50%, #F5F3FF 100%); min-height: 100vh;">

  <div class="auth-overlay" style="position: static; min-height: 100vh; padding: 40px 20px;">
    <div class="auth-card">
      <div class="auth-hdr">
        <div class="auth-title">Créer mon compte</div>
        <div class="auth-sub">Entrez vos coordonnées pour accéder au Miracle Lab</div>
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

      <form action="{{ route('register.post') }}" method="POST">
        @csrf
        <div class="auth-group">
          <label class="auth-label">Nom Complet</label>
          <input class="auth-input-field" name="nom_complet" type="text" placeholder="Prénom Nom" value="{{ old('nom_complet') }}" required/>
        </div>
        <div class="auth-group">
          <label class="auth-label">Adresse E-mail</label>
          <input class="auth-input-field" name="email" type="email" placeholder="nom@exemple.com" value="{{ old('email') }}" required/>
        </div>
        <div class="auth-group">
          <label class="auth-label">Téléphone</label>
          <input class="auth-input-field" name="telephone" type="tel" placeholder="+221 77 123 45 67" value="{{ old('telephone') }}"/>
        </div>
        <div class="auth-grid-2">
            <div class="auth-group">
            <label class="auth-label">Mot de passe</label>
            <input class="auth-input-field" name="password" type="password" placeholder="••••••••" required/>
            </div>
            <div class="auth-group">
            <label class="auth-label">Confirmer</label>
            <input class="auth-input-field" name="password_confirmation" type="password" placeholder="••••••••" required/>
            </div>
        </div>
        <button type="submit" class="btn-auth-submit" style="margin-top: 15px;">S'inscrire (Essai gratuit 3 jours)</button>
      </form>
      <a href="{{ route('login') }}" class="auth-footer-link" style="display:block; margin-top:20px; text-align:center;">Déjà inscrit ? Se connecter</a>
    </div>
  </div>
</body>
</html>
