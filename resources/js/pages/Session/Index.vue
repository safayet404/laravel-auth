<template>
    <div class="max-w-3xl p-6">
        <h1 class="mb-4 text-xl font-semibold">Interview Session</h1>

        <div v-if="loading">Loading…</div>

        <div v-else>
            <video ref="videoEl" autoplay playsinline muted class="mb-3 w-full rounded border"></video>

            <div class="flex gap-2">
                <button class="rounded border px-3 py-2" @click="begin" :disabled="phase !== 'idle'">
                    Start
                </button>
                <button class="rounded border px-3 py-2" @click="stopAll" :disabled="phase === 'done'">
                    Stop
                </button>
            </div>

            <div class="relative mb-4 min-h-[140px] rounded border p-4">
                <div v-if="activeQ" :class="pinned
                        ? 'absolute right-2 bottom-2 text-sm opacity-80'
                        : 'text-lg'
                    ">
                    {{ activeQ.type }}
                </div>
                <div class="mt-3 text-sm opacity-80">
                    <div v-if="phase === 'idle'">Ready.</div>
                    <div v-if="phase === 'prep'">Prep: {{ countdown }}s</div>
                    <div v-if="phase === 'answer'">
                        Answer: {{ countdown }}s
                    </div>
                    <div v-if="phase === 'uploading'">Uploading recording…</div>
                    <div v-if="phase === 'done'">Submitted!</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';

const props = defineProps({ interviewId: { type: Number, required: true } });

const loading = ref(true);
const interview = ref(null);

const idx = ref(0);
const phase = ref('idle'); // idle|prep|answer|uploading|done
const countdown = ref(0);
const pinned = ref(false);

const videoEl = ref(null);
let stream = null;
let recorder = null;
let chunks = [];
let timerHandle = null;

const questions = computed(() => interview.value?.questions ?? []);
const activeQ = computed(() => questions.value[idx.value] ?? null);

function clearTimer() {
    if (timerHandle) {
        clearInterval(timerHandle);
        timerHandle = null;
    }
}

function tick(seconds, onDone) {
    countdown.value = seconds;
    clearTimer();
    timerHandle = setInterval(() => {
        countdown.value -= 1;
        if (countdown.value <= 0) {
            clearTimer();
            onDone();
        }
    }, 1000);
}

async function initCamera() {
    stream = await navigator.mediaDevices.getUserMedia({
        video: true,
        audio: true,
    });
    videoEl.value.srcObject = stream;
}

async function load() {
    loading.value = true;
    const { data } = await axios.get(`/interviews/${props.interviewId}`);
    interview.value = data;
    loading.value = false;
}

async function begin() {
    await axios.post(`/interviews/${props.interviewId}/start`);
    // record ONE continuous video for the whole interview:
    chunks = [];
    recorder = new MediaRecorder(stream, { mimeType: 'video/webm' });
    recorder.ondataavailable = (e) => {
        if (e.data.size > 0) chunks.push(e.data);
    };
    recorder.start();

    runQuestion();
}

function runQuestion() {
    if (!activeQ.value) return finish();

    pinned.value = false;
    phase.value = 'prep';
    tick(activeQ.value.prep_seconds, () => {
        pinned.value = true;
        phase.value = 'answer';
        tick(activeQ.value.answer_seconds, () => {
            idx.value += 1;
            runQuestion();
        });
    });
}

let finishing = false;

async function stopAndUpload({ stopCamera = true } = {}) {
    if (finishing) return;
    finishing = true;

    phase.value = 'uploading';
    pinned.value = false;
    clearTimer();

    // 1) Stop recorder and WAIT for stop
    if (recorder && recorder.state !== 'inactive') {
        await new Promise((resolve) => {
            const prevOnStop = recorder.onstop;
            recorder.onstop = (e) => {
                if (typeof prevOnStop === 'function') prevOnStop(e);
                resolve();
            };
            recorder.stop();
        });
    }

    // 2) Optionally stop camera stream
    if (stopCamera && stream) {
        stream.getTracks().forEach((t) => t.stop());
        stream = null;
    }

    // 3) Build blob and upload
    const blob = new Blob(chunks, { type: 'video/webm' });
    console.log('Final blob size:', blob.size);

    if (blob.size === 0) {
        phase.value = 'done';
        finishing = false;
        recorder = null;
        return;
    }

    const form = new FormData();
    form.append('recording', blob, `interview-${props.interviewId}.webm`);

    const a = await axios.post(`/interviews/${props.interviewId}/recording`, form);
    console.log(a);



    // 4) Reset state so it’s definitely stopped
    recorder = null;
    chunks = [];
    phase.value = 'done';
    finishing = false;
}

async function finish() {
    await stopAndUpload({ stopCamera: true });
}

async function stopAll() {
    await stopAndUpload({ stopCamera: true });
}

onMounted(async () => {
    await load();
    await initCamera();
});
</script>
