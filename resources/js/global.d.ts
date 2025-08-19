// src/types/global.d.ts

import { RouteParamsWithQueryOverload, RouteParam } from 'ziggy-js';
import { Page } from '@inertiajs/core';

declare module 'vue' {
    interface ComponentCustomProperties {
        // Inertia: $page и его структура
        $page: Page<{
            auth: {
                user: {
                    name: string;
                    email_verified_at: string | null;
                    roles?: string[];
                    permissions?: string[];
                } | null;
            };
            url: string;
            unreadCount: number;
        }>;

        // Ziggy: route
        route(name: string, params?: RouteParamsWithQueryOverload | RouteParam, absolute?: boolean): string;

        // Методы из AuthMixin
        auth(): boolean;
        guest(): boolean;
        emailVerified(): boolean;
        hasRole(role: string): boolean;
        can(permission: string): boolean;
        activeLink(pattern: string): string;
    }
}
