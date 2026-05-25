<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    initiative: {
        type: Object,
        required: true,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    body: '',
});

const submitComment = () => {
    form.post(route('initiatives.comments.store', props.initiative.id), {
        preserveScroll: true,
        onSuccess: () => form.reset('body'),
    });
};

const canDeleteComment = (comment) => comment.user_id === user.id;

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
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
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

                        <p class="text-sm text-gray-500">
                            By {{ initiative.user.name }}
                        </p>

                        <Link
                            :href="route('initiatives.edit', initiative.id)"
                            class="text-sm text-indigo-600 hover:text-indigo-800"
                        >
                            Edit
                        </Link>
                    </div>
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900">
                            Comments
                        </h3>

                        <ul
                            v-if="initiative.comments.length"
                            class="mt-4 divide-y divide-gray-200"
                        >
                            <li
                                v-for="comment in initiative.comments"
                                :key="comment.id"
                                class="py-4"
                            >
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ comment.user.name }}
                                        </p>
                                        <p class="mt-1 whitespace-pre-wrap text-gray-700">
                                            {{ comment.body }}
                                        </p>
                                        <p class="mt-1 text-xs text-gray-500">
                                            {{ new Date(comment.created_at).toLocaleString() }}
                                        </p>
                                    </div>

                                    <Link
                                        v-if="canDeleteComment(comment)"
                                        :href="route('initiatives.comments.destroy', [initiative.id, comment.id])"
                                        method="delete"
                                        as="button"
                                        class="text-sm text-red-600 hover:text-red-800"
                                        preserve-scroll
                                    >
                                        Delete
                                    </Link>
                                </div>
                            </li>
                        </ul>

                        <p v-else class="mt-4 text-sm text-gray-500">
                            No comments yet. Be the first to comment.
                        </p>

                        <form
                            @submit.prevent="submitComment"
                            class="mt-6 space-y-4 border-t border-gray-200 pt-6"
                        >
                            <div>
                                <InputLabel for="body" value="Add a comment" />

                                <textarea
                                    id="body"
                                    v-model="form.body"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    rows="3"
                                    required
                                />

                                <InputError class="mt-2" :message="form.errors.body" />
                            </div>

                            <PrimaryButton :disabled="form.processing">
                                Post comment
                            </PrimaryButton>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
