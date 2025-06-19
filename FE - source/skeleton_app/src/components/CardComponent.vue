<template>
    <div class="p-6 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center" style="margin-bottom: 4rem;">Le Mie Iscrizioni
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <div v-for="(registration, index) in props.registrations" :key="index"
                    class="transform hover:scale-105 transition-transform duration-300">
                    <Card
                        class="shadow-lg hover:shadow-xl transition-shadow duration-300 border-0 rounded-xl overflow-hidden bg-white">
                        <template #header>
                            <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-6 flex justify-center">
                                <div class="bg-white p-3 rounded-lg shadow-md">
                                    <qrcode-vue :value="url" :size="150" />
                                </div>
                            </div>
                        </template>

                        <template #title>
                            <div class="px-6 pt-4">
                                <h3 class="text-xl font-bold text-gray-800 mb-2">{{ registration.name }}</h3>
                            </div>
                        </template>

                        <template #subtitle>
                            <div class="px-6">
                                <div class="flex items-center text-gray-600 mb-2">
                                    <i class="pi pi-map-marker text-blue-500 mr-2"></i>
                                    <span class="font-medium registration-text">{{ registration.race_place
                                        }}</span>
                                </div>
                                <div class="flex items-center text-gray-600 mb-2">
                                    <i class="pi pi-calendar text-green-500 mr-2"></i>
                                    <span class="registration-text">{{ formatDate(registration.race_date)
                                        }}</span>
                                </div>
                                <div class="flex items-center text-gray-600">
                                    <i class="pi pi-flag text-orange-500 mr-2"></i>
                                    <span class="font-semibold registration-text">{{ registration.km }} km</span>
                                </div>
                            </div>
                        </template>

                        <template #content>
                            <div class="px-6 pb-6">
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg">
                                        <span class="text-gray-600 font-medium">Taglia:</span>
                                        <span
                                            class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                                            {{ registration.size }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between items-center bg-green-50 p-3 rounded-lg">
                                        <span class="text-gray-600 font-medium">Totale:</span>
                                        <span class="text-green-600 font-bold text-lg">
                                            €{{ registration.total.toFixed(2) }}
                                        </span>
                                    </div>

                                    <div class="pt-2">
                                        <button
                                            class="pi pi-pencil w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-2 px-4 rounded-lg hover:from-blue-600 hover:to-purple-700 transition-colors duration-200 font-medium"
                                            style="margin-bottom:  4px;" @click="openModal(registration)">
                                        </button>

                                        <button
                                            class="pi pi-trash w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-2 px-4 rounded-lg hover:from-blue-600 hover:to-purple-700 transition-colors duration-200 font-medium"
                                            @click="deleteRace(registration.userRace_id)">
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Card>
                </div>
            </div>

            <div v-if="registrations.length === 0" class="text-center py-12">
                <i class="pi pi-inbox text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl text-gray-500 font-medium">Nessuna iscrizione trovata</h3>
                <p class="text-gray-400 mt-2">Le tue iscrizioni alle gare appariranno qui</p>
            </div>
        </div>
    </div>

    <!-- Dialog per modificare la registrazione -->
    <Dialog v-model:visible="showEditDialog" modal header="Modifica Iscrizione" class="w-full max-w-2xl">
        <form @submit.prevent="updateRace" class="space-y-6 p-4">
            <!-- Nome e Cognome -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <FloatLabel>
                        <InputText id="editFirstName" v-model="editForm.firstName" class="w-full" />
                        <label for="editFirstName">Nome</label>
                    </FloatLabel>
                </div>

                <div class="form-group">
                    <FloatLabel>
                        <InputText id="editLastName" v-model="editForm.lastName" class="w-full" />
                        <label for="editLastName">Cognome</label>
                    </FloatLabel>
                </div>
            </div>

            <!-- Km e Taglia -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <span class="p-float-label">
                        <Dropdown id="editDistance" v-model="editForm.km" :options="kmOptions" optionLabel="label"
                            optionValue="value" class="w-full" @change="calculateTotal" />
                        <label for="editDistance">Distanza (km)</label>
                    </span>
                </div>

                <div class="form-group">
                    <span class="p-float-label">
                        <Dropdown id="editSize" v-model="editForm.size" :options="sizeOptions" optionLabel="label"
                            optionValue="value" class="w-full" />
                        <label for="editSize">Taglia</label>
                    </span>
                </div>
            </div>

            <!-- Totale -->
            <div class="form-group">
                <Card class="bg-blue-50 shadow rounded">
                    <template #title>
                        Totale: €{{ editForm.total.toFixed(2) }}
                    </template>
                </Card>
            </div>

            <!-- Pulsanti -->
            <div class="flex justify-end gap-3" style="margin-top: 2rem;">
                <Button label="Annulla" severity="secondary" @click="closeEditDialog" />
                <Button type="submit" label="Salva Modifiche" severity="primary" />
            </div>
        </form>
    </Dialog>
</template>

<script setup lang="ts">
import Card from 'primevue/card';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';
import FloatLabel from 'primevue/floatlabel';
import Button from 'primevue/button';
import { ref, reactive } from 'vue';
import QrcodeVue from 'qrcode.vue';
import axios, { AxiosError } from 'axios';
import { getAccessToken } from '@/utils/auth';


interface Registration {
    userRace_id: number,
    race_id: number,
    race_place: string,
    race_date: Date,
    km: number,
    name: string,
    birthDate: Date,
    gender: string,
    size: string,
    total: number,
    paymentCode: string
}

const props = defineProps<{
    registrations: Registration[];
}>();

const emits = defineEmits(['reloadData', 'update:registrations']);

const url = ref("http://localhost:5173/profile");

const showEditDialog = ref(false);

// Opzioni per i dropdown
const kmOptions = [
    { label: '5 km', value: 5 },
    { label: '10 km', value: 10 },
    { label: '21 km', value: 21 }
];

const sizeOptions = [
    { label: 'S', value: 'S' },
    { label: 'M', value: 'M' },
    { label: 'L', value: 'L' },
    { label: 'XL', value: 'XL' }
];

const distancePrices: Record<number, number> = {
    5: 10,
    10: 15,
    21: 20
};

// Form per la modifica
const editForm = reactive({
    userRace_id: 0,
    firstName: '',
    lastName: '',
    km: 0,
    size: '',
    total: 0,
    COEFF: 1
});

const closeEditDialog = () => {
    showEditDialog.value = false;
    // Reset form
    Object.assign(editForm, {
        userRace_id: 0,
        firstName: '',
        lastName: '',
        km: 0,
        size: '',
        total: 0,
        COEFF: 1
    });
};

const formatDate = (date: Date | string) => {
    const d = new Date(date);
    return d.toLocaleDateString('it-IT', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const calculateTotal = () => {
    if (editForm.km && editForm.COEFF) {
        const price = distancePrices[editForm.km];
        editForm.total = editForm.COEFF * price;
    }
};

const deleteRace = async (registrationId: number) => {
    const token = await getAccessToken();

    axios.delete(import.meta.env.VITE_API_URL + 'userRace/' + registrationId, {
        headers: {
            Authorization: 'Bearer ' + token
        }
    }).then(response => {
        console.log(response.data);

        emits('reloadData');
        emits('update:registrations', props.registrations.filter(r => r.userRace_id !== registrationId));
    }).catch((e: AxiosError) => {
        console.error('Error deleting race: ', e);
    });
}


const openModal = (registration: Registration) => {
    const nameParts = registration.name.split(' ');
    editForm.userRace_id = registration.userRace_id;
    editForm.firstName = nameParts[0] || '';
    editForm.lastName = nameParts.slice(1).join(' ') || '';
    editForm.km = registration.km;
    editForm.size = registration.size;
    editForm.total = registration.total;
    editForm.COEFF = registration.COEFF || 1;

    showEditDialog.value = true;
}

const updateRace = async () => {
    const token = await getAccessToken();

    axios.put(import.meta.env.VITE_API_URL + 'userRace/' + editForm.userRace_id, {
        name: editForm.firstName + ' ' + editForm.lastName,
        size: editForm.size,
        total: editForm.total,
        km: editForm.km
    }, {
        headers: {
            Authorization: 'Bearer ' + token
        }
    }).then(response => {
        // console.log('Race updated: ', response.data);
        emits('reloadData');
        closeEditDialog();
    }).catch((e: AxiosError) => {
        console.error('Error updating race: ', e);
    });
};

</script>

<style scoped>
.pi {
    font-family: 'primeicons';
}

.registration-text {
    padding: 10px;
}
</style>