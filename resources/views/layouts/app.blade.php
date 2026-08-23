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
    window.getAuthToken = function() { return localStorage.getItem('token') || ''; };
    window.getAuthHeader = function() {
      return {
        'Authorization': 'Bearer ' + window.getAuthToken(),
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      };
    };

    // Global Toast Notification
    window.showToast = function(message, type = 'success') {
      let toast = document.getElementById('global-toast');
      if (!toast) {
        toast = document.createElement('div');
        toast.id = 'global-toast';
        toast.style.cssText = 'position:fixed;bottom:24px;left:50%;transform:translateX(-50%);z-index:9999;background:var(--accent);color:#fff;padding:12px 24px;border-radius:var(--radius-md);box-shadow:var(--shadow-lg);font-weight:600;font-size:14px;transition:all 0.3s cubic-bezier(0.16,1,0.3,1);opacity:0;pointer-events:none;display:flex;align-items:center;gap:8px;';
        document.body.appendChild(toast);
      }
      toast.textContent = message;
      toast.style.opacity = '1';
      toast.style.transform = 'translateX(-50%) translateY(0)';
      setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(-50%) translateY(10px)';
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
