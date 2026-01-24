<script setup>
import { ref } from 'vue';
import { FileCheck, List, Headphones, ArrowLeft } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';

// 1. Logic: Track which section is currently open
const activeSection = ref('interviews');

// 2. Navigation Function
const setSection = (section) => {
    activeSection.value = section;
};
</script>

<template>
    <div class="grid grid-cols-4 min-h-screen">
        <div class="bg-slate-100 dark:bg-slate-900 p-4 border-r border-slate-200 dark:border-slate-800">
            <div class="text-center mb-10">
                <h1 class="text-2xl mt-5 font-bold text-slate-800 dark:text-white">Shabuj Global</h1>
                <h2 class="mt-2 text-slate-500 font-medium">Hello Aleksandre</h2>
            </div>

            <div class="px-2">
                <ul class="space-y-2">
                    <li @click="setSection('checklist')"
                        :class="[activeSection === 'checklist' ? 'bg-slate-200 dark:bg-slate-800 text-white' : 'text-slate-400']"
                        class="flex items-center justify-between cursor-pointer hover:bg-slate-200 dark:hover:bg-slate-800/50 p-3 rounded-lg transition-all group">
                        <div class="flex items-center gap-3">
                            <List :size="20" />
                            <span class="font-medium">CAS Checklist</span>
                        </div>
                        <span class="text-[10px] bg-slate-700 px-2 py-0.5 rounded text-cyan-400">Done</span>
                    </li>

                    <li @click="setSection('documents')"
                        :class="[activeSection === 'documents' ? 'bg-slate-200 dark:bg-slate-800 text-white' : 'text-slate-400']"
                        class="flex items-center justify-between cursor-pointer hover:bg-slate-200 dark:hover:bg-slate-800/50 p-3 rounded-lg transition-all group">
                        <div class="flex items-center gap-3">
                            <FileCheck :size="20" />
                            <span class="font-medium">General Documents</span>
                        </div>
                        <span class="text-[10px] bg-slate-700 px-2 py-0.5 rounded text-cyan-400">Done</span>
                    </li>

                    <li @click="setSection('interviews')"
                        :class="[activeSection === 'interviews' ? 'bg-slate-200 dark:bg-slate-800 text-white' : 'text-slate-400']"
                        class="flex items-center justify-between cursor-pointer hover:bg-slate-200 dark:hover:bg-slate-800/50 p-3 rounded-lg transition-all group">
                        <div class="flex items-center gap-3">
                            <Headphones :size="20" />
                            <span class="font-medium">Interview</span>
                        </div>
                        <span class="text-[10px] bg-blue-600 px-2 py-0.5 rounded text-white font-bold">New</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="col-span-3 bg-white dark:bg-slate-950 min-h-screen text-black dark:text-white transition-colors">

            <div v-if="activeSection === 'interviews'" class="p-10 animate-in fade-in duration-500">
                <h1 class="text-3xl font-bold mt-5">Interviews</h1>
                <p class="mt-4 text-slate-500 max-w-2xl">
                    You have been invited to complete the following interviews. Please follow the instructions below.
                </p>

                <div class="mt-10">
                    <h1 class="text-xl font-bold">Complete your recorded video interview</h1>
                    <p class="text-sm text-slate-500 mt-2 max-w-md leading-relaxed">
                        You need to complete a short video recording yourself answering some questions.
                        You can do this at a time that is convenient for you. Please have your passport ready.
                    </p>

                    <div
                        class="flex flex-col mt-8 max-w-lg gap-4 p-6 bg-slate-50 dark:bg-slate-900/50 rounded-r-xl rounded-l-sm border-l-[6px] border-orange-500 shadow-sm">
                        <h2 class="text-lg font-bold">Credibility Interview</h2>
                        <p class="text-slate-600 dark:text-slate-400 text-sm">
                            You have been asked to complete a recorded video interview.
                        </p>
                        <Link href="/setup"
                            class="w-fit px-8 py-2.5 bg-[#0070f3] hover:bg-blue-600 text-white font-semibold rounded-lg transition-all active:scale-95">
                            Start interview
                        </Link>
                    </div>
                </div>

                <div class="mt-12 pt-6 border-t border-slate-100 dark:border-slate-800">
                    <button @click="setSection('documents')"
                        class="flex items-center gap-2 text-blue-500 font-medium hover:underline group">
                        <ArrowLeft class="group-hover:-translate-x-1 transition-transform" :size="18" />
                        Previous task: General Documents
                    </button>
                </div>
            </div>

            <div v-if="activeSection === 'documents'" class="p-10 animate-in fade-in duration-500">
                <h1 class="text-3xl font-bold mt-5">General Documents</h1>
                <p class="mt-4 text-slate-500">Upload and manage your required documents here.</p>
                <button @click="setSection('interviews')" class="mt-10 px-6 py-2 bg-slate-800 text-white rounded-lg">Go
                    back to Interview</button>
            </div>

            <div v-if="activeSection === 'checklist'" class="p-10 animate-in fade-in duration-500">
                <h1 class="text-3xl font-bold mt-5">CAS Checklist</h1>
                <p class="mt-4 text-slate-500">Review your final CAS requirements.</p>
            </div>

        </div>
    </div>
</template>