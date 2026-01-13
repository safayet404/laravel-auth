<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { onBeforeUnmount, ref } from 'vue';

const breadcrumbs = [
    {
        title: 'Interview',
        href: '/interview',
    },
];

const video = ref(null);
let stream = null;

const startCamera = async () => {
    try {
        stream = await navigator.mediaDevices.getUserMedia({
            video: true,
            audio: true,
        });

        video.value.srcObject = stream;
    } catch (err) {
        console.error('Camera/Mic access denied:', err);
        alert('Please allow camera and microphone access.');
    }
};

const stopCamera = () => {
    if (!stream) return;

    if (stream) {
        stream.getTracks().forEach((track) => track.stop());
        stream = null;
    }

    if (video.value) {
        video.value.pause();
        video.value.srcObject = null;
        video.value.removeAttribute('src');
        video.value.load();
    }
};

onBeforeUnmount(stopCamera);
</script>

<template>
    <Head title="Interview" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div>
            <div class="mt-5 flex justify-center gap-4">
                <button
                    class="bg-white px-3 py-2 font-bold text-black"
                    @click="startCamera"
                >
                    Start Camera
                </button>
                <button
                    class="bg-white px-3 py-2 font-bold text-black"
                    @click="stopCamera"
                >
                    Stop Camera
                </button>
            </div>
            <video
                ref="video"
                autoplay
                playsinline
                muted
                style="width: 100%; max-width: 480px"
            ></video>
        </div>
    </AppLayout>
</template>
