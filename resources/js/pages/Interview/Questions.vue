<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { CheckCircle2, Play, Square, Video, Mic } from 'lucide-vue-next';

// --- State Management ---
const interviewStarted = ref(false);
const currentQuestionIndex = ref(0);
const isPreparing = ref(false);
const isRecording = ref(false);
const prepTimer = ref(0);
const recordingTimer = ref(0);
const videoElement = ref(null);
let timerInterval = null;

// --- Interview Data ---
const questions = ref([
    {
        id: 1,
        text: "Please read the following statement out loud: I confirm that I understand the seriousness of this interview process and that all answers are my own.",
        prepTime: 10,
        time: 30
    },
    {
        id: 2,
        text: "Why did you choose this specific university and course for your higher education?",
        prepTime: 30,
        time: 60
    },
    {
        id: 3,
        text: "Will you stay in this country after completing your course, or what are your future plans?",
        prepTime: 20,
        time: 45
    },
]);

const currentQuestion = computed(() => questions.value[currentQuestionIndex.value]);

// --- Lifecycle & Camera ---
onMounted(async () => {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({
            video: true,
            audio: true
        });
        if (videoElement.value) {
            videoElement.value.srcObject = stream;
        }
    } catch (error) {
        console.error("Camera access denied", error);
    }
});

onUnmounted(() => {
    clearInterval(timerInterval);
});

// --- Interview Logic ---
const startInterviewFlow = () => {
    interviewStarted.value = true;
    startPreparation();
};

const startPreparation = () => {
    isPreparing.value = true;
    isRecording.value = false;
    prepTimer.value = currentQuestion.value.prepTime;

    clearInterval(timerInterval);
    timerInterval = setInterval(() => {
        if (prepTimer.value > 0) {
            prepTimer.value--;
        } else {
            startRecording();
        }
    }, 1000);
};

const startRecording = () => {
    clearInterval(timerInterval);
    isPreparing.value = false;
    isRecording.value = true;
    recordingTimer.value = currentQuestion.value.time;

    timerInterval = setInterval(() => {
        if (recordingTimer.value > 0) {
            recordingTimer.value--;
        } else {
            stopRecording();
        }
    }, 1000);
};

const stopRecording = () => {
    clearInterval(timerInterval);
    isRecording.value = false;
};

const stopMediaTracks = () => {
    if (stream.value) {
        stream.value.getTracks().forEach(track => {
            track.stop(); // This physically turns off the camera light
        });
        stream.value = null; // Clear the reference
    }
};

const nextQuestion = () => {
    if (currentQuestionIndex.value < questions.value.length - 1) {
        currentQuestionIndex.value++;
        startPreparation();
    } else {
        stopMediaTracks();
        alert("Interview Complete! All responses have been saved.");
        interviewStarted.value = false; // Reset for demo purposes
        currentQuestionIndex.value = 0;
    }
};
</script>

