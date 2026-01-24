<script setup>
import { ref, onUnmounted, onMounted } from 'vue';
import { CheckCircle2, Mic, MicOff, MoreVertical } from 'lucide-vue-next';

const stream = ref(null);
const videoElement = ref(null);

const setupStatus = ref({
    camera: 'loading',
    mic: 'loading',
    internet: 'loading'
});

const checkInternet = () => {
    setupStatus.value.internet = navigator.onLine ? 'ready' : 'error';
};

const startSetup = async () => {
    checkInternet();
    try {
        const mediaStream = await navigator.mediaDevices.getUserMedia({
            video: { width: 1280, height: 720 },
            audio: true
        });

        stream.value = mediaStream;
        setupStatus.value.camera = 'ready';
        setupStatus.value.mic = 'ready';

        if (videoElement.value) {
            videoElement.value.srcObject = mediaStream;
        }
    } catch (error) {
        console.error("Permission denied or device not found", error);
        setupStatus.value.camera = 'error';
        setupStatus.value.mic = 'error';
    }
};

const stopStream = () => {
    if (stream.value) {
        stream.value.getTracks().forEach(track => track.stop());
    }
};

onMounted(() => {
    window.addEventListener('online', checkInternet);
    window.addEventListener('offline', checkInternet);
    startSetup(); // Call the setup immediately on mount
});

onUnmounted(() => {
    stopStream();
    window.removeEventListener('online', checkInternet);
    window.removeEventListener('offline', checkInternet);
});
</script>

<template>
    <div class="grid grid-cols-4 h-screen overflow-hidden bg-white">

        <div class="col-span-1 bg-white p-12 flex flex-col justify-between border-r border-slate-100">
            <div>
                <h1 class="text-3xl font-bold mb-10 text-slate-800">Check your setup</h1>

                <div class="space-y-8">
                    <div v-for="(item, key) in { camera: 'Built-in Camera', mic: 'Built-in Microphone', internet: 'Internet Connection' }"
                        :key="key" class="flex items-start gap-4">

                        <CheckCircle2 :size="28"
                            :class="setupStatus[key] === 'ready' ? 'text-emerald-500' : 'text-slate-200'"
                            :fill="setupStatus[key] === 'ready' ? 'currentColor' : 'none'" />

                        <div>
                            <p class="font-bold text-slate-700">{{ item }}</p>
                            <p class="text-sm text-slate-400">
                                {{ setupStatus[key] === 'ready' ? 'Looks good!' : 'Checking...' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <button :disabled="setupStatus.camera !== 'ready'"
                class="w-full text-sm py-3 rounded-xl font-bold text-white transition-all bg-blue-700 hover:bg-blue-800 disabled:bg-blue-200 disabled:cursor-not-allowed uppercase tracking-wider">
                Let's Start
            </button>
        </div>

        <div class="col-span-3 bg-blue-600 relative flex items-center justify-center p-12">
            <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500 rounded-full -mr-20 -mt-20 opacity-40"></div>

            <div
                class="relative w-full max-w-4xl aspect-video bg-slate-900 rounded-3xl overflow-hidden shadow-2xl border-4 border-blue-400/30">
                <video ref="videoElement" autoplay playsinline class="w-full h-full object-cover shadow-inner"></video>

                <div class="absolute bottom-6 left-6 p-3 bg-blue-700 rounded-full text-white shadow-lg">
                    <Mic :size="20" v-if="setupStatus.mic === 'ready'" />
                    <MicOff :size="20" v-else />
                </div>

                <div class="absolute top-6 right-6">
                    <button
                        class="p-2 bg-white/20 hover:bg-white/40 backdrop-blur-md rounded-full text-white transition-all">
                        <MoreVertical :size="20" />
                    </button>
                </div>

                <div class="absolute bottom-8 right-8 grid grid-cols-4 gap-2 opacity-30">
                    <div v-for="i in 12" :key="i" class="w-1.5 h-1.5 bg-white rounded-full"></div>
                </div>
            </div>
        </div>
    </div>
</template>