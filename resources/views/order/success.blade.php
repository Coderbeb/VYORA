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
        <h2 class="font-serif font-bold text-2xl tracking-tight text-[var(--text-primary)] mb-1">Order Placed!</h2>
        <p class="text-xs text-[var(--text-secondary)]">Your order has been transmitted directly to the kitchen chefs.</p>
      </div>

      <div class="p-4 rounded-[var(--radius-md)] bg-[var(--bg-base)] border border-[var(--border)] text-xs text-[var(--text-secondary)] space-y-2">
        <div class="flex justify-between">
          <span>Dining Table:</span>
          <span class="font-bold text-[var(--text-primary)]">Table #{{ $tableNumber }}</span>
        </div>
        <div class="flex justify-between">
          <span>Status:</span>
          <span class="font-bold text-amber-600">Preparing in Kitchen</span>
        </div>
      </div>

      <div class="pt-2">
        <a href="/order/{{ $ownerId }}/{{ $tableNumber }}" class="w-full py-3 rounded-[var(--radius-md)] bg-[var(--accent)] hover:bg-[var(--accent-hover)] text-white font-bold text-xs shadow-sm transition flex items-center justify-center gap-2">
          <i data-lucide="utensils" class="w-4 h-4"></i>
          <span>Return to Digital Menu</span>
        </a>
      </div>
    </div>
  </div>
</div>
@endsection
