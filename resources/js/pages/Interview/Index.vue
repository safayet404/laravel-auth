<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { onBeforeUnmount, ref } from 'vue';

const breadcrumbs = [{ title: 'Interview', href: '/interview' }];

// DOM refs
const video = ref(null);

// UI state
const cameraOn = ref(false);
const audioLevel = ref(0); // 0..1
const isAudioRecording = ref(false);
const audioUrl = ref(null);

// Media state
let stream = null;

// Audio meter internals
let audioContext = null;
let analyser = null;
let rafId = null;

// Audio recorder internals
let recorder = null;
let chunks = [];

const setupAudioMeter = (mediaStream) => {
    // Clean any previous meter
    teardownAudioMeter();

    const AudioCtx = window.AudioContext || window.webkitAudioContext;
    audioContext = new AudioCtx();

    const source = audioContext.createMediaStreamSource(mediaStream);
    analyser = audioContext.createAnalyser();
    analyser.fftSize = 256;

    source.connect(analyser);

    const dataArray = new Uint8Array(analyser.frequencyBinCount);

    const tick = () => {
        if (!analyser) return;

        analyser.getByteTimeDomainData(dataArray);

        // RMS volume 0..1
        let sumSquares = 0;
        for (let i = 0; i < dataArray.length; i++) {
            const v = (dataArray[i] - 128) / 128;
            sumSquares += v * v;
        }
        const rms = Math.sqrt(sumSquares / dataArray.length);
        audioLevel.value = Math.min(1, rms * 2); // scale a bit so it's visible

        rafId = requestAnimationFrame(tick);
    };

    tick();
};

const teardownAudioMeter = () => {
    if (rafId) cancelAnimationFrame(rafId);
    rafId = null;

    audioLevel.value = 0;
    analyser = null;

    if (audioContext) {
        audioContext.close();
        audioContext = null;
    }
};

const startCamera = async () => {
    try {
        // Stop first if already on
        if (stream) stopCamera();

        stream = await navigator.mediaDevices.getUserMedia({
            video: true,
            audio: true,
        });

        if (!video.value) throw new Error('Video element not found.');
        video.value.srcObject = stream;

        cameraOn.value = true;
        setupAudioMeter(stream);
    } catch (err) {
        console.error('Camera/Mic error:', err);

        // More helpful messages
        if (err?.name === 'NotAllowedError') {
            alert(
                'Permission denied. Please allow camera and microphone access.',
            );
        } else if (err?.name === 'NotFoundError') {
            alert('No camera/microphone found on this device.');
        } else {
            alert(
                'Could not start camera/microphone. Check browser permissions.',
            );
        }
    }
};

const stopCamera = () => {
    // Stop audio recording if running
    if (isAudioRecording.value) stopAudioRecording();

    // Stop tracks
    if (stream) {
        stream.getTracks().forEach((t) => t.stop());
        stream = null;
    }

    // Clear video element (prevents black box feeling)
    if (video.value) {
        video.value.pause();
        video.value.srcObject = null;
        video.value.removeAttribute('src');
        video.value.load();
    }

    cameraOn.value = false;
    teardownAudioMeter();
};

const startAudioRecording = () => {
    if (!stream) return alert('Start camera/mic first.');

    // Reset old playback URL
    if (audioUrl.value) {
        URL.revokeObjectURL(audioUrl.value);
        audioUrl.value = null;
    }

    chunks = [];

    // Record audio only from the same stream
    const audioStream = new MediaStream(stream.getAudioTracks());

    // Pick a supported mimeType
    const preferred = [
        'audio/webm;codecs=opus',
        'audio/webm',
        'audio/mp4', // some Safari cases
    ];
    const mimeType =
        preferred.find((t) => MediaRecorder.isTypeSupported(t)) || '';

    recorder = new MediaRecorder(
        audioStream,
        mimeType ? { mimeType } : undefined,
    );

    recorder.ondataavailable = (e) => {
        if (e.data && e.data.size > 0) chunks.push(e.data);
    };

    recorder.onstop = () => {
        const type = recorder?.mimeType || mimeType || 'audio/webm';
        const blob = new Blob(chunks, { type });
        audioUrl.value = URL.createObjectURL(blob);
        recorder = null;
        chunks = [];
    };

    recorder.start();
    isAudioRecording.value = true;
};

const stopAudioRecording = () => {
    if (!recorder) {
        isAudioRecording.value = false;
        return;
    }

    // stop() triggers onstop which creates audioUrl
    recorder.stop();
    isAudioRecording.value = false;
};

onBeforeUnmount(() => {
    // Cleanup object URL
    if (audioUrl.value) URL.revokeObjectURL(audioUrl.value);
    stopCamera();
});
</script>

<template>
    <Head title="Interview" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <!-- Controls -->
            <div class="mt-5 flex flex-wrap justify-center gap-4">
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

                <button
                    class="bg-white px-3 py-2 font-bold text-black disabled:opacity-50"
                    :disabled="!cameraOn || isAudioRecording"
                    @click="startAudioRecording"
                >
                    Start Voice Record
                </button>

                <button
                    class="bg-white px-3 py-2 font-bold text-black disabled:opacity-50"
                    :disabled="!isAudioRecording"
                    @click="stopAudioRecording"
                >
                    Stop Voice Record
                </button>
            </div>

            <!-- Video preview -->
            <div class="mx-auto mt-6 w-full max-w-[480px]">
                <video
                    ref="video"
                    autoplay
                    playsinline
                    muted
                    class="w-full rounded bg-black"
                ></video>
            </div>

            <!-- Mic level meter -->
            <div class="mx-auto mt-4 w-full max-w-[480px]">
                <div class="mb-1 text-sm text-white">Mic level</div>
                <div class="h-3 w-full rounded bg-gray-700">
                    <div
                        class="h-3 rounded bg-green-500 transition-all"
                        :style="{ width: `${audioLevel * 100}%` }"
                    ></div>
                </div>
                <div class="mt-1 text-xs text-gray-300">
                    {{
                        cameraOn
                            ? 'Mic is active'
                            : 'Start camera/mic to test voice'
                    }}
                </div>
            </div>

            <!-- Audio playback (voice recording result) -->
            <div v-if="audioUrl" class="mx-auto mt-6 w-full max-w-[480px]">
                <div class="mb-2 text-sm text-white">
                    Recorded voice (preview)
                </div>
                <audio :src="audioUrl" controls class="w-full"></audio>
            </div>
        </div>
    </AppLayout>
</template>
