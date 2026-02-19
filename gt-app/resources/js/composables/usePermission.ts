import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { PageProps } from '@/types';

/**
 * Composable for checking user permissions and roles in Vue components.
 *
 * Usage:
 *   const { can, hasRole, isSuperAdmin } = usePermission();
 *   if (can('edit users')) { ... }
 *   if (hasRole('admin')) { ... }
 */
export function usePermission() {
    const page = usePage<PageProps>();

    const permissions = computed(() => page.props.auth.permissions ?? []);
    const roles = computed(() => page.props.auth.roles ?? []);

    /**
     * Check if the user has a specific permission.
     * super-admin always returns true.
     */
    const can = (permission: string): boolean => {
        if (roles.value.includes('super-admin')) return true;
        return permissions.value.includes(permission);
    };

    /**
     * Check if the user has ANY of the given permissions.
     */
    const canAny = (...perms: string[]): boolean => {
        return perms.some((p) => can(p));
    };

    /**
     * Check if the user has ALL of the given permissions.
     */
    const canAll = (...perms: string[]): boolean => {
        return perms.every((p) => can(p));
    };

    /**
     * Check if the user has a specific role.
     */
    const hasRole = (role: string): boolean => roles.value.includes(role);

    /**
     * Check if the user has any of the given roles.
     */
    const hasAnyRole = (...roleList: string[]): boolean => {
        return roleList.some((r) => roles.value.includes(r));
    };

    const isSuperAdmin = computed(() => roles.value.includes('super-admin'));

    return { can, canAny, canAll, hasRole, hasAnyRole, isSuperAdmin, permissions, roles };
}
