// ─────────────────────────────────────────────────────────────
// SHARED STATE & MOCK DATABASE
// ─────────────────────────────────────────────────────────────

const INITIAL_CLIENTS = [
  { id: 101, first_name: "Awa", last_name: "Diop", email: "awa.diop@example.com", phone: "+221 77 123 45 67", role: "CLIENT", subscription_status: "ACTIVE", formula_count: 12, created_at: "10/06/2026" },
  { id: 102, first_name: "Babacar", last_name: "Sy", email: "babacar.sy@example.com", phone: "+221 78 987 65 43", role: "CLIENT", subscription_status: "ACTIVE", formula_count: 8, created_at: "14/06/2026" },
  { id: 103, first_name: "Mariama", last_name: "Diallo", email: "mariama.d@example.com", phone: "+221 76 543 21 09", role: "CLIENT", subscription_status: "ACTIVE", formula_count: 1, created_at: "22/06/2026" },
  { id: 104, first_name: "Moussa", last_name: "Ndiaye", email: "moussa.n@example.com", phone: "+221 70 111 22 33", role: "CLIENT", subscription_status: "INACTIVE", formula_count: 0, created_at: "02/07/2026" },
  { id: 105, first_name: "Fatou", last_name: "Sow", email: "fatou.sow@example.com", phone: "+221 77 999 88 77", role: "CLIENT", subscription_status: "EXPIRED", formula_count: 4, created_at: "25/05/2026" }
];

const INITIAL_CHATS = {
  "awa.diop@example.com": [
    { sender: "client", text: "Bonjour, j'ai une question sur l'émulsifiant BTMS-50.", time: "10:32" },
    { sender: "support", text: "Bonjour Awa ! Oui, je t'écoute. Qu'aimerais-tu savoir ?", time: "10:35" },
    { sender: "client", text: "Puis-je l'utiliser dans une lotion légère à 3% ?", time: "10:36" },
    { sender: "support", text: "Oui tout à fait, entre 2% et 4% c'est idéal pour une lotion fluide et démêlante.", time: "10:38" }
  ],
  "babacar.sy@example.com": [
    { sender: "client", text: "Est-ce que le Rétinol est compatible avec le peroxyde de benzoyle ?", time: "14:15" },
    { sender: "support", text: "Bonjour Babacar, non ! Le peroxyde de benzoyle oxyde et désactive totalement le Rétinol. Il faut éviter de les associer dans la même formule.", time: "14:18" }
  ]
};

// Database persistence helpers
function loadClients() {
  try {
    const stored = localStorage.getItem("miracle_clients_v3");
    if (!stored) {
      localStorage.setItem("miracle_clients_v3", JSON.stringify(INITIAL_CLIENTS));
      return INITIAL_CLIENTS;
    }
    return JSON.parse(stored);
  } catch (e) { return INITIAL_CLIENTS; }
}

function saveClients(clients) {
  try { localStorage.setItem("miracle_clients_v3", JSON.stringify(clients)); } catch (e) {}
}

// In shared.js, we also provide a way to increment formula counts for client
function incrementFormulaCount(email) {
  let clients = loadClients();
  clients = clients.map(c => {
    if (c.email.toLowerCase() === email.toLowerCase()) {
      return { ...c, formula_count: (c.formula_count || 0) + 1 };
    }
    return c;
  });
  saveClients(clients);
}

function loadChats() {
  try {
    const stored = localStorage.getItem("miracle_chats_v3");
    if (!stored) {
      localStorage.setItem("miracle_chats_v3", JSON.stringify(INITIAL_CHATS));
      return INITIAL_CHATS;
    }
    return JSON.parse(stored);
  } catch (e) { return INITIAL_CHATS; }
}

function saveChats(chats) {
  try { localStorage.setItem("miracle_chats_v3", JSON.stringify(chats)); } catch (e) {}
}

// Authentication session helpers
function getCurrentUser() {
  try {
    const stored = localStorage.getItem("miracle_currentUser");
    return stored ? JSON.parse(stored) : null;
  } catch (e) { return null; }
}

function setCurrentUser(user) {
  try {
    if (user) {
      localStorage.setItem("miracle_currentUser", JSON.stringify(user));
    } else {
      localStorage.removeItem("miracle_currentUser");
    }
  } catch (e) {}
}

function getSimulatedEmail() {
  try {
    const stored = localStorage.getItem("miracle_simulatedEmail");
    return stored ? JSON.parse(stored) : null;
  } catch (e) { return null; }
}

// Format currency
function formatCFA(amount) {
  return amount.toLocaleString('fr-FR') + ' FCFA';
}

// Mobile Sidebar Toggle
window.toggleSidebar = function() {
  const sidebar = document.getElementById('dashSidebar');
  const overlay = document.querySelector('.sidebar-overlay');
  if (sidebar) sidebar.classList.toggle('open');
  if (overlay) overlay.classList.toggle('open');
};

// Global UI Helpers
function esc(s) {
  return String(s || "").replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
}

let toastTimer = null;
function showToast(msg) {
  // Remove existing toast
  const oldToast = document.querySelector(".toast");
  if (oldToast) oldToast.remove();
  if (toastTimer) clearTimeout(toastTimer);

  const el = document.createElement("div");
  el.className = "toast";
  el.textContent = msg;
  document.body.appendChild(el);
  
  // Trigger transition
  setTimeout(() => el.style.opacity = "1", 10);

  toastTimer = setTimeout(() => {
    el.style.opacity = "0";
    setTimeout(() => el.remove(), 400);
  }, 2500);
}

// Page authorization guards
function checkAuth(requiredRole = "CLIENT") {
  const user = getCurrentUser();
  if (!user) {
    window.location.href = "login.html";
    return null;
  }
  
  if (requiredRole === "ADMIN" && user.role !== "ADMIN") {
    window.location.href = "dashboard-client.html";
    return null;
  }

  // Force active subscription constraint for client pages
  if (requiredRole === "CLIENT" && user.role !== "ADMIN" && user.subscription_status !== "ACTIVE") {
    alert("Votre abonnement a expiré ou est inactif. Veuillez régulariser votre compte.");
    setCurrentUser(null); // Déconnexion pour éviter une boucle de redirection infinie
    window.location.href = "login.html";
    return null;
  }

  return user;
}

function logout() {
  setCurrentUser(null);
  window.location.href = "login.html";
}
