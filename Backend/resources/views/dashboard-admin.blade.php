<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>The Miracle Lab · Miss Miracle Cosmetics (Admin)</title>
  <script>
    window.LARAVEL_CLIENTS = @json($clients);
    window.LARAVEL_CHATS = @json($chats ?? []);
    window.LARAVEL_USER = @json($user);
    window.LARAVEL_STATS = @json($stats ?? []);
    window.LARAVEL_INGREDIENTS = @json($ingredients);
  </script>
  @vite(['resources/css/style.css'])
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
    rel="stylesheet">
  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
  <style>
    /* Admin-specific non-sidebar overrides only */
    body {
      background: #FAF7F2;
    }

    .dash-main {
      background: #FAF7F2;
    }

    /* Stat cards */
    .stat-card {
      background: #FFFFFF;
      border: 1px solid #F1EBE3;
      box-shadow: 0 4px 16px rgba(186, 126, 192, 0.05), 0 1px 3px rgba(0, 0, 0, 0.03);
      border-radius: 16px;
    }

    .stat-val {
      color: #221230;
      font-weight: 800;
    }

    .stat-lbl {
      color: #8E7E72;
    }

    /* Tables */
    .admin-table {
      border: 1px solid #F1EBE3;
    }

    .admin-table th {
      background: #FAF5F8;
      color: #9D174D;
      border-bottom: 1.5px solid #F1EBE3;
    }

    .admin-table td {
      border-bottom: 1px solid #FAF7F2;
      color: #4A3E3D;
    }

    /* Action buttons */
    .admin-btn-action.btn-toggle-sub {
      background: #A21CAF;
      color: #fff;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 600;
      padding: 6px 12px;
      border: none;
    }

    .admin-btn-action.btn-toggle-sub:hover {
      background: #86198F;
    }

    /* Chart */
    .admin-chart-box {
      background: #fff;
      border: 1px solid #F1EBE3;
      border-radius: 16px;
    }

    /* Chat */
    .admin-chat-layout {
      border: 1px solid #F1EBE3;
      background: #fff;
    }

    .admin-chat-users {
      border-right: 1px solid #F1EBE3;
      background: #FCFAF7;
    }

    .admin-chat-user-item {
      border-bottom: 1px solid #FAF7F2;
    }

    .admin-chat-user-item.active {
      background: #FAF3F6;
    }

    .admin-chat-user-item:hover {
      background: #FAF7F2;
    }

    .admin-chat-user-name {
      color: #20132B;
      font-weight: 700;
    }

    .admin-chat-pane-header {
      background: #FCFAF7;
      border-bottom: 1px solid #F1EBE3;
      color: #20132B;
    }

    .admin-chat-pane-messages {
      background: #FAF7F2;
    }

    #btn-admin-show-lib {
      background: #A21CAF;
    }

    #btn-admin-show-lib:hover {
      background: #86198F;
    }
  </style>
</head>

