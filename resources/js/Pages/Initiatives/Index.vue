<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    initiatives: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Инициативы города
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        <div class="mb-4 flex justify-end">
                            <Link
                                :href="route('initiatives.create')"
                                class="rounded-md bg-gray-800 px-4 py-2 text-sm text-white hover:bg-gray-700"
                            >
                                New initiative
                            </Link>
                        </div>

                        <ul v-if="initiatives.length" class="divide-y divide-gray-200">
                            <li
                                v-for="initiative in initiatives"
                                :key="initiative.id"
                                class="flex gap-4 py-4"
                            >
                                <img
                                    v-if="initiative.image_url"
                                    :src="initiative.image_url"
                                    :alt="initiative.title"
                                    class="h-20 w-20 shrink-0 rounded-md object-cover"
                                />
                                <div>
                                    <Link
                                        :href="route('initiatives.show', initiative.id)"
                                        class="font-medium text-indigo-600 hover:text-indigo-800"
                                    >
                                        {{ initiative.title }}
                                    </Link>
                                    <p class="mt-1 line-clamp-2 text-sm text-gray-600">
                                        {{ initiative.description }}
                                    </p>
                                </div>
                            </li>
                        </ul>

                        <p v-else class="text-gray-500">No initiatives yet.</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
