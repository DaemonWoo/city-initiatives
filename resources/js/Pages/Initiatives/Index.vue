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
    <Head title="City initiatives" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Инициативы города
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 px-6 py-5 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-900">
                                City initiatives
                            </h1>

                            <p class="mt-2 text-sm text-gray-500">
                                Browse proposals, open an item, and track what is gaining support.
                            </p>
                        </div>

                        <Link
                            :href="route('initiatives.create')"
                            class="mt-4 inline-flex items-center justify-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-700 sm:mt-0"
                        >
                            New initiative
                        </Link>
                    </div>

                    <div class="p-6">
                        <div v-if="initiatives.length" class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                            <article
                                v-for="initiative in initiatives"
                                :key="initiative.id"
                                class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                            >
                            <Link
                                :href="route('initiatives.show', initiative.id)"
                                class="block"
                            >
                                <div class="aspect-[16/10] bg-gray-100">
                                    <img
                                        v-if="initiative.image_url"
                                        :src="initiative.image_url"
                                        :alt="initiative.title"
                                        class="h-full w-full object-cover"
                                    />

                                    <div
                                        v-else
                                        class="flex h-full items-center justify-center text-sm font-medium text-gray-400"
                                    >
                                        No image
                                    </div>
                                </div>

                                <div class="p-5">
                                    <div class="flex items-start justify-between gap-4">
                                        <h3 class="text-base font-semibold text-gray-900">
                                            {{ initiative.title }}
                                        </h3>

                                        <span class="shrink-0 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">
                                            {{ initiative.votes_count }} votes
                                        </span>
                                    </div>

                                    <p class="mt-3 line-clamp-3 text-sm leading-6 text-gray-600">
                                        {{ initiative.description }}
                                    </p>

                                    <div class="mt-4 text-sm font-medium text-indigo-600">
                                        Open initiative
                                    </div>
                                </div>
                            </Link>
                            </article>
                        </div>

                        <div v-else class="flex min-h-64 flex-col items-center justify-center rounded-xl border border-dashed border-gray-300 px-6 py-12 text-center">
                            <h3 class="text-lg font-semibold text-gray-900">
                                No initiatives yet
                            </h3>

                            <p class="mt-2 max-w-md text-sm text-gray-500">
                                Create the first proposal so people can start reading and voting.
                            </p>

                            <Link
                                :href="route('initiatives.create')"
                                class="mt-6 inline-flex items-center justify-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-700"
                            >
                                New initiative
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
