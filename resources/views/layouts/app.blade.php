<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>@yield('title', 'Vyora - Unified OS for Stays & Dining')</title>
  <meta name="description" content="Effortless Hotel PMS, In-Room QR Services, Table QR Ordering, Kitchen KDS, and Cross-Partner Dining Network.">
  
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>
  
  <!-- Pusher JS -->
  <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

  <!-- Tailwind CSS CDN + Vyora Custom Global CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="/css/globals.css">

  <script>
    // Theme Initializer
    (function() {
      const savedTheme = localStorage.getItem('theme');
      if (savedTheme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
      } else {
        document.documentElement.removeAttribute('data-theme');
      }

      // Business Type Initializer (Eliminates sidebar flash on navigation)
      try {
        const ownerRaw = localStorage.getItem('owner');
        const owner = ownerRaw ? JSON.parse(ownerRaw) : null;
        const bType = (owner && owner.businessType) ? owner.businessType : (localStorage.getItem('businessType') || 'RESTAURANT');
        document.documentElement.setAttribute('data-business-type', bType);
      } catch (e) {
        document.documentElement.setAttribute('data-business-type', 'RESTAURANT');
      }
    })();

    // Pusher Config global
    window.PUSHER_KEY = "{{ env('PUSHER_APP_KEY') }}";
    window.PUSHER_CLUSTER = "{{ env('PUSHER_APP_CLUSTER', 'mt1') }}";

    // Audio chime notification synthesizer
    window.playNotificationSound = function() {
      try {
        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        if (!AudioCtx) return;
        const ctx = new AudioCtx();
        const now = ctx.currentTime;

        const playTone = (freq, start, dur) => {
          const osc = ctx.createOscillator();
          const gain = ctx.createGain();
          osc.connect(gain);
          gain.connect(ctx.destination);
          osc.type = 'sine';
          osc.frequency.setValueAtTime(freq, start);
          gain.gain.setValueAtTime(0, start);
          gain.gain.linearRampToValueAtTime(0.15, start + 0.02);
          gain.gain.exponentialRampToValueAtTime(0.001, start + dur);
          osc.start(start);
          osc.stop(start + dur);
        };

        playTone(587.33, now, 0.45);        // D5
        playTone(880.00, now + 0.12, 0.60);  // A5
      } catch (err) {
        console.warn('Audio chime blocked or failed:', err);
      }
    };

    // Shared Auth Helper
    window.getAuthToken = function() { return localStorage.getItem('token') || localStorage.getItem('qrestro_token') || ''; };
    window.getAuthHeader = function() {
      return {
        'Authorization': 'Bearer ' + window.getAuthToken(),
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      };
    };

    // Global Toast Notification with Premium Vector Icons
    window.showToast = function(message, type = 'success') {
      let toast = document.getElementById('global-toast');
      if (!toast) {
        toast = document.createElement('div');
        toast.id = 'global-toast';
        toast.style.cssText = 'position:fixed;bottom:28px;left:50%;transform:translateX(-50%) translateY(12px);z-index:9999;background:rgba(20,28,24,0.94);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);color:#fff;padding:10px 20px;border-radius:9999px;box-shadow:0 12px 32px -4px rgba(0,0,0,0.35),0 0 0 1px rgba(255,255,255,0.15);font-weight:600;font-size:13px;transition:all 0.35s cubic-bezier(0.16,1,0.3,1);opacity:0;pointer-events:none;display:flex;align-items:center;gap:10px;max-width:90vw;';
        document.body.appendChild(toast);
      }

      let iconSvg = `<svg class="w-4 h-4 text-emerald-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>`;
      if (type === 'water') {
        iconSvg = `<svg class="w-4 h-4 text-blue-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/></svg>`;
      } else if (type === 'waiter' || type === 'bell') {
        iconSvg = `<svg class="w-4 h-4 text-amber-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>`;
      } else if (type === 'order') {
        iconSvg = `<svg class="w-4 h-4 text-orange-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>`;
      }

      toast.innerHTML = `${iconSvg}<span class="truncate">${message}</span>`;
      toast.style.opacity = '1';
      toast.style.transform = 'translateX(-50%) translateY(0)';
      clearTimeout(window.__toastTimeout);
      window.__toastTimeout = setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(-50%) translateY(12px)';
      }, 3500);
    };
  </script>
  @stack('styles')
</head>
<body class="antialiased min-h-screen text-[var(--text-primary)] bg-[var(--bg-base)] transition-colors duration-200">
  @yield('content')

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      lucide.createIcons();
    });
  </script>
  @stack('scripts')
</body>
</html>
