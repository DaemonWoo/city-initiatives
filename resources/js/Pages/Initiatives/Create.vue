<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ImageUpload from '@/Components/ImageUpload.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    title: '',
    description: '',
    image: null,
});

const submit = () => {
    form.post(route('initiatives.store'), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="New initiative" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                New initiative
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 px-6 py-5">
                        <h1 class="text-2xl font-semibold text-gray-900">
                            Create Initiative
                        </h1>

                        <p class="mt-2 text-sm text-gray-500">
                            Describe your idea to improve the city.
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6 p-6">
                        <div>
                            <InputLabel for="title" value="Title" />

                            <TextInput id="title" v-model="form.title" type="text" class="mt-1 block w-full" required />

                            <InputError class="mt-2" :message="form.errors.title" />
                        </div>

                        <div>
                            <InputLabel for="description" value="Description" />

                            <textarea
                                id="description"
                                v-model="form.description"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                rows="5"
                                required
                            />

                            <InputError class="mt-2" :message="form.errors.description" />
                        </div>

                        <div>
                            <ImageUpload
                                id="image"
                                v-model="form.image"
                                label="Image"
                            />

                            <InputError class="mt-2" :message="form.errors.image" />
                        </div>

                        <PrimaryButton :disabled="form.processing">
                            Create
                        </PrimaryButton>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
