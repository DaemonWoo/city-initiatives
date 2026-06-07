<script setup>
import { computed } from 'vue';
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
const canEditInitiative = computed(() => user?.id === props.initiative.user_id);

const form = useForm({
    body: '',
});

const voteForm = useForm({});

const submitComment = () => {
    form.post(route('initiatives.comments.store', props.initiative.id), {
        preserveScroll: true,
        onSuccess: () => form.reset('body'),
    });
};

const canDeleteComment = (comment) => comment.user_id === user.id;

const submitVote = () => {
    voteForm.post(route('initiatives.votes.store', props.initiative.id), {
        preserveScroll: true,
    });
};

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
                <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
                    <div class="space-y-6">
                        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                            <div class="border-b border-gray-200 px-6 py-5">
                                <h1 class="text-2xl font-semibold text-gray-900">
                                    {{ initiative.title }}
                                </h1>

                                <p class="mt-2 text-sm text-gray-500">
                                    By {{ initiative.user.name }}
                                </p>
                            </div>

                            <div class="space-y-5 p-6 text-gray-900">
                                <div v-if="initiative.image_url" class="overflow-hidden rounded-xl border border-gray-200 bg-gray-100">
                                    <img
                                        :src="initiative.image_url"
                                        :alt="initiative.title"
                                        class="max-h-[26rem] w-full object-cover"
                                    />
                                </div>

                                <p class="whitespace-pre-wrap leading-7 text-gray-700">
                                    {{ initiative.description }}
                                </p>
                            </div>
                        </div>

                        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Comments
                                </h3>

                                <ul v-if="initiative.comments.length" class="mt-4 divide-y divide-gray-200">
                                    <li
                                        v-for="comment in initiative.comments"
                                        :key="comment.id"
                                        class="py-4"
                                    >
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="min-w-0">
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ comment.user.name }}
                                                </p>
                                                <p class="mt-1 whitespace-pre-wrap text-sm leading-6 text-gray-700">
                                                    {{ comment.body }}
                                                </p>
                                                <p class="mt-2 text-xs text-gray-500">
                                                    {{ new Date(comment.created_at).toLocaleString() }}
                                                </p>
                                            </div>

                                            <Link
                                                v-if="canDeleteComment(comment)"
                                                :href="route('initiatives.comments.destroy', [initiative.id, comment.id])"
                                                method="delete"
                                                as="button"
                                                class="text-sm font-medium text-red-600 hover:text-red-800"
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

                    <aside class="space-y-6 lg:sticky lg:top-6 lg:self-start">
                        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">
                                    Initiative stats
                                </h3>

                                <dl class="mt-4 space-y-4">
                                    <div class="flex items-center justify-between gap-4">
                                        <dt class="text-sm text-gray-600">
                                            Votes
                                        </dt>
                                        <dd class="text-sm font-semibold text-gray-900">
                                            {{ initiative.votes_count }}
                                        </dd>
                                    </div>

                                    <div class="flex items-center justify-between gap-4">
                                        <dt class="text-sm text-gray-600">
                                            Views
                                        </dt>
                                        <dd class="text-sm font-semibold text-gray-900">
                                            {{ initiative.views_count }}
                                        </dd>
                                    </div>
                                </dl>

                                <div class="mt-6 flex flex-col gap-3">
                                    <button
                                        type="button"
                                        class="inline-flex items-center justify-center rounded-md border border-transparent bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-40"
                                        :disabled="initiative.has_voted || voteForm.processing"
                                        @click="submitVote"
                                    >
                                        {{ initiative.has_voted ? 'You already voted' : 'Vote' }}
                                    </button>

                                    <Link
                                        v-if="canEditInitiative"
                                        :href="route('initiatives.edit', initiative.id)"
                                        class="inline-flex items-center justify-center rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:border-gray-400 hover:bg-gray-50"
                                    >
                                        Edit initiative
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
