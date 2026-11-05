@extends('layouts.app')

@section('title', 'Order Received - Vyora')

@section('content')
<div class="min-h-screen flex flex-col justify-center items-center p-4 bg-[var(--bg-base)] text-[var(--text-primary)]">
  <div class="w-full max-w-md text-center">
    <div class="p-8 rounded-[var(--radius-xl)] bg-[var(--bg-surface)] border border-[var(--border)] shadow-[var(--shadow-lg)] space-y-6">
      <div class="w-16 h-16 rounded-full bg-emerald-500/10 text-emerald-600 flex items-center justify-center mx-auto shadow-sm animate-bounce">
        <i data-lucide="check-circle" class="w-8 h-8"></i>
      </div>

      <div>
        <h2 class="font-serif font-bold text-2xl tracking-tight text-[var(--text-primary)] mb-1">
          Order Placed! <span class="block text-base font-sans font-bold text-[var(--accent)] mt-0.5">ऑर्डर दर्ज हो गया!</span>
        </h2>
        <p class="text-xs text-[var(--text-secondary)]">Your order has been transmitted directly to the kitchen chefs.</p>
        <p class="text-[11px] text-[var(--text-muted)] mt-0.5">आपका ऑर्डर सीधा किचन में भेज दिया गया है।</p>
      </div>

      <div class="p-4 rounded-[var(--radius-md)] bg-[var(--bg-base)] border border-[var(--border)] text-xs text-[var(--text-secondary)] space-y-2">
        <div class="flex justify-between items-center">
          <span>Dining Table · मेज़:</span>
          <span class="font-bold text-[var(--text-primary)]">Table #{{ $tableNumber }}</span>
        </div>
        <div class="flex justify-between items-center">
          <span>Status · स्थिति:</span>
          <span class="font-bold text-amber-600 dark:text-amber-400 text-right">
            <span>Cooking in Kitchen</span>
            <span class="block text-[11px] font-semibold text-amber-700 dark:text-amber-300">किचन में पक रहा है</span>
          </span>
        </div>
      </div>

      <div class="pt-2">
        <a href="/order/{{ $ownerId }}/{{ $tableNumber }}" class="w-full py-3 rounded-[var(--radius-md)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white font-bold text-xs shadow-sm transition flex items-center justify-center gap-2 active:scale-95">
          <i data-lucide="utensils" class="w-4 h-4"></i>
          <span>Return to Digital Menu · मेनू पर लौटें</span>
        </a>
      </div>
    </div>
  </div>
</div>
@endsection