<body style="min-height: 100vh;">

  <div id="app"></div>

  <!-- Shared scripts -->
  <script>{!! file_get_contents(resource_path('js/shared.js')) !!}</script>
  <script>{!! file_get_contents(resource_path('js/calculator.js')) !!}</script>

  <script>
    // 1. Get authenticated user from Laravel
    const user = {
      id: {{ auth()->user()->id ?? 'null' }},
      first_name: "{{ explode(' ', auth()->user()->nom_complet ?? '')[0] ?? 'Admin' }}",
      last_name: "{{ count(explode(' ', auth()->user()->nom_complet ?? '')) > 1 ? implode(' ', array_slice(explode(' ', auth()->user()->nom_complet ?? ''), 1)) : '' }}",
      email: "{{ auth()->user()->email ?? '' }}",
      role: "{{ auth()->user()->role ?? 'ADMIN' }}",
      subscription_status: "{{ auth()->user()->statut_abonnement ?? 'ACTIF' }}"
    };

    // 2. Initialize local admin state
    let state = {
      currentUser: user,
      clients: loadClients(),
      chats: loadChats(),
      library: deepClone(LIBRARY),
      savedFormulas: loadSaved(),

      // Admin View Tab
      activeAdminTab: "stats", // 'stats' | 'clients' | 'library' | 'chat'
      activeInboxTab: "atraiter", // 'atraiter' | 'notifications'

      // Admin Chat Space
      adminSelectedChatEmail: "awa.diop@example.com",
      adminChatInput: "",
      adminMobileChatView: "list",

      // Library Modal additions
      showLibrary: false,
      showIngSheet: null,
      libTab: "AQUEUSE",
      libSearch: "",
      libAdding: false,
      libNewName: "", libNewNote: "", libNewPhase: "AQUEUSE"
    };

    function mergeDatabaseIngredients() {
      if (Array.isArray(window.LARAVEL_INGREDIENTS) && window.LARAVEL_INGREDIENTS.length > 0) {
        window.LARAVEL_INGREDIENTS.forEach(ing => {
          const rawPhase = (ing.phase || 'AQUEUSE').toUpperCase();
          const phase = (rawPhase === 'PHASE_A' || rawPhase === 'WATER') ? 'AQUEUSE' 
                      : (rawPhase === 'PHASE_B' || rawPhase === 'OIL') ? 'HUILEUSE' 
                      : (rawPhase === 'PHASE_C' || rawPhase === 'COOL_DOWN') ? 'REFROIDISSEMENT'
                      : rawPhase;
          
          if (!state.library[phase]) {
            state.library[phase] = {};
          }
          
          const groupName = ing.est_personnalise 
            ? "✨ Ingrédients Clients" 
            : (ing.nom_groupe || "📦 Base de Données");

          // Check if ingredient already exists in ANY group of this phase
          let exists = false;
          for (const g of Object.keys(state.library[phase])) {
            if (state.library[phase][g].some(item => item.name.toLowerCase() === (ing.nom || '').toLowerCase())) {
              const found = state.library[phase][g].find(item => item.name.toLowerCase() === (ing.nom || '').toLowerCase());
              if (found) found.id = ing.id;
              exists = true;
              break;
            }
          }

          if (!exists && ing.nom) {
            if (!state.library[phase][groupName]) {
              state.library[phase][groupName] = [];
            }
            state.library[phase][groupName].push({
              id: ing.id,
              name: ing.nom,
              isCustom: !!ing.est_personnalise,
              note: ing.inci ? `INCI: ${ing.inci}` : (ing.note || "Ingrédient global"),
              maxPct: ing.pourcentage_max || ing.max_pct || ing.dosage_max || null
            });
          }
        });
      }
    }
    mergeDatabaseIngredients();

    function render() {
      document.getElementById("app").innerHTML = buildAdminHTML();
      bindEvents();
      if (window.lucide) lucide.createIcons();
    }

    function buildAdminHTML() {
      const activeTab = state.activeAdminTab;
      const adminUser = state.currentUser;

      return `
      <div class="dashboard-layout">
        <!-- Mobile Header -->
        <div class="mobile-header">
          <img src="/logo.png.png" alt="MiracleLab" class="mobile-logo-img" />
          <button class="hamburger-btn" onclick="toggleSidebar()">
            <svg data-lucide="menu"></svg>
          </button>
        </div>
        
        <!-- Sidebar Overlay -->
        <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

        <!-- Admin Sidebar -->
        <aside class="dash-sidebar" id="dashSidebar">
          <div class="sidebar-logo" style="display: flex; justify-content: center; padding: 20px 0;">
            <img src="/logo.png.png" alt="Miraclelab" style="height: 120px; width: auto; transform: scale(1.2); mix-blend-mode: darken;" />
          </div>

          <div class="sidebar-section-label">Administration</div>

          <ul class="sidebar-menu-list">
            <li class="sidebar-menu-item ${activeTab === 'stats' ? 'active' : ''}" data-admintab="stats">
              <svg data-lucide="bar-chart-2"></svg> Statistiques
            </li>
            <li class="sidebar-menu-item ${activeTab === 'clients' ? 'active' : ''}" data-admintab="clients">
              <svg data-lucide="users"></svg> Clients & Abonnements
            </li>
            <li class="sidebar-menu-item ${activeTab === 'library' ? 'active' : ''}" data-admintab="library">
              <svg data-lucide="book-open"></svg> Bibliothèque
            </li>
            <li class="sidebar-menu-item ${activeTab === 'chat' ? 'active' : ''}" data-admintab="chat">
              <svg data-lucide="message-circle"></svg> Chat Support
            </li>
          </ul>

          <div class="sidebar-footer-zone">
            <button class="btn-admin-bascule" id="btn-admin-exit">
              <svg data-lucide="flask-conical"></svg> Espace Client
            </button>
            <button class="btn-logout" id="btn-logout">
              <svg data-lucide="power"></svg> Se déconnecter
            </button>
          </div>
        </aside>

        <!-- Admin Main Content -->
        <main class="dash-main" style="flex:1; padding:30px;">
          <h2 style="font-weight: 800; font-size: 22px; color:#1C0F32; margin-bottom: 30px; letter-spacing: -0.5px; display: flex; align-items: center; gap: 10px;">
            ${activeTab === 'stats' ? '<svg data-lucide="layout-dashboard" style="width: 24px; height: 24px; color: #7C3AED;"></svg> Tableaux de Bord & Statistiques' : activeTab === 'clients' ? '<svg data-lucide="users" style="width: 24px; height: 24px; color: #EA580C;"></svg> Gestion des Comptes Clients' : activeTab === 'library' ? '<svg data-lucide="book-open" style="width: 24px; height: 24px; color: #10B981;"></svg> Bibliothèque & Ingrédients' : '<svg data-lucide="message-circle" style="width: 24px; height: 24px; color: #3B82F6;"></svg> Centre de Chat Support'}
          </h2>

          <div style="flex:1">
            ${activeTab === 'stats' ? buildStatsHTML()
          : activeTab === 'clients' ? buildClientsHTML()
            : activeTab === 'library' ? buildLibraryTabHTML()
              : buildAdminChatHTML()}
          </div>
        </main>
      </div>

      ${state.showLibrary ? buildLibraryModal() : ""}
      ${state.showIngSheet ? buildIngredientSheet(state.showIngSheet) : ""}
      ${state.libAdding ? buildAddIngredientModal() : ""}
      `;
    }

    function buildStatsHTML() {
      // ── Real data from Laravel ──
      const S = window.LARAVEL_STATS || {};
      const activeCount    = S.activeUsers      ?? state.clients.filter(c => (c.statut_abonnement || c.subscription_status || '').toUpperCase() === 'ACTIF').length;
      const totalClients   = S.totalUsers       ?? state.clients.length;
      const inactiveCount  = S.inactiveUsers    ?? (totalClients - activeCount);
      const totalRev       = S.realRevenue      ?? (activeCount * 15000);
      const totalFormulas  = S.totalFormules    ?? 0;
      const totalIngs      = S.totalIngredients ?? 0;
      const convRate       = S.conversionRate   ?? (totalClients > 0 ? Math.round((activeCount / totalClients) * 100) : 0);

      // Formulas by category
      const skincareCount  = S.skincareCount    ?? 0;
      const haircareCount  = S.haircareCount    ?? 0;
      const autresCount    = S.autresCount      ?? 0;
      const maxCat         = Math.max(skincareCount, haircareCount, autresCount, 1);
      const skinH          = Math.max(Math.round((skincareCount / maxCat) * 160), 8);
      const hairH          = Math.max(Math.round((haircareCount / maxCat) * 160), 8);
      const autresH        = Math.max(Math.round((autresCount   / maxCat) * 160), 8);

      // Ingredient phase breakdown
      const ingAqueuse     = S.ingAqueuse          ?? 0;
      const ingHuileuse    = S.ingHuileuse         ?? 0;
      const ingRefr        = S.ingRefroidissement  ?? 0;
      const ingAqPct       = S.ingAqueusePct       ?? 0;
      const ingHuPct       = S.ingHuileusePct      ?? 0;
      const ingRePct       = S.ingRefroidissementPct ?? 0;

      // Top ingredients
      const topIngs = (S.topIngredients || []).slice(0, 5);
      const maxUsage = topIngs.length > 0 ? (topIngs[0].usage_count || 1) : 1;

      return `
      <div class="admin-stats-grid" style="margin-bottom: 30px;">
        
        <!-- Rev Card -->
        <div style="background: #FFF; border: 1px solid #F1EBE3; border-radius: 16px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); display: flex; flex-direction: column; gap: 12px; position: relative; overflow: hidden;">
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 11.5px; font-weight: 700; color: #8E7E72; text-transform: uppercase; letter-spacing: 0.5px;">Revenus Globaux</span>
            <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(124, 58, 237, 0.1); display: flex; align-items: center; justify-content: center; color: #7C3AED;">
              <svg data-lucide="wallet" style="width: 16px; height: 16px;"></svg>
            </div>
          </div>
          <div style="display: flex; align-items: baseline; gap: 6px;">
            <span style="font-size: 28px; font-weight: 800; color: #1C0F32; letter-spacing: -0.5px;">${totalRev.toLocaleString('fr-FR')}</span>
            <span style="font-size: 14px; font-weight: 600; color: #8E7E72;">FCFA</span>
          </div>
          <div style="font-size: 11.5px; color: #8E7E72;">${activeCount} abonné(s) actif(s)</div>
        </div>

        <!-- Active Clients Card -->
        <div style="background: #FFF; border: 1px solid #F1EBE3; border-radius: 16px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); display: flex; flex-direction: column; gap: 12px; position: relative; overflow: hidden;">
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 11.5px; font-weight: 700; color: #8E7E72; text-transform: uppercase; letter-spacing: 0.5px;">Clients Actifs</span>
            <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(234, 88, 12, 0.1); display: flex; align-items: center; justify-content: center; color: #EA580C;">
              <svg data-lucide="users" style="width: 16px; height: 16px;"></svg>
            </div>
          </div>
          <div style="display: flex; align-items: baseline; gap: 6px;">
            <span style="font-size: 28px; font-weight: 800; color: #1C0F32; letter-spacing: -0.5px;">${activeCount}</span>
            <span style="font-size: 16px; font-weight: 600; color: #8E7E72;">/ ${totalClients}</span>
          </div>
          <div style="font-size: 11.5px; color: #8E7E72;">${inactiveCount} inactif(s) &bull; ${totalClients} total</div>
        </div>

        <!-- Formulas Card -->
        <div style="background: #FFF; border: 1px solid #F1EBE3; border-radius: 16px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); display: flex; flex-direction: column; gap: 12px; position: relative; overflow: hidden;">
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 11.5px; font-weight: 700; color: #8E7E72; text-transform: uppercase; letter-spacing: 0.5px;">Formules Créées</span>
            <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(16, 185, 129, 0.1); display: flex; align-items: center; justify-content: center; color: #10B981;">
              <svg data-lucide="flask-conical" style="width: 16px; height: 16px;"></svg>
            </div>
          </div>
          <div style="display: flex; align-items: baseline; gap: 6px;">
            <span style="font-size: 28px; font-weight: 800; color: #1C0F32; letter-spacing: -0.5px;">${totalFormulas}</span>
          </div>
          <div style="font-size: 11.5px; color: #8E7E72;">${skincareCount} Skincare &bull; ${haircareCount} Haircare</div>
        </div>

        <!-- Conversion Card -->
        <div style="background: #FFF; border: 1px solid #F1EBE3; border-radius: 16px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); display: flex; flex-direction: column; gap: 12px; position: relative; overflow: hidden;">
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 11.5px; font-weight: 700; color: #8E7E72; text-transform: uppercase; letter-spacing: 0.5px;">Taux de Conversion</span>
            <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(59, 130, 246, 0.1); display: flex; align-items: center; justify-content: center; color: #3B82F6;">
              <svg data-lucide="activity" style="width: 16px; height: 16px;"></svg>
            </div>
          </div>
          <div style="display: flex; align-items: baseline; gap: 6px;">
            <span style="font-size: 28px; font-weight: 800; color: #1C0F32; letter-spacing: -0.5px;">${convRate}%</span>
          </div>
          <div style="font-size: 11.5px; color: #8E7E72;">Actifs / Total inscrits</div>
        </div>
      </div>

      <!-- Chart Section -->
      <div style="background: #FFF; border: 1px solid #F1EBE3; border-radius: 20px; box-shadow: 0 4px 12px rgba(186,126,192,0.02); padding: 30px;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px;">
          <div>
            <h3 style="font-weight: 800; font-size: 16px; color: #1C0F32; margin: 0 0 4px 0;">Volume de Formulation par Catégorie</h3>
            <p style="font-size: 12px; color: #8E7E72; margin: 0;">Répartition des formules enregistrées par les utilisateurs</p>
          </div>
        </div>

        <div style="display: flex; align-items: flex-end; gap: 24px; height: 200px; padding-bottom: 10px; border-bottom: 1px solid #F1EBE3;">
          <!-- Skincare -->
          <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 12px;">
            <span style="font-size: 13px; font-weight: 800; color: #C2185B;">${skincareCount}</span>
            <div style="width: 100%; max-width: 120px; height: ${skinH}px; background: linear-gradient(180deg, rgba(194,24,91,0.6) 0%, rgba(194,24,91,0.15) 100%); border-radius: 8px 8px 0 0; border: 1px solid rgba(194,24,91,0.2); border-bottom: none;"></div>
          </div>
          
          <!-- Haircare -->
          <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 12px;">
            <span style="font-size: 13px; font-weight: 800; color: #7C3AED;">${haircareCount}</span>
            <div style="width: 100%; max-width: 120px; height: ${hairH}px; background: linear-gradient(180deg, rgba(124,58,237,0.6) 0%, rgba(124,58,237,0.15) 100%); border-radius: 8px 8px 0 0; border: 1px solid rgba(124,58,237,0.2); border-bottom: none;"></div>
          </div>

          <!-- Autres -->
          <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 12px;">
            <span style="font-size: 13px; font-weight: 800; color: #10B981;">${autresCount}</span>
            <div style="width: 100%; max-width: 120px; height: ${autresH}px; background: linear-gradient(180deg, rgba(16,185,129,0.6) 0%, rgba(16,185,129,0.15) 100%); border-radius: 8px 8px 0 0; border: 1px solid rgba(16,185,129,0.2); border-bottom: none;"></div>
          </div>
        </div>
        
        <div style="display: flex; align-items: center; justify-content: space-around; padding-top: 16px;">
          <span style="font-size: 12.5px; font-weight: 700; color: #8E7E72;">Skincare</span>
          <span style="font-size: 12.5px; font-weight: 700; color: #8E7E72;">Haircare</span>
          <span style="font-size: 12.5px; font-weight: 700; color: #8E7E72;">Autres</span>
        </div>
      </div>

      <!-- Secondary Stats Section -->
      <div class="admin-charts-grid" style="margin-top: 30px;">
        
        <!-- Dynamique Bibliothèque -->
        <div style="background: #FFF; border: 1px solid #F1EBE3; border-radius: 20px; box-shadow: 0 4px 12px rgba(186,126,192,0.02); padding: 25px; display: flex; flex-direction: column;">
          <h3 style="font-weight: 800; font-size: 14px; color: #1C0F32; margin: 0 0 20px 0; display: flex; align-items: center; gap: 8px;">
            <svg data-lucide="database" style="width: 16px; height: 16px; color: #10B981;"></svg> Bibliothèque Ingrédients
          </h3>
          
          <div style="flex: 1; display: flex; flex-direction: column; justify-content: center;">
            <div style="text-align: center; margin-bottom: 24px;">
              <div style="font-size: 36px; font-weight: 800; color: #1C0F32; letter-spacing: -1px; line-height: 1;">${totalIngs}</div>
              <div style="font-size: 13px; font-weight: 700; color: #8E7E72; margin-top: 6px;">Ingrédients dans la base</div>
            </div>

            <!-- Progress Bar breakdown -->
            <div style="display: flex; height: 12px; border-radius: 6px; overflow: hidden; margin-bottom: 12px;">
              <div style="width: ${ingAqPct}%; background: #A21CAF;" title="Phase Aqueuse"></div>
              <div style="width: ${ingHuPct}%; background: #F59E0B;" title="Phase Huileuse"></div>
              <div style="width: ${ingRePct}%; background: #3B82F6;" title="Phase Refroidissement"></div>
            </div>

            <div style="display: flex; justify-content: space-between; font-size: 11.5px; font-weight: 600; color: #64748B;">
              <div style="display: flex; align-items: center; gap: 4px;"><div style="width: 8px; height: 8px; border-radius: 2px; background: #A21CAF;"></div> Aqueuse (${ingAqPct}%)</div>
              <div style="display: flex; align-items: center; gap: 4px;"><div style="width: 8px; height: 8px; border-radius: 2px; background: #F59E0B;"></div> Huileuse (${ingHuPct}%)</div>
              <div style="display: flex; align-items: center; gap: 4px;"><div style="width: 8px; height: 8px; border-radius: 2px; background: #3B82F6;"></div> Refr. (${ingRePct}%)</div>
            </div>
          </div>
        </div>

        <!-- Top Ingredients -->
        <div style="background: #FFF; border: 1px solid #F1EBE3; border-radius: 20px; box-shadow: 0 4px 12px rgba(186,126,192,0.02); padding: 25px;">
          <h3 style="font-weight: 800; font-size: 14px; color: #1C0F32; margin: 0 0 20px 0; display: flex; align-items: center; gap: 8px;">
            <svg data-lucide="star" style="width: 16px; height: 16px; color: #F59E0B;"></svg> Ingrédients les plus utilisés
          </h3>
          <div style="display: flex; flex-direction: column; gap: 10px;">
            ${topIngs.length > 0 ? topIngs.map((ing, i) => `
              <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-weight: 800; color: #7C3AED; font-size: 12px; min-width: 24px;">#${i+1}</span>
                <div style="flex: 1;">
                  <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                    <span style="font-size: 13px; font-weight: 700; color: #1C0F32;">${esc(ing.nom || ing.name || '')}</span>
                    <span style="font-size: 11px; color: #8E7E72; font-weight: 600;">${ing.usage_count} formule(s)</span>
                  </div>
                  <div style="height: 4px; border-radius: 2px; background: #F3F0FF; overflow: hidden;">
                    <div style="height: 100%; width: ${Math.round((ing.usage_count / maxUsage) * 100)}%; background: linear-gradient(90deg, #7C3AED, #A855F7); border-radius: 2px;"></div>
                  </div>
                </div>
              </div>
            `).join('') : '<div style="color: #8E7E72; font-size: 13px; text-align: center; padding: 20px 0;">Aucune donnée disponible</div>'}
          </div>
        </div>

      </div>`;
    }

    function buildClientsHTML() {
      return `
      <div class="admin-table-wrap" style="margin-bottom: 0; box-shadow: none;">
        <table class="admin-table admin-users-table" style="margin-bottom:0; border:none; box-shadow:none; width: 100%;">
          <thead>
            <tr>
              <th>Client</th>
              <th>Email</th>
              <th>Téléphone</th>
              <th>Abonnement</th>
              <th>Formules</th>
              <th>Inscription</th>
              <th style="text-align:right">Actions</th>
            </tr>
          </thead>
          <tbody>
            ${state.clients.map(c => {
              const name = c.nom_complet || ((c.first_name || c.last_name) ? `${c.first_name || ''} ${c.last_name || ''}`.trim() : c.email);
              const phone = c.telephone || c.phone || 'Non renseigné';
              const formCount = c.formules_count ?? c.formula_count ?? 0;
              const dateStr = c.created_at ? new Date(c.created_at).toLocaleDateString('fr-FR') : 'Récemment';
              const status = (c.statut_abonnement || c.subscription_status || 'INACTIF').toUpperCase();

              return `
              <tr onclick="this.classList.toggle('expanded')">
                <td style="font-weight:700; color:#1F2937; cursor:pointer;">
                  <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span>${esc(name)}</span>
                    <svg class="mobile-expand-icon" data-lucide="chevron-down" style="width:18px; height:18px; color:#A21CAF; display:none; transition:transform 0.2s;"></svg>
                  </div>
                </td>
                <td>${esc(c.email)}</td>
                <td>${esc(phone)}</td>
                <td>
                  <span style="background:${status === 'ACTIVE' || status === 'ACTIF' ? '#D1FAE5; color:#065F46' : status === 'EXPIRED' || status === 'EXPIRÉ' ? '#FEF3C7; color:#92400E' : '#FEE2E2; color:#991B1B'}; padding:4px 10px; border-radius:20px; font-size:10.5px; font-weight:800">
                    ${status === 'ACTIVE' || status === 'ACTIF' ? 'ACTIF' : status === 'EXPIRED' || status === 'EXPIRÉ' ? 'EXPIRÉ' : 'INACTIF'}
                  </span>
                </td>
                <td><b>${formCount}</b></td>
                <td>${dateStr}</td>
                <td style="text-align:right">
                  <button class="admin-btn-action btn-toggle-sub" data-clientemail="${esc(c.email)}" style="background:#A21CAF; color:#fff; border-radius:6px">
                    Changer Statut
                  </button>
                </td>
              </tr>`;
            }).join('')}
          </tbody>
        </table>
      </div>`;
    }

    function buildLibraryTabHTML() {
      const q = state.libSearch.toLowerCase().trim();
      const displayGroups = {};
      const initNames = new Set(Object.values(LIBRARY).flatMap(pg => Object.values(pg)).flat().map(i => i.name));

      const ph = PHASES[state.libTab] || PHASES.AQUEUSE;

      if (q) {
        for (const [phase, pgroups] of Object.entries(state.library)) {
          for (const [g, items] of Object.entries(pgroups)) {
            const filtered = items.filter(i => i.name.toLowerCase().includes(q) || i.note.toLowerCase().includes(q));
            if (filtered.length > 0) {
              const label = `${(PHASES[phase] || PHASES.AQUEUSE).icon} ${g}`;
              displayGroups[label] = filtered.map(i => ({ ...i, phase }));
            }
          }
        }
      } else {
        for (const [g, items] of Object.entries(state.library[state.libTab] || {})) {
          displayGroups[g] = items.map(i => ({ ...i, phase: state.libTab }));
        }
      }

      const total = Object.values(state.library).flatMap(pg => Object.values(pg)).flat().length;

      return `
      <div style="background: #fff; border-radius: 20px; border: 1px solid #E2E8F0; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <div class="modal-header" style="border-radius: 20px 20px 0 0; background: #966BF4;">
          <div class="modal-header-top">
            <div>
              <div class="modal-subtitle" style="color: rgba(255,255,255,0.7)">Référentiel professionnel</div>
              <div class="modal-title" style="font-weight: 800; font-size: 20px; color: #fff;">📚 Bibliothèque d'Ingrédients</div>
              <div class="modal-count" style="color: rgba(255,255,255,0.7); font-size: 11px;">${total} ingrédients · ${Object.values(state.library).flatMap(pg => Object.keys(pg)).length} groupes</div>
            </div>
            <div class="modal-btns">
              <button class="btn-add-lib" id="lib-add-btn">+ Ajouter</button>
            </div>
          </div>
          <input class="modal-search" id="lib-search" placeholder="Rechercher dans toutes les phases…" value="${esc(state.libSearch)}"/>
          ${!state.libSearch ? `
          <div class="modal-tabs" style="display: flex; flex-wrap: wrap; gap: 4px;">
            ${Object.entries(PHASES).filter(([k]) => ["AQUEUSE", "HUILEUSE", "REFROIDISSEMENT"].includes(k)).map(([k, p]) => `
              <button class="modal-tab ${state.libTab === k ? "active" : ""}" data-libtab="${k}" style="${state.libTab === k ? "color:#FFF; border-bottom: 2px solid #FFF;" : "color:rgba(255,255,255,0.6);"}">
                ${p.icon} ${p.label.replace("Phase ", "")}
              </button>`).join("")}
          </div>`: `<div style="height:10px"></div>`}
        </div>

        <div class="lib-list" style="max-height: 600px; overflow-y: auto;">
          ${Object.entries(displayGroups).map(([groupName, items]) => `
            <div class="lib-group-header" style="background:${q ? "#F8FAFC" : ph.light};color:${q ? "#94A3B8" : ph.accent}">
              ${groupName} <span style="font-weight:400;opacity:.6">(${items.length})</span>
            </div>
            ${items.map((ing, i) => {
        const p = PHASES[ing.phase] || PHASES.AQUEUSE;
        const isCustom = !initNames.has(ing.name);
        return `
              <div class="lib-item" style="background:${i % 2 === 0 ? "#fff" : "#FAFAFA"}">
                <div class="lib-dot" style="background:${p.color}"></div>
                <div class="lib-info" style="flex:1;">
                  <div class="lib-name" style="font-weight:700; color:#1E293B;">
                    ${esc(ing.name)}
                    ${isCustom ? `<span class="lib-badge-custom">PERSO</span>` : ""}
                    ${ing.maxPct ? `<span class="lib-limit">max ${ing.maxPct}%</span>` : ""}
                  </div>
                  <div class="lib-note" style="font-size:11px;color:#64748B">${esc(ing.note)}</div>
                </div>
              
                ${q ? `<div style="font-size:10px;padding:2px 7px;border-radius:20px;font-weight:700;margin-right:7px;background:${p.light};color:${p.accent}">${p.icon} ${p.short}</div>` : ""}
                ${INGREDIENT_SHEETS[ing.name] ? `<button class="ing-info-btn" data-ingname="${esc(ing.name)}" title="Fiche technique">ℹ️</button>` : ""}
                <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">
                  <button class="edit-lib-btn" data-libphase="${ing.phase}" data-libname="${esc(ing.name)}" title="Modifier" style="background:#FFF7ED;border:1px solid #FED7AA;cursor:pointer;color:#F56D13;width:28px;height:28px;border-radius:6px;display:flex;align-items:center;justify-content:center;transition:.15s;flex-shrink:0;"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                  <button class="del-lib-btn" data-libphase="${ing.phase}" data-libname="${esc(ing.name)}" title="Supprimer" style="background:#FEE2E2;border:none;cursor:pointer;color:#DC2626;font-size:14px;width:28px;height:28px;border-radius:6px;display:flex;align-items:center;justify-content:center;transition:.15s;flex-shrink:0;">🗑</button>
                </div>
              </div>`;
      }).join("")}
          `).join("")}
          ${Object.keys(displayGroups).length === 0 ? `<div style="padding:40px;text-align:center;color:#9CA3AF;font-size:14px">Aucun résultat pour "${esc(state.libSearch)}"</div>` : ""}
        </div>
      </div>`;
    }

    function buildAdminChatHTML() {
      const getClientDisplayName = (c, defaultEmail) => {
        if (!c) return defaultEmail;
        return c.nom_complet || ((c.first_name || c.last_name) ? `${c.first_name || ''} ${c.last_name || ''}`.trim() : defaultEmail);
      };

      const emails = Object.keys(state.chats);
      const selectedEmail = state.adminSelectedChatEmail;
      const currentMessages = state.chats[selectedEmail] || [];
      const foundClient = state.clients.find(c => c.email === selectedEmail);
      const foundClientName = getClientDisplayName(foundClient, selectedEmail);

      return `
      <div class="admin-chat-layout mobile-view-${state.adminMobileChatView}" style="height: 520px;">
        <div class="admin-chat-users">
          ${emails.map(email => {
        const client = state.clients.find(c => c.email === email);
        const name = getClientDisplayName(client, email);
        const list = state.chats[email];
        const lastMsg = list && list.length ? list[list.length - 1].text : "Pas de message";
        return `
            <div class="admin-chat-user-item ${selectedEmail === email ? 'active' : ''}" data-chatemail="${esc(email)}">
              <div class="admin-chat-user-name">${esc(name)}</div>
              <div class="admin-chat-user-msg">${esc(lastMsg)}</div>
            </div>`;
      }).join('')}
        </div>
        
        <div class="admin-chat-pane">
          <div class="admin-chat-pane-header" style="background:#fff; border-bottom:1px solid #E2E8F0; display:flex; align-items:center; gap:10px;">
            <button class="btn-chat-back" style="background:none; border:none; cursor:pointer; color:#7C3AED; font-weight:800; font-size:14px; padding:0; display:flex; align-items:center; gap:4px;">
              <svg data-lucide="arrow-left" style="width:16px; height:16px;"></svg> Retour
            </button>
            <span>💬 Support direct : ${esc(foundClientName)}</span>
          </div>
          
          <div class="admin-chat-pane-messages" id="admin-chat-messages-box">
            ${currentMessages.map(m => `
              <div class="chat-msg ${m.sender === 'support' ? 'client' : 'support'}" style="${m.sender === 'support' ? 'background:#221230; align-self:flex-end' : ''}">
                <div>${esc(m.text)}</div>
                <span class="chat-msg-time">${m.time}</span>
              </div>`).join('')}
          </div>
          
          <div class="chat-box-footer" style="background:#fff;">
            <input class="chat-box-input" id="admin-chat-input-el" placeholder="Saisir votre message..." value="${esc(state.adminChatInput)}"/>
            <button class="chat-btn-send" id="btn-admin-chat-send" style="background:#A21CAF">Répondre</button>
          </div>
        </div>
      </div>`;
    }

    function buildLibraryModal() {
      const q = state.libSearch.toLowerCase().trim();
      const displayGroups = {};
      const initNames = new Set(Object.values(LIBRARY).flatMap(pg => Object.values(pg)).flat().map(i => i.name));

      const ph = PHASES[state.libTab] || PHASES.AQUEUSE;

      if (q) {
        for (const [phase, pgroups] of Object.entries(state.library)) {
          for (const [g, items] of Object.entries(pgroups)) {
            const filtered = items.filter(i => i.name.toLowerCase().includes(q) || i.note.toLowerCase().includes(q));
            if (filtered.length > 0) {
              const label = `${(PHASES[phase] || PHASES.AQUEUSE).icon} ${g}`;
              displayGroups[label] = filtered.map(i => ({ ...i, phase }));
            }
          }
        }
      } else {
        for (const [g, items] of Object.entries(state.library[state.libTab] || {})) {
          displayGroups[g] = items.map(i => ({ ...i, phase: state.libTab }));
        }
      }

      const total = Object.values(state.library).flatMap(pg => Object.values(pg)).flat().length;

      return `
      <div class="modal-overlay" id="lib-overlay">
        <div class="modal-box" onclick="event.stopPropagation()">
          <div class="modal-header" style="background: #966BF4;">
            <div class="modal-header-top">
              <div>
                <div class="modal-subtitle" style="color: rgba(255,255,255,0.7)">Référentiel professionnel</div>
                <div class="modal-title" style="font-weight: 800; font-size: 20px; color: #fff;">📚 Bibliothèque d'Ingrédients</div>
                <div class="modal-count" style="color: rgba(255,255,255,0.7); font-size: 11px;">${total} ingrédients · ${Object.values(state.library).flatMap(pg => Object.keys(pg)).length} groupes</div>
              </div>
              <div class="modal-btns">
                <button class="btn-add-lib" id="lib-add-btn">+ Ajouter</button>
                <button class="btn-close" id="lib-close">×</button>
              </div>
            </div>
            <input class="modal-search" id="lib-search" placeholder="Rechercher dans toutes les phases…" value="${esc(state.libSearch)}"/>
            ${!state.libSearch ? `
            <div class="modal-tabs" style="display: flex; flex-wrap: wrap; gap: 4px;">
              ${Object.entries(PHASES).filter(([k]) => ["AQUEUSE", "HUILEUSE", "REFROIDISSEMENT"].includes(k)).map(([k, p]) => `
                <button class="modal-tab ${state.libTab === k ? "active" : ""}" data-libtab="${k}" style="${state.libTab === k ? "color:#FFF; border-bottom: 2px solid #FFF;" : "color:rgba(255,255,255,0.6);"}">
                  ${p.icon} ${p.label.replace("Phase ", "")}
                </button>`).join("")}
            </div>`: `<div style="height:10px"></div>`}
          </div>

          <div class="lib-list" style="max-height: 350px; overflow-y: auto;">
            ${Object.entries(displayGroups).map(([groupName, items]) => `
              <div class="lib-group-header" style="background:${q ? "#F8FAFC" : ph.light};color:${q ? "#94A3B8" : ph.accent}">
                ${groupName} <span style="font-weight:400;opacity:.6">(${items.length})</span>
              </div>
              ${items.map((ing, i) => {
        const p = PHASES[ing.phase] || PHASES.AQUEUSE;
        const isCustom = !initNames.has(ing.name);
        return `
                <div class="lib-item" style="background:${i % 2 === 0 ? "#fff" : "#FAFAFA"}">
                  <div class="lib-dot" style="background:${p.color}"></div>
                  <div class="lib-info" style="flex:1;">
                    <div class="lib-name" style="font-weight:700; color:#1E293B;">
                      ${esc(ing.name)}
                      ${isCustom ? `<span class="lib-badge-custom">PERSO</span>` : ""}
                      ${ing.maxPct ? `<span class="lib-limit">max ${ing.maxPct}%</span>` : ""}
                    </div>
                    <div class="lib-note" style="font-size:11px;color:#64748B">${esc(ing.note)}</div>
                  </div>
                  ${q ? `<div style="font-size:10px;padding:2px 7px;border-radius:20px;font-weight:700;margin-right:7px;background:${p.light};color:${p.accent}">${p.icon} ${p.short}</div>` : ""}
                  ${INGREDIENT_SHEETS[ing.name] ? `<button class="ing-info-btn" data-ingname="${esc(ing.name)}" title="Fiche technique">ℹ️</button>` : ""}
                  <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">
                    <button class="edit-lib-btn" data-libphase="${ing.phase}" data-libname="${esc(ing.name)}" title="Modifier" style="background:#FFF7ED;border:1px solid #FED7AA;cursor:pointer;color:#F56D13;width:28px;height:28px;border-radius:6px;display:flex;align-items:center;justify-content:center;transition:.15s;flex-shrink:0;"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                    <button class="del-lib-btn" data-libphase="${ing.phase}" data-libname="${esc(ing.name)}" title="Supprimer" style="background:#FEE2E2;border:none;cursor:pointer;color:#DC2626;font-size:14px;width:28px;height:28px;border-radius:6px;display:flex;align-items:center;justify-content:center;transition:.15s;flex-shrink:0;">🗑</button>
                  </div>
                </div>`;
      }).join("")}
            `).join("")}
            ${Object.keys(displayGroups).length === 0 ? `<div style="padding:40px;text-align:center;color:#9CA3AF;font-size:14px">Aucun résultat pour "${esc(state.libSearch)}"</div>` : ""}
          </div>
        </div>
      </div>`;
    }

    function buildIngredientSheet(name) {
      const s = INGREDIENT_SHEETS[name];
      if (!s) return '';
      const barW = Math.min((s.dosageMax / 30) * 100, 100);
      return `
      <div class="sheet-overlay" id="sheet-overlay">
        <div class="sheet-modal">
          <div class="sheet-header" style="background: linear-gradient(135deg, #221230, #3E2452);">
            <div>
              <div class="sheet-eyebrow" style="color: rgba(255,255,255,0.5)">Fiche Technique</div>
              <div class="sheet-title" style="font-weight: 800; font-size: 20px; color: #fff;">${esc(name)}</div>
              <div class="sheet-inci" style="color: rgba(255,255,255,0.7); font-size: 11px;">${esc(s.inci)}</div>
            </div>
            <button class="sheet-close" id="sheet-close">✕</button>
          </div>
          <div class="sheet-body">
            <div class="sheet-badge" style="background:#A21CAF; color:#fff">${esc(s.category)}</div>
            <div class="sheet-grid">
              <div class="sheet-item"><div class="sheet-label">📍 Phase d'ajout</div><div class="sheet-value">${esc(s.phases)}</div></div>
              <div class="sheet-item"><div class="sheet-label">🌡️ Température</div><div class="sheet-value">${esc(s.temp)}</div></div>
              <div class="sheet-item"><div class="sheet-label">💧 Solubilité</div><div class="sheet-value">${esc(s.solubility)}</div></div>
              <div class="sheet-item"><div class="sheet-label">⚗️ pH optimal</div><div class="sheet-value">${esc(s.pH)}</div></div>
            </div>
            <div class="sheet-dosage">
              <div class="sheet-label">📏 Dosage recommandé</div>
              <div class="sheet-dosage-bar"><div class="sheet-dosage-fill" style="width:${barW}%"></div></div>
              <div class="sheet-dosage-nums"><span>${s.dosageMin}%</span><span style="font-weight:700;color:#A21CAF">${s.dosageMin}% – ${s.dosageMax}%</span><span>max ${s.dosageMax}%</span></div>
            </div>
            <div class="sheet-section">
              <div class="sheet-label">Propriétés</div>
              <ul class="sheet-list">${s.properties.map(p => `<li>${esc(p)}</li>`).join('')}</ul>
            </div>
            ${s.cautions ? `<div class="sheet-caution"><div class="sheet-label">⚠️ Précautions</div><div class="sheet-caution-text">${esc(s.cautions)}</div></div>` : ''}
            ${s.tips ? `<div class="sheet-tips"><div class="sheet-label">💡 Conseil formulateur</div><div class="sheet-tips-text">${esc(s.tips)}</div></div>` : ''}
          </div>
        </div>
      </div>`;
    }

    function buildAddIngredientModal() {
      return `
      <div class="modal-overlay" id="add-ing-overlay">
        <div class="modal-box" style="max-width: 480px; padding: 25px; border-radius: 20px; box-shadow: 0 20px 50px rgba(0,0,0,0.15); background:#fff;" onclick="event.stopPropagation()">
          <h3 style="font-weight: 800; font-size: 18px; color: #221230; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
            Nouvel ingrédient
          </h3>
          
          <div style="margin-bottom: 15px;">
            <label style="display: block; font-size: 12px; font-weight: 700; color: #64748B; margin-bottom: 6px;">Phase de formulation</label>
            <select class="add-select" id="lib-new-phase" style="width: 100%; padding: 10px; border-radius: 10px; border: 1px solid #CBD5E1; background: #fff; font-size: 13px; box-sizing: border-box;">
              ${Object.entries(PHASES).map(([k, p]) => `<option value="${k}" ${state.libNewPhase === k ? "selected" : ""}>${p.icon} ${p.label}</option>`).join("")}
            </select>
          </div>
          
          <div style="margin-bottom: 15px;">
            <label style="display: block; font-size: 12px; font-weight: 700; color: #64748B; margin-bottom: 6px;">Nom de l'ingrédient *</label>
            <input class="add-input" id="lib-new-name" placeholder="Ex: Hydrolat de Rose" value="${esc(state.libNewName)}" style="width: 100%; padding: 10px; border-radius: 10px; border: 1px solid #CBD5E1; box-sizing: border-box; font-size: 13px;"/>
          </div>
          
          <div style="margin-bottom: 20px;">
            <label style="display: block; font-size: 12px; font-weight: 700; color: #64748B; margin-bottom: 6px;">Propriétés / Description</label>
            <input class="add-input" id="lib-new-note" placeholder="Ex: Apaisant, tonifiant..." value="${esc(state.libNewNote)}" style="width: 100%; padding: 10px; border-radius: 10px; border: 1px solid #CBD5E1; box-sizing: border-box; font-size: 13px;"/>
          </div>
          
          <div style="display: flex; gap: 10px; justify-content: flex-end;">
            <button class="btn-confirm" id="lib-confirm" style="background:#F56D13; color:#fff; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 700; cursor: pointer; font-size: 13px;">
              Confirmer
            </button>
            <button class="btn-cancel" id="lib-cancel" style="background: #F1F5F9; color: #475569; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 700; cursor: pointer; font-size: 13px;">
              Annuler
            </button>
          </div>
        </div>
      </div>`;
    }

    function addToLibrary() {
      const n = state.libNewName.trim();
      if (!n) return;
      const ph = state.libNewPhase || state.libTab;
      const groups = state.library[ph];
      const groupKey = ph === "AQUEUSE" ? "Actifs Capillaires" : ph === "HUILEUSE" ? "Huiles Végétales" : "Actifs & Vitamines";
      if (!groups[groupKey]) groups[groupKey] = [];
      
      const newItem = {
        name: n,
        note: state.libNewNote.trim() || "Ingrédient global",
        isCustom: false
      };
      groups[groupKey].push(newItem);

      // Save to Backend Database asynchronously
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      if (csrfToken) {
        fetch('/ingredients', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            nom: n,
            phase: ph,
            inci: state.libNewNote.trim() || null
          })
        })
        .then(r => r.json())
        .then(data => {
          if (data.ingredient && data.ingredient.id) {
            newItem.id = data.ingredient.id;
          }
        })
        .catch(err => console.error('Erreur sauvegarde ingrédient admin:', err));
      }

      state.libNewName = ""; state.libNewNote = ""; state.libAdding = false;
      showToast("✅ Ingrédient ajouté à la base globale !");
      render();
    }

    function deleteFromLibrary(phase, name) {
      const groups = state.library[phase];
      let targetId = null;

      for (const g of Object.keys(groups)) {
        const item = (groups[g] || []).find(i => i.name === name);
        if (item && item.id) targetId = item.id;
        groups[g] = groups[g].filter(i => i.name !== name);
      }
      render();

      if (targetId) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken) {
          fetch(`/ingredients/${targetId}`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': csrfToken,
              'Accept': 'application/json'
            }
          }).catch(err => console.error('Erreur suppression ingrédient admin:', err));
        }
      }
    }

    function bindEvents() {
      // Sidebar Menu click
      document.querySelectorAll(".sidebar-menu-list .sidebar-menu-item").forEach(item => {
        item.addEventListener("click", e => {
          state.activeAdminTab = e.currentTarget.dataset.admintab;
          render();
          if (state.activeAdminTab === 'chat') scrollChatToBottom("admin-chat-messages-box");
        });
      });

      // Inbox Tabs click
      document.querySelectorAll(".inbox-tab-btn").forEach(item => {
        item.addEventListener("click", e => {
          state.activeInboxTab = e.currentTarget.dataset.inboxtab;
          render();
        });
      });

      document.getElementById("btn-logout")?.addEventListener("click", logout);
      document.getElementById("btn-admin-exit")?.addEventListener("click", () => {
        window.location.href = "/dashboard?view=client";
      });

      // Stats card shortcut
      document.getElementById("btn-admin-show-lib")?.addEventListener("click", () => {
        state.showLibrary = true;
        render();
      });

      // Toggle user status
      document.querySelectorAll(".btn-toggle-sub").forEach(btn => {
        btn.addEventListener("click", e => {
          const email = e.currentTarget.dataset.clientemail;
          state.clients = state.clients.map(c => {
            if (c.email === email) {
              const newStatus = c.subscription_status === 'ACTIVE' ? 'EXPIRED' : 'ACTIVE';
              showToast(`Abonnement de ${c.first_name} passé à ${newStatus}`);
              return { ...c, subscription_status: newStatus };
            }
            return c;
          });
          saveClients(state.clients);
          render();
        });
      });

      // Admin Chat list item select
      document.querySelectorAll(".admin-chat-user-item").forEach(item => {
        item.addEventListener("click", e => {
          state.adminSelectedChatEmail = e.currentTarget.dataset.chatemail;
          state.adminMobileChatView = "chat";
          render();
          scrollChatToBottom("admin-chat-messages-box");
        });
      });

      const backBtn = document.querySelector(".btn-chat-back");
      if (backBtn) {
        backBtn.addEventListener("click", () => {
          state.adminMobileChatView = "list";
          render();
        });
      }

      // Admin send support reply
      const sendAdminMessage = () => {
        const input = document.getElementById("admin-chat-input-el");
        const text = input ? input.value.trim() : "";
        if (!text) return;

        const email = state.adminSelectedChatEmail;
        if (!state.chats[email]) state.chats[email] = [];

        const targetClient = state.clients.find(c => c.email === email);

        fetch('/messages', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({
            message: text,
            destinataire_id: targetClient ? targetClient.id : null
          })
        }).then(r => r.json()).then(data => {
          if (data.success) {
            state.chats[email].push(data.message);
            state.adminChatInput = "";
            render();
            scrollChatToBottom("admin-chat-messages-box");
          }
        }).catch(e => {
          console.error(e);
          const now = new Date();
          const timeStr = now.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
          state.chats[email].push({ sender: "support", text: text, time: timeStr });
          state.adminChatInput = "";
          render();
          scrollChatToBottom("admin-chat-messages-box");
        });
      };

      document.getElementById("btn-admin-chat-send")?.addEventListener("click", sendAdminMessage);
      const adminInputEl = document.getElementById("admin-chat-input-el");
      if (adminInputEl) {
        adminInputEl.addEventListener("input", e => { state.adminChatInput = e.target.value; });
        adminInputEl.addEventListener("keydown", e => { if (e.key === "Enter") sendAdminMessage(); });
      }

      // Library modal events
      document.getElementById("lib-overlay")?.addEventListener("mousedown", e => { if (e.target.id === "lib-overlay") { state.showLibrary = false; render(); } });
      document.getElementById("lib-close")?.addEventListener("click", () => { state.showLibrary = false; render(); });
      document.getElementById("lib-add-btn")?.addEventListener("click", () => { state.libAdding = true; render(); });
      document.getElementById("lib-cancel")?.addEventListener("click", () => { state.libAdding = false; state.libNewName = ""; state.libNewNote = ""; render(); });
      document.getElementById("lib-confirm")?.addEventListener("click", addToLibrary);
      document.querySelectorAll("[data-libtab]").forEach(el => el.addEventListener("click", e => { state.libTab = e.currentTarget.dataset.libtab; state.libSearch = ""; render(); }));
      const libSearchEl = document.getElementById("lib-search");
      if (libSearchEl) {
        libSearchEl.addEventListener("input", e => { state.libSearch = e.target.value; });
        libSearchEl.addEventListener("blur", () => render());
      }
      document.querySelectorAll(".del-lib-btn").forEach(el => el.addEventListener("click", e => deleteFromLibrary(e.currentTarget.dataset.libphase, e.currentTarget.dataset.libname)));
      document.querySelectorAll(".ing-info-btn").forEach(el => el.addEventListener("click", e => { e.stopPropagation(); state.showIngSheet = e.currentTarget.dataset.ingname; render(); }));
      document.getElementById("sheet-close")?.addEventListener("click", () => { state.showIngSheet = null; render(); });
      document.getElementById("sheet-overlay")?.addEventListener("mousedown", e => { if (e.target.id === "sheet-overlay") { state.showIngSheet = null; render(); } });

      const libNameEl = document.getElementById("lib-new-name");
      if (libNameEl) {
        libNameEl.addEventListener("input", e => { state.libNewName = e.target.value; const btn = document.getElementById("lib-confirm"); if (btn) { btn.style.background = e.target.value.trim() ? "linear-gradient(135deg,#A21CAF,#86198F)" : "#E5E7EB"; btn.style.color = e.target.value.trim() ? "#fff" : "#9CA3AF"; } });
        setTimeout(() => libNameEl.focus(), 100);
      }
      const libNoteEl = document.getElementById("lib-new-note");
      if (libNoteEl) { libNoteEl.addEventListener("input", e => { state.libNewNote = e.target.value; }); }
      document.getElementById("lib-new-phase")?.addEventListener("change", e => { state.libNewPhase = e.target.value; });
      document.getElementById("add-ing-overlay")?.addEventListener("mousedown", e => { if (e.target.id === "add-ing-overlay") { state.libAdding = false; state.libNewName = ""; state.libNewNote = ""; render(); } });
    }

    function scrollChatToBottom(id) {
      setTimeout(() => {
        const box = document.getElementById(id);
        if (box) box.scrollTop = box.scrollHeight;
      }, 50);
    }

    // Initial render
    render();
  </script>
</body>

</html>