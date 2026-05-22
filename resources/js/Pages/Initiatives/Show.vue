<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    initiative: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head :title="initiative.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ initiative.title }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="space-y-4 p-6 text-gray-900">
                        <img
                            v-if="initiative.image_url"
                            :src="initiative.image_url"
                            :alt="initiative.title"
                            class="max-h-96 w-full rounded-md object-cover"
                        />

                        <p class="whitespace-pre-wrap">
                            {{ initiative.description }}
                        </p>

                        <Link
                            :href="route('initiatives.edit', initiative.id)"
                            class="text-sm text-indigo-600 hover:text-indigo-800"
                        >
                            Edit
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
