<script setup lang="ts">
import { onMounted, ref, onUnmounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import QrcodeVue from 'qrcode.vue'
import { Html5QrcodeScanner } from "html5-qrcode"
import api from '@/utils/api'

const auth = useAuthStore()
const loading = ref(true)

// Attendance state
const isQrModalOpen = ref(false)
const isScannerModalOpen = ref(false)
const scanLoading = ref(false)
const scanMessage = ref<{ text: string; type: 'success' | 'error' | null }>({ text: '', type: null })
let html5QrcodeScanner: any = null

const startScanner = () => {
    isScannerModalOpen.value = true
    scanMessage.value = { text: '', type: null }
    setTimeout(() => {
        html5QrcodeScanner = new Html5QrcodeScanner(
            "reader",
            { fps: 10, qrbox: { width: 240, height: 240 } },
            false
        )
        html5QrcodeScanner.render(onScanSuccess)
    }, 150)
}

const stopScanner = () => {
    if (html5QrcodeScanner) {
        html5QrcodeScanner.clear().catch((error: any) => console.error("Failed to clear scanner", error))
        html5QrcodeScanner = null
    }
    isScannerModalOpen.value = false
}

const onScanSuccess = async (decodedText: string) => {
    if (scanLoading.value) return
    scanLoading.value = true
    scanMessage.value = { text: 'Memproses absensi...', type: null }

    let lat = null, lng = null
    try {
        const pos: any = await new Promise((resolve, reject) => {
            navigator.geolocation.getCurrentPosition(resolve, reject, { timeout: 5000 })
        })
        lat = pos.coords.latitude.toString()
        lng = pos.coords.longitude.toString()
    } catch (e) {
        console.warn('Lokasi tidak tersedia')
    }

    try {
        const res = await api.post('/api/attendance/check-in', { session_qr: decodedText, lat, lng })
        scanMessage.value = { text: res.data.message, type: 'success' }
        setTimeout(() => stopScanner(), 2500)
    } catch (e: any) {
        scanMessage.value = { text: e.response?.data?.message || 'Gagal mencatat absensi.', type: 'error' }
    } finally {
        scanLoading.value = false
    }
}

// Greeting helpers
const getGreeting = () => {
    const h = new Date().getHours()
    if (h < 5) return 'Selamat Malam'
    if (h < 12) return 'Selamat Pagi'
    if (h < 15) return 'Selamat Siang'
    if (h < 18) return 'Selamat Sore'
    return 'Selamat Malam'
}

onMounted(async () => {
    await auth.fetchUser()
    loading.value = false
})

onUnmounted(() => {
    if (html5QrcodeScanner) html5QrcodeScanner.clear().catch(() => {})
})
</script>

<template>
  <div class="min-h-dvh">
    <!-- Header Banner with Islamic Pattern -->
    <div class="pattern-bg relative overflow-hidden" style="padding-top: max(env(safe-area-inset-top), 0px)">
      <!-- Decorative circles -->
      <div class="absolute -right-16 -top-16 w-56 h-56 rounded-full bg-white/5"></div>
      <div class="absolute -right-6 top-8 w-28 h-28 rounded-full bg-white/5"></div>

      <div class="relative z-10 px-5 pt-6 pb-14">
        <!-- Top row -->
        <div class="flex items-start justify-between mb-5">
          <div>
            <p class="text-emerald-300 text-xs font-semibold tracking-widest uppercase mb-0.5">{{ getGreeting() }}</p>
            <div v-if="loading">
              <div class="h-7 w-36 bg-white/10 rounded-lg animate-pulse mb-1"></div>
              <div class="h-4 w-24 bg-white/10 rounded animate-pulse"></div>
            </div>
            <div v-else>
              <h1 class="text-white text-xl font-extrabold leading-tight">
                {{ auth.user?.user?.name?.split(' ')[0] || 'Pengguna' }}
              </h1>
              <div class="flex items-center gap-1.5 mt-1">
                <div class="w-1.5 h-1.5 rounded-full bg-emerald-400"></div>
                <span class="text-emerald-200 text-[11px] font-semibold">
                  {{ auth.user?.details?.is_gt ? 'Guru Tugas' : auth.user?.details?.is_pjgt ? 'PJGT' : 'Korwil' }}
                </span>
              </div>
            </div>
          </div>

          <button @click="auth.logout"
            class="w-9 h-9 rounded-full bg-white/10 border border-white/15 flex items-center justify-center pressable">
            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
          </button>
        </div>

        <!-- Tugas Card (compact inline) -->
        <div class="bg-white/12 border border-white/15 rounded-xl px-4 py-3 backdrop-blur-sm flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-emerald-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
          </div>
          <div class="min-w-0 flex-1">
            <p class="text-emerald-300 text-[9px] font-black uppercase tracking-widest">Penugasan Aktif</p>
            <div v-if="loading" class="h-4 w-40 bg-white/10 rounded animate-pulse mt-0.5"></div>
            <template v-else>
              <p v-if="auth.user?.details?.is_gt" class="text-white font-bold text-xs truncate">
                {{ auth.user?.details?.santri?.penugasan_aktif?.lembaga?.nama || 'Belum ada penugasan' }}
              </p>
              <p v-else-if="auth.user?.details?.is_pjgt || auth.user?.details?.is_korwil" class="text-white font-bold text-xs">
                {{ auth.user?.details?.pjgt?.lembagas?.length || 0 }} Lembaga Tanggung Jawab
              </p>
            </template>
          </div>
        </div>
      </div>
    </div>

    <!-- Content pulled up to overlap header -->
    <div class="relative z-10 px-5 -mt-6 pb-24 space-y-4">

      <!-- === SINGLE ACTION CARD === -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100/80 overflow-hidden">
        <!-- Row 1: Laporan & Riwayat -->
        <div class="grid grid-cols-2 divide-x divide-gray-100">
          <RouterLink to="/report" class="pressable">
            <div class="flex flex-col items-center py-5 gap-2">
              <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
              </div>
              <span class="text-xs font-bold text-gray-700">Buat Laporan</span>
            </div>
          </RouterLink>

          <RouterLink to="/history" class="pressable">
            <div class="flex flex-col items-center py-5 gap-2">
              <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
              </div>
              <span class="text-xs font-bold text-gray-700">Riwayat</span>
            </div>
          </RouterLink>
        </div>

        <!-- Divider -->
        <div class="border-t border-gray-100"></div>

        <!-- Row 2: QR & Scan -->
        <div class="grid grid-cols-2 divide-x divide-gray-100">
          <button @click="isQrModalOpen = true" class="pressable w-full">
            <div class="flex flex-col items-center py-5 gap-2">
              <div class="w-10 h-10 rounded-xl bg-teal-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
              </div>
              <span class="text-xs font-bold text-gray-700">QR Saya</span>
            </div>
          </button>

          <button @click="startScanner" class="pressable w-full">
            <div class="flex flex-col items-center py-5 gap-2">
              <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
              <span class="text-xs font-bold text-gray-700">Scan Absen</span>
            </div>
          </button>
        </div>
      </div>

      <!-- Daftar Lembaga for PJGT/Korwil -->
      <div v-if="!loading && (auth.user?.details?.is_pjgt || auth.user?.details?.is_korwil)" class="space-y-3">
        <div class="flex items-center gap-2 px-0.5">
          <div class="h-4 w-1 bg-[#0f7a5a] rounded-full"></div>
          <h2 class="text-xs font-black text-gray-500 uppercase tracking-widest">Lembaga & GT</h2>
        </div>

        <div v-if="auth.user?.details?.pjgt?.lembagas?.length > 0" class="space-y-2.5">
          <div v-for="l in auth.user?.details?.pjgt?.lembagas" :key="l.id"
               class="bg-white rounded-2xl p-4 border border-gray-100/80 shadow-sm">
            <div class="flex items-start justify-between mb-2">
              <h3 class="font-bold text-gray-800 text-sm leading-snug flex-1 pr-2">{{ l.nama }}</h3>
              <span class="text-[10px] px-2 py-0.5 bg-emerald-50 text-emerald-700 font-black rounded-full flex-shrink-0">Aktif</span>
            </div>
            <div v-if="l.santri_aktif?.length > 0" class="space-y-1">
              <div v-for="p in l.santri_aktif" :key="p.id" class="flex items-center gap-2">
                <div class="w-5 h-5 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                  <span class="text-[9px] font-black text-emerald-700">GT</span>
                </div>
                <span class="text-xs text-gray-600">{{ p.santri?.nama }}</span>
              </div>
            </div>
            <p v-else class="text-[11px] text-gray-400 italic">Belum ada GT aktif</p>
          </div>
        </div>

        <div v-if="auth.user?.details?.is_korwil && auth.user?.details?.pjgt?.wilayahs?.length > 0">
          <div v-for="w in auth.user?.details?.pjgt?.wilayahs" :key="w.id" class="space-y-2 mb-3">
            <p class="text-[10px] font-black text-[#0f7a5a] uppercase tracking-widest px-0.5">Wilayah: {{ w.nama }}</p>
            <div v-for="l in w.lembagas" :key="l.id" class="bg-emerald-50/50 rounded-2xl p-4 border border-emerald-100/50">
              <h4 class="font-bold text-gray-800 text-sm mb-1">{{ l.nama }}</h4>
              <div v-if="l.santri_aktif?.length > 0" class="space-y-1">
                <div v-for="p in l.santri_aktif" :key="p.id" class="flex items-center gap-2">
                  <div class="w-4 h-4 rounded-full bg-[#0f7a5a]/10 flex items-center justify-center">
                    <div class="w-1.5 h-1.5 rounded-full bg-[#0f7a5a]"></div>
                  </div>
                  <span class="text-xs text-gray-600">{{ p.santri?.nama }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Info Banner -->
      <div class="bg-white rounded-2xl p-4 border border-amber-100/80 shadow-sm flex items-start gap-3">
        <div class="w-8 h-8 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0 mt-0.5">
          <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div>
          <p class="text-xs font-bold text-gray-700 mb-0.5">Pengingat Laporan</p>
          <p class="text-[11px] text-gray-500 leading-relaxed">
            Kirim laporan sebelum akhir periode bulan berjalan. Laporan yang telah dikirim langsung dievaluasi admin.
          </p>
        </div>
      </div>
    </div>

    <!-- ======= QR Modal ======= -->
    <Transition name="page">
      <div v-if="isQrModalOpen" class="fixed inset-0 z-[60] flex items-end justify-center bg-black/40 backdrop-blur-sm">
        <div class="bg-white w-full max-w-sm rounded-t-3xl pt-2 pb-8 px-6 shadow-2xl">
          <div class="w-10 h-1 bg-gray-200 rounded-full mx-auto mb-5"></div>

          <div class="text-center mb-5">
            <h3 class="text-lg font-extrabold text-gray-900">QR Code Absensi</h3>
            <p class="text-xs text-gray-400 mt-0.5">Tunjukkan ke Admin untuk dicatat</p>
          </div>

          <div class="flex justify-center mb-5">
            <div class="p-4 bg-gray-50 rounded-2xl border-4 border-white shadow-inner">
              <qrcode-vue :value="auth.user?.user?.personal_qr_token" :size="190" level="H" />
            </div>
          </div>

          <div class="bg-[#f0f5f2] rounded-xl p-3 mb-5 text-center">
            <p class="text-xs text-[#0f7a5a] font-black uppercase tracking-widest mb-0.5">Pemilik</p>
            <p class="font-bold text-gray-800 text-sm">{{ auth.user?.user?.name }}</p>
            <p class="text-xs text-gray-500">{{ auth.user?.user?.email }}</p>
          </div>

          <button @click="isQrModalOpen = false"
            class="w-full py-3.5 bg-gray-900 text-white rounded-xl font-bold text-sm pressable">
            Tutup
          </button>
        </div>
      </div>
    </Transition>

    <!-- ======= Scanner Modal ======= -->
    <Transition name="page">
      <div v-if="isScannerModalOpen" class="fixed inset-0 z-[60] flex flex-col bg-[#f0f5f2]" style="padding-top: env(safe-area-inset-top)">
        <div class="pattern-bg px-5 py-4 flex items-center gap-3">
          <button @click="stopScanner" class="w-9 h-9 rounded-full bg-white/15 flex items-center justify-center pressable">
            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
          <div>
            <h2 class="text-white font-extrabold text-base">Scan QR Absensi</h2>
            <p class="text-emerald-200 text-xs">Arahkan kamera ke QR Code kegiatan</p>
          </div>
        </div>

        <div class="flex-1 flex flex-col items-center justify-center px-5 gap-5">
          <div id="reader" class="w-full max-w-xs rounded-3xl overflow-hidden shadow-2xl"></div>

          <Transition name="page">
            <div v-if="scanMessage.text"
              class="w-full max-w-xs p-4 rounded-2xl text-center font-bold text-sm"
              :class="{
                'bg-emerald-100 text-emerald-800': scanMessage.type === 'success',
                'bg-red-100 text-red-700': scanMessage.type === 'error',
                'bg-blue-100 text-blue-700 animate-pulse': !scanMessage.type
              }">
              {{ scanMessage.text }}
            </div>
          </Transition>
        </div>
      </div>
    </Transition>
  </div>
</template>
