<script setup>
import { trans } from "matice";
import { formatDate } from '@/tools';
import { Head, Link } from "@inertiajs/inertia-vue3";
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Inertia } from "@inertiajs/inertia";
import { onMounted, ref, computed } from "vue";

const props = defineProps({
    activities: Object
});

const loadMoreIntersect = ref(null);
const allActivities = ref(props.activities.data);

function loadMore()
{
    if (props.activities.next_page_url === null) {
        return
    }
    Inertia.get(props.activities.next_page_url, {}, {
        preserveState: true,
        preserveScroll: true,
        only: ['activities'],
        onSuccess: () => {
            allActivities.value = [...allActivities.value, ...props.activities.data];
        }
    });
}

onMounted(() => {
    const observer = new IntersectionObserver(entries => entries.forEach(entry => entry.isIntersecting && loadMore(), {
        rootMargin: "-150px 0px 0px 0px"
    }));

    observer.observe(loadMoreIntersect.value);
})

</script>
<template>
    <Head :title="trans('pages.activities')" />

    <BreezeAuthenticatedLayout>
        <div class="wrapper my-3">
            <h2>{{ trans('pages.activities') }}</h2>
            <div class="box mt-3">
                <div v-for="activity in allActivities" :key="activity.id" class="box__section row">
                    <div class="row__column row__column--compact mr-2" style="width: 25px;">
                        <img v-if="activity.user" class="avatar" :src="activity.user.avatar" />
                    </div>
                    <div class="row__column row__column--middle">
                        {{ trans('activities.' + activity.action) }}
                        <Link :href="'/'+activity.entity_type+'s/'+ activity.entity_id">#{{ activity.entity_id }}</Link>
                    </div>
                    <div class="row__column row__column--middle row__column--compact">{{ formatDate(activity.created_at) }}</div>
                </div>
                <span ref="loadMoreIntersect"/>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
