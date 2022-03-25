require('./bootstrap');

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => require(`./Pages/${name}.vue`),
    setup({ el, app, props, plugin }) {
        return createApp({ render: () => h(app, props) })
            .use(plugin)
            .mixin({ methods: { route } })
            .directive('click-outside', {
                created: function (e, binding, vnode) {
                    document.body.addEventListener('click', (event) => {
                        if (!(e == event.target || e.contains(event.target))) {
                            binding.value();
                        }
                    })
                },
                unmounted: function (e) {
                    document.body.removeEventListener('click', e.clickOutsideEvent)
                }
            })
            .mount(el);
    },
});

InertiaProgress.init({ color: '#4B5563' });
