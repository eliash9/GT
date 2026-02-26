<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { onMounted, onUnmounted, ref } from 'vue';
import { Html5QrcodeScanner, Html5QrcodeSupportedFormats } from "html5-qrcode";
import axios from 'axios';

const props = defineProps<{
    session: any;
}>();

const scanResult = ref<string | null>(null);
const isLoading = ref(false);
const message = ref<{ text: string; type: 'success' | 'error' | 'info' | null }>({ text: '', type: null });
const lastUser = ref<{ name: string; time: string; type: string } | null>(null);

let scanner: any = null;

const onScanSuccess = async (decodedText: string) => {
    if (isLoading.value) return;
    
    // Simple deduplication: don't scan the same thing twice in 3 seconds
    if (scanResult.value === decodedText) return;
    
    scanResult.value = decodedText;
    isLoading.value = true;
    
    try {
        const response = await axios.post(route('attendance.check-in'), {
            session_qr: props.session.qr_code,
            user_qr: decodedText,
        });
        
        message.value = { text: response.data.message, type: 'success' };
        lastUser.value = { 
            name: response.data.user, 
            time: response.data.time,
            type: response.data.type
        };

    } catch (error: any) {
        const msg = error.response?.data?.message || 'Terjadi kesalahan sistem.';
        message.value = { text: msg, type: 'error' };
    } finally {
        isLoading.value = false;
        // Reset scan result after delay to allow next scan
        setTimeout(() => {
            scanResult.value = null;
        }, 3000);
    }
};

onMounted(() => {
    scanner = new Html5QrcodeScanner(
        "qr-reader",
        { 
            fps: 10, 
            qrbox: { width: 250, height: 250 },
            formatsToSupport: [ Html5QrcodeSupportedFormats.QR_CODE ]
        },
        /* verbose= */ false
    );
    scanner.render(onScanSuccess, (err: any) => {
        // quiet fail on scan error (usually just frame errors)
    });
});

onUnmounted(() => {
    if (scanner) {
        scanner.clear().catch((error: any) => console.error("Failed to clear scanner", error));
    }
});
</script>

<template>
    <Head title="Scanner Absensi Admin" />
    <AuthenticatedLayout>
        <div class="max-w-2xl mx-auto space-y-6">
            <div class="flex items-center gap-3">
                <Link :href="route('attendance-sessions.show', session.id)" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Standby Scanner</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Sesi: {{ session.title }}</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-xl overflow-hidden">
                <div class="p-4 bg-indigo-600 text-white flex justify-between items-center">
                    <span class="text-xs font-bold uppercase tracking-widest">Kamera Aktif</span>
                    <div v-if="isLoading" class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-white rounded-full animate-ping"></div>
                        <span class="text-[10px] font-bold">Memproses...</span>
                    </div>
                </div>

                <div id="qr-reader" class="w-full"></div>

                <div v-if="message.text" class="p-6 text-center animate-in fade-in slide-in-from-bottom-4 duration-300"
                    :class="message.type === 'success' ? 'bg-emerald-50 text-emerald-800' : 'bg-red-50 text-red-800'">
                    <p class="font-bold">{{ message.text }}</p>
                </div>

                <div v-if="lastUser" class="p-6 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-black text-xl">
                                {{ lastUser.name[0] }}
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Berhasil Discan:</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-white leading-none mb-1">{{ lastUser.name }}</p>
                                <span class="text-[10px] font-black uppercase text-indigo-500 tracking-tighter">{{ lastUser.type }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-black text-gray-900 dark:text-white">{{ lastUser.time }}</p>
                            <p class="text-[10px] font-bold text-gray-400 uppercase">Waktu Hadir</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center">
                <p class="text-xs text-gray-400">Arahkan kamera ke QR Code yang ada di aplikasi PWA (ID Card Digital) atau QR Code cetak milik Guru Tugas.</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
#qr-reader {
    border: none !important;
}
#qr-reader__scan_region {
    background: #f8fafc;
}
#qr-reader img {
    display: none;
}
#qr-reader button {
    @apply px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-bold text-gray-600 hover:bg-gray-50 transition-colors mt-4;
}
</style>
