<template>
    <div class="devices-container">
        <h2>Your Devices</h2>
        
        <Listbox 
            v-model="selectedDevice" 
            :options="allDevices" 
            optionLabel="device" 
            optionValue="id"
            checkmark 
            :highlightOnSelect="true" 
            class="w-full md:w-56"
        >
            <template #option="slotProps">
                <div class="device-item">
                    
                    <div>{{ slotProps.option.device }}</div>
                    <div v-if="isCurrentDevice(slotProps.option)" class="current-device-badge">Current</div>
                    <i v-else class = "pi pi-trash trash-icon" style = "margin-left: 4rem;" @click = "deleteDevice(slotProps.option.id)"></i>
                </div>
            </template>
        </Listbox>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import Listbox from 'primevue/listbox';
import axios from "axios";
import { getAccessToken } from "@/utils/auth";



interface Device {
    id: number;
    device: string;
}

const props = defineProps<{
    currentDevice?: Device | null;
    devices: Device[];
}>();

const allDevices        = ref<Device[]>(props.devices);
const selectedDevice    = ref<Device | null>(null);
const emits             = defineEmits(['update:devices']);


onMounted(() => {
    if (props.currentDevice) {
        selectedDevice.value = props.currentDevice;
    }
});

const isCurrentDevice = (device: any) => {
    return props.currentDevice && props.currentDevice.id === device.id;
};

const deleteDevice = async (id : Number) => {
    // console.log("delete Device : " + id);

    const token = await getAccessToken();

    axios.delete(import.meta.env.VITE_API_URL + 'user/device/' + id, {
        headers: {
            Authorization: 'Bearer ' + token
        }
    }).then(response => {

        console.log(response.data);
        allDevices.value = allDevices.value.filter(device => device.id !== id);

    }).catch(error => {
        console.log(error);
    });
}

</script>

<style scoped>
.devices-container {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 20px;
    align-items: center;
}

.device-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
}

.current-device-badge {
    background-color: #10b981;
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: bold;
    margin-left : 4rem;
}

:deep(.p-listbox) {
    width: 100%;
}

:deep(.p-listbox-item) {
    padding: 8px 12px;
}

h2 {
    margin-bottom: 10px;
}
.trash-icon{
    color: red;
}
.trash-icon:hover{
    color: black;
}

</style>