import '../css/app.css';

import { abilitiesPlugin } from '@casl/vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
// 1. Import the component and CSS
import Vue3EasyDataTable from 'vue3-easy-data-table';
import 'vue3-easy-data-table/dist/style.css';

import { ability } from './acl/ability';
import { initializeTheme } from './composables/useAppearance';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - La Fabrica` : 'La Fabrica'),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const stored = localStorage.getItem('userAbilityRules');

        if (stored) {
            try {
                ability.update(JSON.parse(stored));
            } catch (e) {
                console.log('invalid userAbilityes is localstoreage');
            }
        }

        // 2. Assign to a constant so we can register components
        const app = createApp({ render: () => h(App, props) });

        app.use(plugin).use(abilitiesPlugin, ability);

        // 3. Register the Data Table globally
        app.component('EasyDataTable', Vue3EasyDataTable);

        app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

initializeTheme();
