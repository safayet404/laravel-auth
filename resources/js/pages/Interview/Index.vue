<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { onBeforeUnmount, ref } from 'vue';

const breadcrumbs = [{ title: 'Interview', href: '/interview' }];

const video = ref(null);

// UI state
const cameraOn = ref(false);
const isRecording = ref(false);
const uploadProgress = ref(0);
const statusText = ref('Idle');

const RECORD_SECONDS = 15;
const countdown = ref(0);
let countdownInterval = null;
let autoStopTimeout = null;

const clearTimers = () => {
    if (countdownInterval) {
        clearInterval(countdownInterval);
        countdownInterval = null;
    }
    if (autoStopTimeout) {
        clearTimeout(autoStopTimeout);
        autoStopTimeout = null;
    }
};

// Media state
let stream = null;
let recorder = null;
let chunks = [];
let lastBlob = null;

// Choose a supported recorder type (important)
const pickMimeType = () => {
    const list = [
        'video/webm;codecs=vp9,opus',
        'video/webm;codecs=vp8,opus',
        'video/webm',
        'video/mp4', // might work on some Safari, not guaranteed
    ];
    return list.find((t) => window.MediaRecorder?.isTypeSupported?.(t)) || '';
};

const startCamera = async () => {
    try {
        statusText.value = 'Requesting camera/mic...';
        uploadProgress.value = 0;

        // If something is already running, stop it first
        await stopCamera();

        stream = await navigator.mediaDevices.getUserMedia({
            video: true,
            audio: true,
        });

        if (!video.value) throw new Error('Video element not found');
        video.value.srcObject = stream;

        cameraOn.value = true;
        statusText.value = 'Camera ON. Recording...';

        // ✅ AUTO START RECORDING
        startRecording();

        // ✅ START COUNTDOWN + AUTO STOP AFTER 20s
        clearTimers();
        countdown.value = RECORD_SECONDS;

        countdownInterval = setInterval(() => {
            countdown.value -= 1;
            if (countdown.value <= 0) {
                countdown.value = 0;
                clearInterval(countdownInterval);
                countdownInterval = null;
            }
        }, 1000);

        autoStopTimeout = setTimeout(async () => {
            await stopCamera(); // stop + upload
        }, RECORD_SECONDS * 1000);
    } catch (err) {
        console.error('Camera/Mic error:', err);
        statusText.value = 'Failed to start camera/mic';
        alert(
            'Could not start camera/mic. Please allow permissions and try again.',
        );
    }
};

const startRecording = () => {
    if (!stream) return;

    chunks = [];
    lastBlob = null;

    const mimeType = pickMimeType();
    const options = mimeType ? { mimeType } : undefined;

    recorder = new MediaRecorder(stream, options);

    recorder.ondataavailable = (e) => {
        if (e.data && e.data.size > 0) chunks.push(e.data);
    };

    recorder.onstart = () => {
        isRecording.value = true;
    };

    recorder.start();
};

const stopRecording = () => {
    return new Promise((resolve) => {
        if (!recorder) return resolve(null);

        recorder.onstop = () => {
            const type = recorder?.mimeType || pickMimeType() || 'video/webm';
            const blob = new Blob(chunks, { type });

            recorder = null;
            chunks = [];
            isRecording.value = false;

            resolve(blob);
        };

        recorder.stop();
    });
};

const uploadBlob = async (blob) => {
    if (!blob) return;

    try {
        statusText.value = 'Uploading...';
        uploadProgress.value = 0;

        const ext = (blob.type || '').includes('mp4') ? 'mp4' : 'webm';
        const file = new File([blob], `interview_${Date.now()}.${ext}`, {
            type: blob.type || 'video/webm',
        });

        const form = new FormData();
        form.append('video', file);

        await axios.post('/int', form, {
            // ✅ DON'T set multipart header manually; browser sets boundary
            onUploadProgress: (evt) => {
                if (!evt.total) return;
                uploadProgress.value = Math.round(
                    (evt.loaded * 100) / evt.total,
                );
            },
        });

        statusText.value = 'Uploaded successfully ✅';
    } catch (err) {
        console.error('Upload failed:', err);
        console.log('Server said:', err.response?.data);
        statusText.value = 'Upload failed ❌';
        alert('Upload failed. Please try again.');
    }
};

const stopCamera = async () => {
    // ✅ stop timers whenever we stop
    clearTimers();
    countdown.value = 0;

    // If nothing is running, just ensure cleanup
    if (!stream && !recorder) {
        cameraOn.value = false;
        isRecording.value = false;
        statusText.value = 'Idle';
        return;
    }

    statusText.value = 'Stopping recording...';

    // Stop recording
    const blob = await stopRecording();
    lastBlob = blob;

    statusText.value = 'Stopping camera...';

    // Stop tracks
    if (stream) {
        stream.getTracks().forEach((t) => t.stop());
        stream = null;
    }

    // Clear video element
    if (video.value) {
        video.value.pause();
        video.value.srcObject = null;
        video.value.removeAttribute('src');
        video.value.load();
    }

    cameraOn.value = false;

    // Auto upload after stop
    if (lastBlob) {
        await uploadBlob(lastBlob);
    } else {
        statusText.value = 'No recording created';
    }
};

onBeforeUnmount(() => {
    clearTimers();
    if (stream) stream.getTracks().forEach((t) => t.stop());
});
</script>

<template>
    <Head title="Interview" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <!-- Big Circle -->
            <div class="flex justify-center">
                <button
                    class="h-60 w-60 rounded-full border-2 border-red-700 bg-white text-lg font-semibold text-black"
                    :disabled="isRecording"
                >
                    Here We Go
                </button>
            </div>

            <!-- ✅ Countdown -->
            <div v-if="isRecording" class="mt-3 text-center text-xl text-white">
                Stopping in: <b>{{ countdown }}</b
                >s
            </div>

            <div class="mt-5 flex justify-center gap-4">
                <button
                    class="bg-white px-3 py-2 font-bold text-black disabled:opacity-60"
                    @click="startCamera"
                    :disabled="isRecording"
                >
                    Start Camera (Auto Record 20s)
                </button>

                <button
                    class="bg-white px-3 py-2 font-bold text-black"
                    @click="stopCamera"
                >
                    Stop Camera (Auto Upload)
                </button>
            </div>

            <div class="mx-auto mt-6 w-full max-w-[640px]">
                <video
                    ref="video"
                    autoplay
                    playsinline
                    muted
                    class="w-full rounded bg-black"
                ></video>

                <div class="mt-3 text-white">
                    Status: <b>{{ statusText }}</b>
                </div>

                <div class="mt-2 text-white">
                    Camera: <b>{{ cameraOn ? 'ON' : 'OFF' }}</b> | Recording:
                    <b>{{ isRecording ? 'YES' : 'NO' }}</b>
                </div>

                <div v-if="uploadProgress > 0" class="mt-2 text-white">
                    Upload: <b>{{ uploadProgress }}%</b>
                    <div class="mt-1 h-2 w-full rounded bg-gray-700">
                        <div
                            class="h-2 rounded bg-green-500"
                            :style="{ width: uploadProgress + '%' }"
                        ></div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