<template>
    <div class="grid grid-cols-10 h-screen overflow-hidden bg-white font-sans">

        <div class="col-span-3 p-12 border-r border-slate-100 flex flex-col justify-center bg-slate-50/50">

            <div v-if="!isRecording && !isPreparing">
                <h3 class="text-xl font-bold mb-8 text-slate-800">Your Progress</h3>
                <div v-for="(q, index) in questions" :key="q.id"
                    class="flex items-center gap-4 p-4 rounded-xl mb-4 transition-all" :class="index === currentQuestionIndex
                        ? 'bg-blue-50 border-l-4 border-blue-600 opacity-100 shadow-sm'
                        : index < currentQuestionIndex ? 'opacity-100' : 'opacity-40'">

                    <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center text-sm font-bold"
                        :class="index < currentQuestionIndex ? 'bg-emerald-500 border-emerald-500 text-white' : 'text-slate-600'">
                        <CheckCircle2 v-if="index < currentQuestionIndex" :size="16" />
                        <span v-else>{{ index + 1 }}</span>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-slate-800">Question {{ index + 1 }}</p>
                        <p class="text-xs text-slate-500">Prep: {{ q.prepTime }}s • Record: {{ q.time }}s</p>
                    </div>
                </div>

                <button v-if="!interviewStarted" @click="startInterviewFlow"
                    class="w-full mt-6 px-6 py-4 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-200 uppercase tracking-wider">
                    Start Interview
                </button>
            </div>

            <div v-else class="animate-in fade-in slide-in-from-left-4 duration-500">
                <p class="text-xs text-blue-600 font-bold mb-2 uppercase tracking-widest">Ongoing Question</p>
                <h2 class="text-2xl font-bold text-slate-800 leading-snug mb-10">{{ currentQuestion.text }}</h2>

                <div v-if="isPreparing"
                    class="flex flex-col items-center p-8 bg-white rounded-3xl shadow-sm border border-slate-100">
                    <p class="text-slate-400 text-xs font-bold uppercase mb-4">Preparation Time Remaining</p>
                    <div
                        class="relative w-32 h-32 flex items-center justify-center rounded-full border-[6px] border-blue-600">
                        <span class="text-3xl font-mono font-bold text-blue-600">
                            {{ prepTimer }}<span class="text-sm">s</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-span-7 bg-[#0047FF] relative flex items-center justify-center p-16">

            <div class="absolute top-0 right-0 w-80 h-80 bg-blue-500 rounded-full -mr-20 -mt-20 opacity-30"></div>
            <div class="absolute bottom-0 left-0 w-40 h-40 bg-blue-400 rounded-full -ml-10 -mb-10 opacity-20"></div>

            <div
                class="relative w-full max-w-4xl aspect-video bg-slate-900 rounded-[2.5rem] overflow-hidden shadow-[0_35px_60px_-15px_rgba(0,0,0,0.5)] border-4 border-blue-400/30">

                <video ref="videoElement" autoplay playsinline
                    class="w-full h-full object-cover grayscale-[0.2]"></video>

                <div v-if="isPreparing"
                    class="absolute inset-0 bg-white/90 backdrop-blur-md flex flex-col items-center justify-center text-center p-10">
                    <div class="p-4 bg-blue-50 rounded-full mb-6">
                        <Video :size="40" class="text-blue-600" />
                    </div>
                    <p class="text-slate-500 mb-2 font-medium">Automatic start in {{ prepTimer }} seconds</p>
                    <h3 class="text-2xl font-bold text-slate-800 mb-8 max-w-md">Adjust your camera and prepare your
                        answer.</h3>

                    <button @click="startRecording"
                        class="px-12 py-4 bg-blue-600 text-white font-extrabold rounded-2xl hover:scale-105 active:scale-95 transition-all uppercase tracking-widest shadow-xl shadow-blue-200">
                        Start Recording Now
                    </button>
                </div>

                <div v-if="isRecording"
                    class="absolute top-8 left-1/2 -translate-x-1/2 bg-black/60 backdrop-blur-md px-6 py-2 rounded-full flex items-center gap-3 text-white border border-white/20">
                    <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse shadow-[0_0_10px_rgba(239,68,68,0.8)]">
                    </div>
                    <span class="text-sm font-mono font-bold tracking-tighter">REC: 00:{{
                        recordingTimer.toString().padStart(2, '0') }}</span>
                </div>

                <div v-if="isRecording" class="absolute bottom-10 left-1/2 -translate-x-1/2">
                    <button @click="stopRecording"
                        class="px-10 py-3 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center gap-3 transition-all font-bold uppercase text-xs tracking-widest shadow-lg">
                        <Square :size="16" fill="currentColor" /> Stop Recording
                    </button>
                </div>

                <div v-if="!isRecording && !isPreparing && interviewStarted"
                    class="absolute inset-0 bg-white flex flex-col items-center justify-center animate-in zoom-in duration-300">
                    <h3 class="text-2xl font-bold mb-8 text-slate-800">Answer Recorded</h3>

                    <div
                        class="w-3/5 aspect-video bg-slate-100 rounded-[2rem] mb-10 flex flex-col items-center justify-center relative group shadow-inner border-2 border-slate-50">
                        <div
                            class="p-6 bg-blue-600 rounded-full shadow-xl shadow-blue-100 cursor-pointer hover:scale-110 transition-transform">
                            <Play :size="32" class="text-white fill-current ml-1" />
                        </div>
                        <p class="mt-4 text-sm font-bold text-blue-600">Review Response</p>

                        <div
                            class="absolute top-4 right-4 flex items-center gap-2 bg-white px-3 py-1 rounded-full shadow-sm">
                            <CheckCircle2 :size="14" class="text-emerald-500" />
                            <span class="text-[10px] font-bold text-slate-500 uppercase">Saved</span>
                        </div>
                    </div>

                    <button @click="nextQuestion"
                        class="px-20 py-4 bg-blue-700 text-white font-black rounded-2xl uppercase tracking-[0.2em] hover:bg-blue-800 hover:shadow-2xl transition-all">
                        Submit & Continue
                    </button>
                </div>

            </div>

            <div class="absolute bottom-12 right-12 grid grid-cols-4 gap-3 opacity-20">
                <div v-for="i in 16" :key="i" class="w-1.5 h-1.5 bg-white rounded-full"></div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Optional: specific fonts or custom easing */
.font-sans {
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
}
</style>