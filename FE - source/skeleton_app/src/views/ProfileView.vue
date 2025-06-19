<script setup lang="ts">
import axios, { AxiosError, type AxiosResponse } from 'axios';
import { jwtDecode } from 'jwt-decode';
import { getNewAccessToken, getAccessToken } from '../utils/auth';
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';


// primeVue imports
import Image from 'primevue/image';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import ProgressSpinner from 'primevue/progressspinner';
import FloatLabel from 'primevue/floatlabel';

// components
// import Roles from '@/components/Roles.vue';
import DevicesTable from '@/components/DevicesTable.vue';
import CardComponent from '@/components/CardComponent.vue';
// import AddRoleTable from '@/components/AddRoleTable.vue';


const router = useRouter();

const email = ref('');
const name = ref('');
const surname = ref('');
const update = ref(false);
const selectedFile = ref<File | null>(null);
const imageUrl = ref('');

const errors = ref<string[]>([]);
const roles = ref<string[]>([]);
const loading = ref(true);

const devices = ref([])
const currentDevice = ref({ id: 0, device: '' });

const registrations = ref<any[]>([]);


const isAdmin = computed(() => {
    return roles.value.includes('ROLE_ADMIN');
});

interface CustomJwtPayload {
    exp: number;
    data: {
        id: number;
        email: string;
        roles: string[];
    };
}


onMounted(async () => {
    const profile = await getProfile();
    if (profile) {
        email.value = profile.email;
        name.value = profile.name;
        surname.value = profile.surname;
        // currentDevice.value = profile.currentDevice;
        // devices.value = profile.devices;

        // Formatta il dispositivo corrente
        if (profile.currentDevice && profile.currentDevice.device) {
            currentDevice.value = {
                id: profile.currentDevice.id,
                device: formatDeviceInfo(profile.currentDevice.device)
            };
        }

        // Formatta tutti i dispositivi
        if (profile.devices && Array.isArray(profile.devices)) {
            devices.value = profile.devices.map((device: any) => {
                return {
                    ...device,
                    device: formatDeviceInfo(device.device)
                };
            });
        }

    }


    // const storedRegs = localStorage.getItem("registrations");
    // if (storedRegs) {
    //     const parsedRegs = JSON.parse(storedRegs);
    //     registrations.value = parsedRegs.map((reg: any) => ({
    //         ...reg,
    //         date: reg.date ? new Date(reg.date) : null
    //     }));
    // }

    console.log(devices.value);
    await downloadImage();
    loading.value = false;

});



// metodo per prendere i dati dell'utente attraverso l'access token, se è scaduto richiamo un altro metodo per rinnovarlo
const getProfile = async (): Promise<any> => {
    const token = await getAccessToken();

    const decodedAccessToken = jwtDecode<CustomJwtPayload>(token!);

    roles.value = decodedAccessToken.data.roles;
    const userId = decodedAccessToken.data.id;

    try {
        const response = await axios.get(import.meta.env.VITE_API_URL + 'user', {
            headers: {
                Authorization: 'Bearer ' + token
            }
        });

        console.log(response.data); // debug
        return response.data.data;
    } catch (error) {
        console.error(error);
        const err = error as AxiosError;
        if (err.response?.status === 401) {
            await getNewAccessToken();
            router.push('/');
        }
    }
}

const updateUserInfo = async () => {
    update.value = !update.value;
    const token = await getAccessToken();

    if (update.value) {
        return;
    }

    axios.put(import.meta.env.VITE_API_URL + 'user',
        {
            name: name.value,
            surname: surname.value
        },
        {
            headers: {
                Authorization: 'Bearer ' + token
            }
        }
    )
        .then((response: AxiosResponse) => {
            console.log(response.data); // debug
            return response.data.data;
        })
        .catch((e: AxiosError) => {
            errors.value.push(e.message);
            if (e.response?.status === 401) {
                getNewAccessToken();
                router.push('/');
            }
        });

}

// img methods
const onFileSelected = (event: any) => {
    console.log(event);
    selectedFile.value = event.target.files[0];
}

