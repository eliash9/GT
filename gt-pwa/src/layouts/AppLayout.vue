<script setup lang="ts">
import { RouterView, RouterLink } from 'vue-router'
</script>

<template>
  <div class="flex flex-col min-h-dvh bg-[#f0f5f2]">
    <main class="flex-1 overflow-y-auto no-scrollbar pb-20">
      <RouterView v-slot="{ Component }">
        <Transition name="page" mode="out-in">
          <component :is="Component" />
        </Transition>
      </RouterView>
    </main>

    <!-- Bottom Navigation - Native App Style -->
    <nav class="fixed bottom-0 left-0 right-0 glass border-t border-white/60 shadow-[0_-4px_20px_rgba(0,0,0,0.08)] z-50 bottom-nav">
      <div class="flex justify-around items-end pt-2 pb-1">
        
        <!-- Beranda -->
        <RouterLink to="/" class="flex flex-col items-center gap-0.5 px-6 py-1 pressable" custom v-slot="{ isActive, navigate }">
          <button @click="navigate" class="flex flex-col items-center gap-0.5 w-full">
            <div class="relative w-10 h-7 flex items-center justify-center">
              <div v-if="isActive" class="absolute inset-0 bg-emerald-100 rounded-xl"></div>
              <svg class="w-5 h-5 relative z-10 transition-colors" :class="isActive ? 'text-[#0f7a5a]' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
              </svg>
            </div>
            <span class="text-[10px] font-bold tracking-tight" :class="isActive ? 'text-[#0f7a5a]' : 'text-gray-400'">Beranda</span>
          </button>
        </RouterLink>

        <!-- Lapor - Center FAB -->
        <RouterLink to="/report" class="relative -mt-5" custom v-slot="{ isActive, navigate }">
          <button @click="navigate" class="relative pressable">
            <div class="w-14 h-14 rounded-full shadow-lg shadow-emerald-300/50 flex items-center justify-center"
                 :class="isActive ? 'shimmer-gold' : 'bg-gradient-to-br from-[#16a374] to-[#0f7a5a]'">
              <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
            </div>
            <span class="block text-[10px] font-bold text-center mt-0.5" :class="isActive ? 'text-[#0f7a5a]' : 'text-gray-400'">Lapor</span>
          </button>
        </RouterLink>

        <!-- Riwayat -->
        <RouterLink to="/history" class="flex flex-col items-center gap-0.5 px-6 py-1 pressable" custom v-slot="{ isActive, navigate }">
          <button @click="navigate" class="flex flex-col items-center gap-0.5 w-full">
            <div class="relative w-10 h-7 flex items-center justify-center">
              <div v-if="isActive" class="absolute inset-0 bg-emerald-100 rounded-xl"></div>
              <svg class="w-5 h-5 relative z-10 transition-colors" :class="isActive ? 'text-[#0f7a5a]' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
            </div>
            <span class="text-[10px] font-bold tracking-tight" :class="isActive ? 'text-[#0f7a5a]' : 'text-gray-400'">Riwayat</span>
          </button>
        </RouterLink>
      </div>
    </nav>
  </div>
</template>
