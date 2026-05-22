<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    initiative: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    title: props.initiative.title,
    description: props.initiative.description,
    image: null,
});

const onImageChange = (event) => {
    form.image = event.target.files[0] ?? null;
};

const submit = () => {
    form.post(route('initiatives.update', props.initiative.id), {
        forceFormData: true,
        _method: 'put',
    });
};
</script>

<template>
    <Head title="Edit initiative" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Edit initiative
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="space-y-6 p-6">
                        <div v-if="initiative.image_url">
                            <img
                                :src="initiative.image_url"
                                :alt="initiative.title"
                                class="max-h-48 rounded-md object-cover"
                            />
                        </div>

                        <div>
                            <InputLabel for="title" value="Title" />

                            <TextInput
                                id="title"
                                v-model="form.title"
                                type="text"
                                class="mt-1 block w-full"
                                required
                            />

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

                            <InputError
                                class="mt-2"
                                :message="form.errors.description"
                            />
                        </div>

                        <div>
                            <InputLabel for="image" value="Replace image" />

                            <input
                                id="image"
                                type="file"
                                accept="image/*"
                                class="mt-1 block w-full text-sm text-gray-600"
                                @change="onImageChange"
                            />

                            <InputError class="mt-2" :message="form.errors.image" />
                        </div>

                        <PrimaryButton :disabled="form.processing">
                            Save
                        </PrimaryButton>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
