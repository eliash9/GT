<script setup lang="ts">
import { RouterView, RouterLink, useRoute } from 'vue-router'
import { HomeIcon, PencilSquareIcon, DocumentTextIcon } from '@heroicons/vue/24/outline'

const route = useRoute()
</script>

<template>
  <div class="h-screen bg-gray-50 flex flex-col font-sans">
    <main class="flex-1 overflow-y-auto pb-16">
      <RouterView />
    </main>

    <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-[0_-2px_10px_rgba(0,0,0,0.05)] text-xs z-50 flex justify-around">
      <RouterLink to="/" class="flex-1 py-3 flex flex-col items-center justify-center transition"
        :class="route.name === 'Home' ? 'text-green-600' : 'text-gray-500 hover:text-green-600'">
        <HomeIcon class="h-6 w-6 mb-1" />
        <span class="font-medium">Beranda</span>
      </RouterLink>
      
      <RouterLink to="/report" class="flex-1 py-3 flex flex-col items-center justify-center transition"
        :class="route.name === 'Report' ? 'text-green-600' : 'text-gray-500 hover:text-green-600'">
        <PencilSquareIcon class="h-6 w-6 mb-1" />
        <span class="font-medium">Lapor</span>
      </RouterLink>

      <RouterLink to="/history" class="flex-1 py-3 flex flex-col items-center justify-center transition"
        :class="route.name === 'History' ? 'text-green-600' : 'text-gray-500 hover:text-green-600'">
        <DocumentTextIcon class="h-6 w-6 mb-1" />
        <span class="font-medium">Riwayat</span>
      </RouterLink>
    </nav>
  </div>
</template>
