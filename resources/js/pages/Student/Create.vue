<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, ref } from 'vue';

const breadcrumbs = [
    {
        title: 'Student Setup',
        href: '/student',
    },
];

const form = useForm({
    name: '',
    permissions: [],
});

const student = ref({
    first_name: '',
    last_name: '',
    email: '',
});

const profile = ref({
    institution: '',
    program: '',
    intake: '',
    tuition_fee: 0,
    scholarship: null,
    paid_amount: 0,
    remaining_amount: 0,
    notes: '',
});

const studentId = ref(null);
const profileId = ref(null);

const interviewId = ref(null);
const docFile = ref([]);
const questions = ref([]);

const creatingStudent = ref(false);
const creatingProfile = ref(false);
const uploadingDoc = ref(false);
const creatingInterview = ref(false);

const questionsPreview = computed(() =>
    questions.value.length ? JSON.stringify(questions.value, null, 2) : '',
);

function onFile(e) {
    docFile.value = Array.from(e.target.files);
}

async function createStudent() {
    creatingStudent.value = true;

    try {
        const { data } = await axios.post('/student', student.value);
        console.log('student data ', data);

        studentId.value = data?.data.id;
    } catch (error) {
        console.log(error);
    } finally {
        creatingStudent.value = false;
    }
}

async function createProfile() {
    creatingProfile.value = true;
    try {
        const { data } = await axios.post(
            `/students/${studentId.value}/compliance-profiles`,
            profile.value,
        );
        console.log('complaince profile', data);

        profileId.value = data?.data.id;
    } catch (error) {
        console.log(error);
    } finally {
        creatingProfile.value = false;
    }
}

async function uploadDoc() {
    if (!docFile.value) return;

    uploadingDoc.value = true;
    try {
        const formData = new FormData();

        docFile.value.forEach((file) => {
            formData.append('files[]', file);
        });

        const docsUpload = await axios.post(
            `/students/${studentId.value}/documents`,
            formData,
            {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            },
        );

        console.log(docsUpload);

        alert('Upload successful!');
        docFile.value = [];
    } catch (error) {
        console.error('Upload failed:', error.response?.data || error.message);
    } finally {
        uploadingDoc.value = false;
    }
}

async function createInterview() {
    creatingInterview.value = true;

    try {
        const { data } = await axios.post('/interviews', {
            student_id: studentId.value,
            compliance_profile_id: profileId.value,
        });
        console.log('interview create', data);
        interviewId.value = data?.data.id;
    } catch (error) {
        console.log(error);
    } finally {
        creatingInterview.value = false;
    }
}

async function generateQuestions() {
    try {
        const { data } = await axios.post(
            `/interviews/${interviewId.value}/generate-questions`,
            { count: 1 },
        );
        console.log('generate questions', data);

        questions.value = data?.data.questions;
    } catch (error) {}
}
</script>

<template>
    <Head title="User Create" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-3xl p-6">
            <h1 class="mb-4 text-xl font-semibold">Student Setup</h1>

            <div v-if="!studentId" class="mb-4 rounded border p-4">
                <h2 class="mb-2 font-semibold">Student</h2>

                <div class="grid grid-cols-2 gap-2">
                    <input
                        v-model="student.first_name"
                        placeholder="First name"
                        class="rounded border p-2"
                    />
                    <input
                        v-model="student.last_name"
                        placeholder="Last name"
                        class="rounded border p-2"
                    />
                    <input
                        v-model="student.email"
                        type="email"
                        placeholder="Email"
                        class="col-span-2 rounded border p-2"
                    />
                </div>

                <button
                    class="mt-3 rounded border px-3 py-2"
                    @click="createStudent"
                    :disabled="creatingStudent"
                >
                    Create Student
                </button>

                <div v-if="studentId" class="mt-2 text-sm opacity-80">
                    Student ID: {{ studentId }}
                </div>
            </div>

            <div class="mb-4 rounded border p-4" v-if="studentId && !profileId">
                <h2 class="mb-2 font-semibold">Compliance Packet</h2>

                <div class="grid grid-cols-2 gap-2">
                    <input
                        v-model="profile.institution"
                        placeholder="Institution"
                        class="col-span-2 rounded border p-2"
                    />
                    <input
                        v-model="profile.program"
                        placeholder="Program"
                        class="col-span-2 rounded border p-2"
                    />
                    <input
                        v-model="profile.intake"
                        placeholder="intake"
                        class="col-span-2 rounded border p-2"
                    />

                    <input
                        v-model="profile.tuition_fee"
                        type="number"
                        placeholder="Tuition fee"
                        class="rounded border p-2"
                    />
                    <input
                        v-model="profile.scholarship"
                        type="number"
                        placeholder="Scholarship"
                        class="rounded border p-2"
                    />

                    <input
                        v-model="profile.paid_amount"
                        type="number"
                        placeholder="Paid Amount"
                        class="rounded border p-2"
                    />
                    <input
                        v-model="profile.remaining_amount"
                        type="number"
                        placeholder="Remaining Amount"
                        class="rounded border p-2"
                    />

                    <textarea
                        v-model="profile.notes"
                        placeholder="Notes (Optional) "
                        class="col-span-2 rounded border p-2"
                    ></textarea>
                </div>

                <button
                    class="mt-3 rounded border px-3 py-2"
                    @click="createProfile"
                    :disabled="creatingProfile"
                >
                    Save Compliance Packet
                </button>

                <div v-if="profileId" class="mt-2 text-sm opacity-80">
                    Profile ID : {{ profileId }}
                </div>
            </div>

            <div class="mb-4 rounded border p-4" v-if="studentId && profileId">
                <h2 class="mb-2 font-semibold">Upload Documents</h2>

                <input type="file" @change="onFile" multiple />

                <button
                    class="mt-3 rounded border px-3 py-2"
                    @click="uploadDoc"
                    :disabled="!docFile || uploadingDoc"
                >
                    Upload
                </button>
            </div>

            <div class="rounded border p-4" v-if="studentId && profileId">
                <h2 class="mb-2 font-semibold">Create Interview</h2>

                <button
                    class="rounded border px-3 py-2"
                    @click="createInterview"
                    :disabled="creatingInterview"
                >
                    Create Interview
                </button>

                <div v-if="interviewId" class="mt-2">
                    Interview ID : {{ interviewId }}

                    <div class="mt-2 flex gap-2">
                        <button
                            class="rounded border px-3 py-2"
                            @click="generateQuestions"
                        >
                            Generate Questions (AI)
                        </button>
                        <Link
                            class="inline-block rounded border px-3 py-2"
                            :href="`/session/${interviewId}`"
                        >
                            Go to Interview Session
                        </Link>
                        <router-link
                            class="inline-block rounded border px-3 py-2"
                            :to="{
                                name: 'InterviewReview',
                                params: { interviewId },
                            }"
                        >
                            Review/Compliance
                        </router-link>
                    </div>
                </div>
            </div>

            <pre class="mt-4 text-xs opacity-80"> {{ questionsPreview }} </pre>
        </div>
    </AppLayout>
</template>