const onUpload = async () => {
    const token = await getAccessToken();

    const fd = new FormData();
    fd.append('image', selectedFile.value!, selectedFile.value!.name);

    console.log(fd);
    axios.post(import.meta.env.VITE_API_URL + 'photo', fd, {
        headers: {
            Authorization: 'Bearer ' + token
        }
    }).then(response => {
        console.log(response.data);

        if (response.data.message === 'error-userExists') {
            onUpdate();
        }
        downloadImage();
    }).catch(error => {
        console.error(error);
    });
}

const onUpdate = async () => {
    const token = await getAccessToken();

    try {
        await axios.delete(import.meta.env.VITE_API_URL + 'photo', {
            headers: {
                Authorization: 'Bearer ' + token
            }
        });

        const fd = new FormData();
        fd.append('image', selectedFile.value!, selectedFile.value!.name);

        const response = await axios.post(import.meta.env.VITE_API_URL + 'photo', fd, {
            headers: {
                Authorization: 'Bearer ' + token
            }
        });

        console.log(response.data);
        downloadImage();
    } catch (error) {
        console.error(error);
    }

}

const downloadImage = async () => {
    try {
        const token = await getAccessToken();
        console.log('Token:', token);

        const decodedAccessToken = jwtDecode<CustomJwtPayload>(token!);
        const userId = decodedAccessToken.data.id;
        console.log('User ID:', userId);

        const response = await axios.get(import.meta.env.VITE_API_URL + 'photo', {
            headers: {
                Authorization: 'Bearer ' + token
            },
            responseType: 'blob'
        });
        console.log('Response:', response);

        const url = URL.createObjectURL(response.data);
        imageUrl.value = url;
    } catch (error) {
        console.error('Error:', error);
    }
}

function formatDeviceInfo(userAgent: string): string {
    // Rimuovi l'indirizzo IP all'inizio
    const cleanUserAgent = userAgent.split(':').slice(1).join(':');

    let deviceInfo = '';
    let browserInfo = '';

    // Determina il dispositivo/sistema operativo
    if (cleanUserAgent.includes('iPhone')) {
        deviceInfo = 'iPhone';
    } else if (cleanUserAgent.includes('iPad')) {
        deviceInfo = 'iPad';
    } else if (cleanUserAgent.includes('Android')) {
        deviceInfo = 'Android';
    } else if (cleanUserAgent.includes('Windows')) {
        deviceInfo = 'Windows';
    } else if (cleanUserAgent.includes('Mac OS X') && !cleanUserAgent.includes('Mobile')) {
        deviceInfo = 'Mac';
    } else if (cleanUserAgent.includes('Linux')) {
        deviceInfo = 'Linux';
    } else if (cleanUserAgent.includes('PostmanRuntime')) {
        return 'Postman (API Client)';
    } else {
        deviceInfo = 'Unknown Device';
    }

    // Determina il browser
    if (cleanUserAgent.includes('Chrome') && !cleanUserAgent.includes('Edg')) {
        browserInfo = 'Chrome';
    } else if (cleanUserAgent.includes('Firefox')) {
        browserInfo = 'Firefox';
    } else if (cleanUserAgent.includes('Safari') && !cleanUserAgent.includes('Chrome')) {
        browserInfo = 'Safari';
    } else if (cleanUserAgent.includes('Edg')) {
        browserInfo = 'Edge';
    } else if (cleanUserAgent.includes('MSIE') || cleanUserAgent.includes('Trident')) {
        browserInfo = 'Internet Explorer';
    } else if (cleanUserAgent.includes('PostmanRuntime')) {
        browserInfo = '';
    } else {
        browserInfo = '';
    }

    return browserInfo ? `${deviceInfo} - ${browserInfo}` : deviceInfo;
}

defineExpose({ selectedFile, onFileSelected, loading });

</script>


