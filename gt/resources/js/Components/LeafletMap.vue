<script setup lang="ts">
import { onMounted, onBeforeUnmount, watch, ref } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

// Fix for default marker icons in Vite/webpack builds
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

delete (L.Icon.Default.prototype as any)._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIcon2x,
    iconUrl: markerIcon,
    shadowUrl: markerShadow,
});

// ── Props ──────────────────────────────────────────────────────────
const props = withDefaults(defineProps<{
    /** 'view'   – single marker, read-only
     *  'picker' – click map to set lat/lng
     *  'sebaran'– multiple markers with popups */
    mode?: 'view' | 'picker' | 'sebaran';
    lat?: number | string | null;
    lng?: number | string | null;
    /** Used only in sebaran mode */
    markers?: Array<{
        lat: number | string;
        lng: number | string;
        title: string;
        subtitle?: string;
        status?: string;
        url?: string;
    }>;
    height?: string;
}>(), {
    mode: 'view',
    lat: null,
    lng: null,
    markers: () => [],
    height: '350px',
});

const emit = defineEmits<{
    (e: 'update:lat', value: string): void;
    (e: 'update:lng', value: string): void;
}>();

// ── State ─────────────────────────────────────────────────────────
const mapEl = ref<HTMLElement | null>(null);
let map: L.Map | null = null;
let marker: L.Marker | null = null;

// ── Helpers ───────────────────────────────────────────────────────
const DEFAULT_CENTER: L.LatLngTuple = [-7.0, 113.5]; // Madura / Jawa Timur area
const DEFAULT_ZOOM = 9;

function latNum(v: any): number | null {
    const n = parseFloat(v);
    return isNaN(n) ? null : n;
}

function createPopupHtml(m: typeof props.markers[0]) {
    const statusClass = m.status === 'aktif'
        ? 'background:#d1fae5;color:#065f46'
        : 'background:#fee2e2;color:#991b1b';
    return `
        <div style="min-width:180px;font-family:sans-serif">
            <p style="font-weight:700;font-size:14px;margin:0 0 4px">${m.title}</p>
            ${m.subtitle ? `<p style="font-size:12px;color:#6b7280;margin:0 0 6px">${m.subtitle}</p>` : ''}
            ${m.status ? `<span style="padding:2px 8px;border-radius:9999px;font-size:11px;font-weight:600;${statusClass}">${m.status === 'aktif' ? 'Aktif' : 'Non-Aktif'}</span>` : ''}
            ${m.url ? `<br/><a href="${m.url}" target="_blank" style="display:inline-block;margin-top:8px;font-size:12px;color:#4f46e5;text-decoration:underline">Lihat Detail →</a>` : ''}
        </div>`;
}

// ── Map init ──────────────────────────────────────────────────────
onMounted(() => {
    if (!mapEl.value) return;

    const initLat = latNum(props.lat);
    const initLng = latNum(props.lng);
    const center: L.LatLngTuple = (initLat !== null && initLng !== null)
        ? [initLat, initLng]
        : DEFAULT_CENTER;
    const zoom = (initLat !== null && initLng !== null) ? 14 : DEFAULT_ZOOM;

    map = L.map(mapEl.value).setView(center, zoom);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19,
    }).addTo(map);

    // ── VIEW mode ─────────────────────────────────────────────────
    if (props.mode === 'view') {
        if (initLat !== null && initLng !== null) {
            marker = L.marker([initLat, initLng]).addTo(map!);
        }
    }

    // ── PICKER mode ───────────────────────────────────────────────
    if (props.mode === 'picker') {
        if (initLat !== null && initLng !== null) {
            marker = L.marker([initLat, initLng], { draggable: true }).addTo(map!);
            bindDrag(marker);
        }
        map.on('click', (e: L.LeafletMouseEvent) => {
            const { lat, lng } = e.latlng;
            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                marker = L.marker([lat, lng], { draggable: true }).addTo(map!);
                bindDrag(marker);
            }
            emit('update:lat', lat.toFixed(7));
            emit('update:lng', lng.toFixed(7));
        });
    }

    // ── SEBARAN mode ──────────────────────────────────────────────
    if (props.mode === 'sebaran') {
        const validMarkers = props.markers.filter(m => latNum(m.lat) !== null && latNum(m.lng) !== null);
        validMarkers.forEach(m => {
            const mk = L.marker([latNum(m.lat)!, latNum(m.lng)!]).addTo(map!);
            mk.bindPopup(createPopupHtml(m), { maxWidth: 260 });
        });
        if (validMarkers.length > 0) {
            const bounds = L.latLngBounds(validMarkers.map(m => [latNum(m.lat)!, latNum(m.lng)!] as L.LatLngTuple));
            map.fitBounds(bounds, { padding: [40, 40] });
        }
    }
});

function bindDrag(mk: L.Marker) {
    mk.on('dragend', (e: any) => {
        const pos = e.target.getLatLng();
        emit('update:lat', pos.lat.toFixed(7));
        emit('update:lng', pos.lng.toFixed(7));
    });
}

// Watch lat/lng changes in view mode to re-position marker
watch([() => props.lat, () => props.lng], ([newLat, newLng]) => {
    if (props.mode !== 'view' || !map) return;
    const lt = latNum(newLat);
    const lg = latNum(newLng);
    if (lt !== null && lg !== null) {
        if (!marker) {
            marker = L.marker([lt, lg]).addTo(map);
        } else {
            marker.setLatLng([lt, lg]);
        }
        map.setView([lt, lg], 14);
    }
});

onBeforeUnmount(() => {
    map?.remove();
    map = null;
});
</script>

<template>
    <div ref="mapEl" :style="{ height, width: '100%', borderRadius: '12px', zIndex: 0 }" />
</template>
