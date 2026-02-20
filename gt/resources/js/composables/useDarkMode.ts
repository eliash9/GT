import { ref, watch, onMounted } from 'vue';

const isDark = ref(false);

export function useDarkMode() {
    onMounted(() => {
        // Check localStorage or system preference
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            isDark.value = true;
            document.documentElement.classList.add('dark');
        } else {
            isDark.value = false;
            document.documentElement.classList.remove('dark');
        }
    });

    const toggleDarkMode = () => {
        isDark.value = !isDark.value;
        if (isDark.value) {
            document.documentElement.classList.add('dark');
            localStorage.theme = 'dark';
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.theme = 'light';
        }
    };

    return {
        isDark,
        toggleDarkMode,
    };
}