<template>
    <div v-if="loading" class="card flex justify-center">
        <ProgressSpinner style="width: 70px; height: 70px" strokeWidth="3" fill="transparent" animationDuration="1s"
            aria-label="Custom ProgressSpinner" />
    </div>

    <div v-else>
        <main class="responsive-container">
            <div class="left-div">
                <div class="profile-container">
                    <h1>{{ email }}</h1>

                    <div class="profile-image-container flex justify-center items-center w-full">
                        <Image :src="imageUrl" alt="profile image" width="250" preview class="mx-auto" />
                    </div>

                    <br>

                    <div class="image-upload-container">
                        <label class="file-input-container">
                            <InputText type="file" @change='onFileSelected' />
                            Choose
                        </label>

                        <Button v-if="selectedFile" variant="text" raised rounded aria-label="Filter" @click="onUpload"
                            style="padding: .5rem;">
                            <span class="pi pi-upload" style="font-size: 1.5rem"></span>
                        </Button>
                    </div>
                </div>
            </div>

            <div class="right-div">
                <div class="info-container">
                    <DevicesTable :devices="devices" :currentDevice="currentDevice" />

                    <h2>Your Data</h2>
                    <div class="userInfo-container">
                        <FloatLabel variant="on">
                            <InputText id="user_name" v-model="name" :disabled="!update" />
                            <label for="user_name">Name</label>
                        </FloatLabel>

                        <FloatLabel variant="on">
                            <InputText id="user_surname" v-model="surname" :disabled="!update" />
                            <label for="user_name">Surname</label>
                        </FloatLabel>

                        <Button @click="updateUserInfo()">{{ update ? 'Save' : 'Edit' }}</Button>
                    </div>
                </div>
            </div>
        </main>

        <!-- <div v-if="isAdmin" class="w-full max-w-7xl mx-auto p-4">
            <div class="flex flex-col lg:flex-row gap-6">
                
                <div class="w-full lg:w-1/2">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                        <h2 class="text-xl font-bold mb-4">User Roles</h2>
                        <Roles />
                    </div>
                </div>
                
                
                <div class="w-full lg:w-1/2">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                        <h2 class="text-xl font-bold mb-4">Add Role</h2>
                        <AddRoleTable />
                    </div>
                </div>
            </div>
        </div> -->

        <!-- <CardComponent /> -->

    </div>
</template>

<style scoped>
main.responsive-container {
    display: flex;
    flex-direction: row;
    justify-content: center;
    gap: 40px;
    width: 100%;
    max-width: 1200px;
    margin: 3rem auto;
    padding: 0 1rem;
}

.left-div {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
    flex: 1;
}

.right-div {
    display: flex;
    flex-direction: row;
    align-items: center;
    flex: 1;
}

.profile-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 20px;
    width: 100%;
}

.info-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
}

.userInfo-container {
    display: flex;
    flex-direction: row;
    align-items: center;
    margin-top: 2rem;
    gap: 10px;
    flex-wrap: wrap;
    justify-content: center;
}

.image-upload-container {
    display: flex;
    align-items: center;
    gap: 10px;
}

:deep(.p-image img) {
    border-radius: 50%;
    object-fit: cover;
    aspect-ratio: 1/1;
    max-width: 100%;
    height: auto;
}

input {
    margin: 10px;
    padding: 5px;
}

button {
    margin: 10px;
    padding: 5px;
}

.file-input-container {
    border: 1px solid black;
    padding: 10px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.file-input-container:hover {
    background-color: #f0f0f0;
}

input[type="file"] {
    display: none;
}

.card.flex.justify-center {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.card {
    padding: 1rem;
    border-radius: 4px;
    margin-bottom: 1.5rem;
    width: 100%;
    max-width: 1200px;
    margin-left: auto;
    margin-right: auto;
}

.card-header {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
}

.card-header>.pt-part {
    flex: 1;
    text-align: center;
}

.divider-mobile {
    display: none;
}

/* Tablet styles */
@media (max-width: 992px) {
    .userInfo-container {
        flex-direction: column;
        width: 100%;
    }

    .userInfo-container .p-float-label {
        width: 100%;
    }
}

/* Mobile styles */
@media (max-width: 768px) {
    main.responsive-container {
        flex-direction: column;
        gap: 2rem;
    }

    .left-div,
    .right-div {
        width: 100%;
    }

    .profile-image-container {
        width: 80%;
    }

    .card-header {
        flex-direction: column;
    }

    .divider-desktop {
        display: none;
    }

    .divider-mobile {
        display: block;
        width: 100%;
    }

    h1 {
        font-size: 1.5rem;
    }

    .info-container {
        width: 100%;
    }
}

/* Small phone styles */
@media (max-width: 480px) {
    .profile-image-container {
        width: 100%;
    }

    .userInfo-container button {
        width: 100%;
    }

    .image-upload-container {
        flex-direction: column;
        width: 100%;
    }

    .file-input-container {
        width: 100%;
    }
}
</style>