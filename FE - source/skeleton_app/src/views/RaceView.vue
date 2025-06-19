<template>

    <div class="race-container">

        <div class="p-4 max-w-2xl mx-auto">
            <h1 class="text-2xl font-bold mb-6 text-center">Iscrizione Gara</h1>

            <form @submit.prevent="submitRace" class="space-y-6">
                <div v-if="errors.length" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <p v-for="error in errors" :key="error" class="text-red-600">
                        {{ error }}
                    </p>
                </div>

                <!-- Luogo e Data -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div class="form-group">
                        <span class="p-float-label">
                            <Dropdown id="place" v-model="raceForm.place" :options="races" optionLabel="place"
                                optionValue="id" class="w-full" />
                            <label for="place">Luogo della Gara</label>
                        </span>
                    </div>

                    <div class="form-group">
                        <FloatLabel>
                            <Calendar id="date" v-model="raceForm.date" dateFormat="dd/mm/yy" class="w-full"
                                :disabled="true" />
                            <label for="date">Data della gara</label>
                        </FloatLabel>
                    </div>
                </div>

                <!-- Km -->
                <div class="form-group" v-if="isFirstStepComplete">
                    <span class="p-float-label">
                        <Dropdown id="distance" v-model="raceForm.km" :options="kmOptions" optionLabel="label"
                            optionValue="value" class="w-full" />
                        <label for="distance">Distanza (km)</label>
                    </span>
                </div>

                <!-- Dati Utente -->
                <div v-if="isSecondStepComplete" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <FloatLabel>
                                <InputText id="firstName" v-model="raceForm.firstName" class="w-full" />
                                <label for="firstName">Nome</label>
                            </FloatLabel>
                        </div>

                        <div class="form-group">
                            <FloatLabel>
                                <InputText id="lastName" v-model="raceForm.lastName" class="w-full" />
                                <label for="lastName">Cognome</label>
                            </FloatLabel>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <FloatLabel>
                                <Calendar id="birthDate" v-model="raceForm.birthDate" dateFormat="dd/mm/yy"
                                    :maxDate="maxBirthDate" class="w-full" />
                                <label for="birthDate">Data di nascita</label>
                            </FloatLabel>
                        </div>

                        <div class="form-group">
                            <span class="p-float-label">
                                <Dropdown id="gender" v-model="raceForm.gender" :options="genderOptions"
                                    optionLabel="label" optionValue="value" class="w-full" />
                                <label for="gender">Sesso</label>
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <span class="p-float-label">
                            <Dropdown id="size" v-model="raceForm.size" :options="sizeOptions" optionLabel="label"
                                optionValue="value" class="w-full" />
                            <label for="size">Taglia</label>
                        </span>
                    </div>

                    <div class="my-4">
                        <Card class="bg-blue-50 shadow rounded" style="min-width: 220px;">
                            <template #title>
                                Totale: €{{ raceForm.total }}
                            </template>
                        </Card>
                    </div>

                    <Button type="submit" :disabled="!isFormValid" label="Conferma Iscrizione" class="w-full"
                        severity="primary" raised />
                </div>
            </form>
        </div>
    </div>


    <CardComponent @reloadData="getAllUserRaces" :registrations="registrations" />

</template>

<script setup lang="ts">
import axios, { AxiosError, type AxiosResponse } from 'axios';
import { RouterLink, useRouter } from 'vue-router';
import { getAccessToken } from '@/utils/auth';
import { computed, onMounted, reactive, ref, watch } from 'vue';


// PrimeVue imports
import InputText from 'primevue/inputtext';
import Calendar from 'primevue/calendar';
import Dropdown from 'primevue/dropdown';
import FloatLabel from 'primevue/floatlabel';
import Button from 'primevue/button';
import Card from 'primevue/card';
import CardComponent from '@/components/CardComponent.vue';
import { max } from 'rxjs';


interface Race {
    id: number,
    place: string,
    date: string,
    COEFF: number,
    maxPartecipanti: number
}

onMounted(async () => {
    await getAllRaces();
    await getAllUserRaces();
    console.log('registrations: ', registrations.value);
});


