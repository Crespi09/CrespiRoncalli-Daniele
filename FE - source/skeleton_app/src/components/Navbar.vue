<template>
    <Toast />
    
    <div class="card">

        <Menubar >
            <template v-slot:start>
                <RouterLink to="/">
                    <!-- <i class = "pi pi-slack" style = "font-size: 2rem;"></i> -->
                    <i class = "pi pi-check-square" style = "font-size: 2rem;"></i>
                </RouterLink>
            </template>
            
            <template #item></template>
            
            <template v-slot:end>
                <div class="flex items-center gap-2">

                    <RouterLink to="/login" v-if="!currentUser">
                        <Button label="Login" severity="info" variant="text" />
                    </RouterLink>

                    <div v-else>
                        
                        <div class="container-choices">
                            <RouterLink to="/race">

                                <OverlayBadge :value = "todoCount">
                                    <Button label="Book a Race" severity="info" variant="text"/>
                                </OverlayBadge>

                            </RouterLink>
                            
                            <div @click="toggle" class="container-profile p-2">
                                <Avatar :image="imageUrl" shape="circle" class="profile-img"/> 
                                <i class="pi pi-angle-down"></i>                               
                            </div>
                        </div>


                        <!-- <TieredMenu ref="menu" id="overlay_tmenu" :model="items" popup /> -->
                        
                        <TieredMenu ref="menu" :model="items" popup>
                            <template #item="{ item, props }">
                                <a v-bind="props.action" :class="item.label === 'Logout' ? 'highlight-logout' : ''">
                                    <i :class="item.icon"></i>
                                    <span>{{ item.label }}</span>
                                </a>
                            </template>
                        </TieredMenu>


                    </div>

                    </div>
            </template>
        </Menubar>
    </div>
</template>

<script setup lang="ts" >


import { BehaviorSubject } from 'rxjs';
import { computed, inject, onMounted, ref, watch  } from 'vue';
import { useSubject } from '@vueuse/rxjs';
import { RouterLink , useRouter } from 'vue-router'


// primevue imports
import Menubar from 'primevue/menubar';
import Avatar from 'primevue/avatar';
import Button from 'primevue/button';
import { jwtDecode } from 'jwt-decode';
import axios, { AxiosError } from 'axios';
import TieredMenu from 'primevue/tieredmenu';
import Badge from 'primevue/badge';
import OverlayBadge from 'primevue/overlaybadge';
import { getNewAccessToken, getAccessToken, logOutUser } from '@/utils/auth';
import Toast from 'primevue/toast';

import { useToast } from "primevue/usetoast";

const toast = useToast();

interface CustomJwtPayload {
    exp: number;
    data: {
        id: number;
        email: string;
        roles: string[];
    };
}

const router                =   useRouter();

const currentUser$          =   inject<BehaviorSubject<any>>('currentUser$')!;
const currentUser           =   useSubject(currentUser$);
const imageUrl              =   ref('');
const todoCount             =   ref(0);

const darkModeActivation    =   ref(false);
const labelMode             =   ref('Dark Mode');
const iconMode              =   ref('pi pi-moon');

currentUser$.subscribe(user => {
    currentUser.value = user;
});


const menu = ref();
const items = computed(() => [
    {
        label       : 'Profile',
        icon        : 'pi pi-user',
        command     : () => router.push('/profile')
    },
    {
        label       : labelMode.value,
        icon        : iconMode.value,
        command     : () => toggleDarkMode()
    },
    {
        separator   : true
    },
    {
        label       : 'Logout',
        icon        : 'pi pi-sign-out',
        command     : () => logout()
    },
]);


interface Registration {
    place: string,
    date: Date,
    km: number,
    firstName: string,
    lastName: string,
    birthDate: Date,
    gender: string,
    size: string,
    total : number,
    paymentCode : string
}


const registrations = ref<Registration[]>([]);

const storedRegs = localStorage.getItem("registrations");
if (storedRegs) {
    const parsedRegs = JSON.parse(storedRegs);
    registrations.value = parsedRegs.map((reg: any) => ({
        ...reg,
        date: reg.date ? new Date(reg.date) : null
    }));
}

function toggleDarkMode() {
    const isDark = document.documentElement.classList.contains('my-app-dark');
    
    // Cambia lo stato
    if (isDark) {
        document.documentElement.classList.remove('my-app-dark');
        localStorage.setItem('colorScheme', 'light');
        darkModeActivation.value = false;
    } else {
        document.documentElement.classList.add('my-app-dark');
        localStorage.setItem('colorScheme', 'dark');
        darkModeActivation.value = true;
    }

    if(darkModeActivation.value){
        iconMode.value  = 'pi pi-sun';
        labelMode.value = 'Light Mode';
    }else{
        console.log("test");
        iconMode.value  = 'pi pi-moon';
        labelMode.value = 'Dark Mode';
    }

}

const logout = () => {
    logOutUser();
    setToast('success', 'Logout', 'Successfull Logout')
    currentUser$.next(null);
    router.push('/');
};

onMounted(async () => {
    if(currentUser.value) {
        await downloadImage();


        await getAllRaces();

        // await getAllTodo();
    }
});

watch(
  () => currentUser.value,
  (newVal) => {
    if (newVal) {
        downloadImage();
    }
  }
);

const getAllRaces = async () => {
    const token = await getAccessToken();

    axios.get(import.meta.env.VITE_API_URL + 'userRace', {
        headers: {
            Authorization: 'Bearer ' + token
        }
    }).then(response => {
        console.log(response.data);
        // books.value = response.data.books;
        // loading.value = false;
        registrations.value = response.data.races;

        todoCount.value = registrations.value.length;

    }).catch((e: AxiosError) => {
        console.error('Error getting races: ', e);
    });
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


const toggle = (event : any) => {
    menu.value.toggle(event);
};


const setToast = (type: any ,title: string, description: string) => {
   toast.add({ severity: type, summary: title, detail: description, life: 1000 });
}

</script>

<style scoped>
a {
    text-decoration: none;
    color: inherit;
    background-color: transparent;
}

.container-profile{
    display: inline-block;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
}

.container-profile:hover{
    background-color: #f0f0f0;
    border-radius: 0.5rem;
    transition : background-color 0.5s;
}

.profile-img{
    vertical-align: middle;
    margin-right: .5rem;
}

.highlight-logout {
    color: #ff4d4d !important;
    font-weight: bold !important;
}
.highlight-logout:hover {
    color: #fb1111 !important;
}

.container-choices{
    display: flex;
    gap: 1rem;
    align-items: center;
}

</style>
