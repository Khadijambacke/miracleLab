// ─────────────────────────────────────────────────────────────
// SHARED STATE & MOCK DATABASE (ADAPTED FOR LARAVEL)
// ─────────────────────────────────────────────────────────────

function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

// Database persistence helpers
function loadClients() {
  return window.LARAVEL_CLIENTS || [];
}

function saveClients(clients) {
  // Obsolete: géré par Laravel
}

function incrementFormulaCount(email) {
  // Obsolete: géré par Laravel
}

function loadChats() {
  return window.LARAVEL_CHATS || {};
}

function saveChats(chats) {
  // Obsolete: géré par Laravel via fetch dans la vue
}

// Authentication session helpers
function getCurrentUser() {
  return window.LARAVEL_USER || null;
}

function setCurrentUser(user) {
  // Obsolete: géré par les sessions Laravel
}

function getSimulatedEmail() {
  return null;
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
  const oldToast = document.querySelector(".toast");
  if (oldToast) oldToast.remove();
  if (toastTimer) clearTimeout(toastTimer);

  const el = document.createElement("div");
  el.className = "toast";
  el.textContent = msg;
  document.body.appendChild(el);
  
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
    window.location.href = "/login";
    return null;
  }
  
  if (requiredRole === "ADMIN" && user.role !== "ADMIN") {
    window.location.href = "/dashboard";
    return null;
  }

  if (requiredRole === "CLIENT" && user.role !== "ADMIN" && user.statut_abonnement !== "actif") {
    alert("Votre abonnement a expiré ou est inactif. Veuillez régulariser votre compte.");
    window.location.href = "/payment";
    return null;
  }

  return user;
}

function logout() {
  fetch('/logout', {
      method: 'POST',
      headers: {
          'X-CSRF-TOKEN': getCsrfToken(),
          'Content-Type': 'application/json',
          'Accept': 'application/json'
      }
  }).then(() => {
      window.location.href = '/login';
  });
}
