<script setup>
import { computed, onBeforeUnmount, ref } from 'vue';

const model = defineModel({
    default: null,
});

const props = defineProps({
    id: {
        type: String,
        default: 'image',
    },
    label: {
        type: String,
        default: 'Image',
    },
    existingImageUrl: {
        type: String,
        default: null,
    },
    existingImageAlt: {
        type: String,
        default: '',
    },
    hint: {
        type: String,
        default: 'PNG, JPG, WEBP',
    },
});

const previewUrl = ref(null);

const revokePreview = () => {
    if (previewUrl.value) {
        URL.revokeObjectURL(previewUrl.value);
        previewUrl.value = null;
    }
};

const hasSelectedFile = computed(() => !!model.value);
const displayUrl = computed(() => previewUrl.value || props.existingImageUrl);
const displayAlt = computed(() => {
    if (hasSelectedFile.value) {
        return 'Selected image preview';
    }

    return props.existingImageAlt || 'Image preview';
});
const actionLabel = computed(() => {
    if (hasSelectedFile.value) {
        return 'Replace image';
    }

    if (props.existingImageUrl) {
        return 'Change image';
    }

    return 'Click to upload image';
});
const helperLabel = computed(() => {
    if (hasSelectedFile.value) {
        return model.value?.name ?? 'Selected file';
    }

    if (props.existingImageUrl) {
        return 'Current image';
    }

    return props.hint;
});

const onImageChange = (event) => {
    revokePreview();

    const file = event.target.files[0] ?? null;
    model.value = file;

    if (file) {
        previewUrl.value = URL.createObjectURL(file);
    }
};

onBeforeUnmount(() => {
    revokePreview();
});
</script>

<template>
    <div>
        <span class="block text-sm font-medium text-gray-700">
            {{ label }}
        </span>

        <label
            class="mt-2 flex min-h-56 cursor-pointer flex-col justify-center rounded-xl border-2 border-dashed border-gray-300 p-4 transition hover:border-indigo-500 focus-within:border-indigo-500">
            <input :id="id" type="file" accept="image/*" class="sr-only" :aria-label="label" @change="onImageChange" />

            <template v-if="displayUrl">
                <img :src="displayUrl" :alt="displayAlt" class="h-48 w-full rounded-lg object-cover" />

                <div class="mt-3 flex items-center justify-between gap-4">
                    <span class="truncate text-sm font-medium text-gray-700">
                        {{ helperLabel }}
                    </span>

                    <span class="text-sm font-medium text-indigo-600">
                        {{ actionLabel }}
                    </span>
                </div>
            </template>

            <template v-else>
                <div class="flex flex-col items-center justify-center py-6 text-center">
                    <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16V4m0 0L3 8m4-4l4 4m6 8v4m0 0l-4-4m4 4l-4-4" />
                    </svg>

                    <span class="mt-3 text-sm font-medium text-gray-700">
                        {{ actionLabel }}
                    </span>

                    <span class="mt-1 text-xs text-gray-500">
                        {{ helperLabel }}
                    </span>
                </div>
            </template>
        </label>
    </div>
</template>