const races = ref<Race[]>([]);
const registrations = ref<any[]>([]);

const raceForm = reactive({
    place: '',
    date: null as Date | null,
    km: null as number | null,
    firstName: '',
    lastName: '',
    birthDate: null as Date | null,
    gender: null as string | null,
    size: null as string | null,
    total: 0,
    paymentCode: null as string | null
});

const selectedRace = computed(() => {
    return races.value.find(race => race.id === Number(raceForm.place));
});

const distancePrices: Record<number, number> = {
    5: 10,
    10: 15,
    21: 20
};


const errors = ref<string[]>([]);
const maxBirthDate = new Date();
maxBirthDate.setFullYear(maxBirthDate.getFullYear() - 18);

const kmOptions = [
    { label: '5 km', value: 5 },
    { label: '10 km', value: 10 },
    { label: '21 km', value: 21 }
];

const genderOptions = [
    { label: 'Maschio', value: 'M' },
    { label: 'Femmina', value: 'F' }
];

const sizeOptions = [
    { label: 'S', value: 'S' },
    { label: 'M', value: 'M' },
    { label: 'L', value: 'L' },
    { label: 'XL', value: 'XL' }
];

const isFirstStepComplete = computed(() => {
    return !!raceForm.place && !!raceForm.date;
});

const isSecondStepComplete = computed(() => {
    return isFirstStepComplete.value && !!raceForm.km;
});

const isFormValid = computed(() => {

    if (!selectedRace.value || !raceForm.km) {
        return false;
    }

    const price = distancePrices[raceForm.km!];
    raceForm.total = selectedRace.value!.COEFF * price;

    const randomCode = Math.floor(10000000 + Math.random() * 90000000);

    raceForm.paymentCode = "RUNNER-" + randomCode;

    return (
        isSecondStepComplete.value &&
        !!raceForm.firstName &&
        !!raceForm.lastName &&
        !!raceForm.birthDate &&
        !!raceForm.gender &&
        !!raceForm.size
    );
});




watch(
    () => raceForm.place,
    (newPlace) => {
        const selectedRace = races.value.find(race => race.id === Number(newPlace))
        if (selectedRace) {
            raceForm.date = new Date(selectedRace.date)
        } else {
            raceForm.date = null
        }
    }
)

const getAllRaces = async () => {
    const token = await getAccessToken();

    axios.get(import.meta.env.VITE_API_URL + 'race/all', {
        headers: {
            Authorization: 'Bearer ' + token
        }
    }).then(response => {
        console.log(response.data);

        races.value = response.data.races;
    }).catch((e: AxiosError) => {
        console.error('Error getting races: ', e);
    });
}

const getAllUserRaces = async () => {
    const token = await getAccessToken();

    axios.get(import.meta.env.VITE_API_URL + 'userRace', {
        headers: {
            Authorization: 'Bearer ' + token
        }
    }).then(response => {
        // console.log(response.data);

        registrations.value = response.data.races;

        console.log('Registrations: ', registrations.value);

    }).catch((e: AxiosError) => {
        console.error('Error getting races: ', e);
    });
}

const submitRace = async () => {

    const token = await getAccessToken();

    axios.post(import.meta.env.VITE_API_URL + 'userRace', {
        name: raceForm.firstName + ' ' + raceForm.lastName,
        size: raceForm.size,
        total: raceForm.total,
        km: raceForm.km,
        race_id: selectedRace.value!.id,
    }, {
        headers: {
            Authorization: 'Bearer ' + token
        }
    }).then(response => {
        console.log(response.data);

        getAllUserRaces();

    }).catch((e: AxiosError) => {
        console.error('Error getting races: ', e);
    });

    resetForm();
};

const resetForm = () => {
    Object.assign(raceForm, {
        place: '',
        date: null,
        km: null,
        firstName: '',
        lastName: '',
        birthDate: null,
        gender: null,
        size: null
    });
}

</script>

<style scoped>
.form-group {
    margin-top: 2rem !important;
}

.race-container {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    margin-top: 3rem;
    /* background-color: #faf9f9; */
}
</style>