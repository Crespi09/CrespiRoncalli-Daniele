<template>
    <Dialog 
        v-model:visible="dialogVisible" 
        header="Your Cart" 
        :style="{ width: '50vw' }" 
        :modal="true"
        :closable="true"
    >
        <div v-if="cartBooks.length === 0" class="empty-cart-message">
            Your cart is empty. Please add books to proceed.
        </div>
        
        <div v-else>
            <h3>Selected Books</h3>
            <DataTable :value="cartBooks" stripedRows>
                <Column field="title" header="Title"></Column>
                <Column field="autore" header="Author"></Column>
                <Column field="daily_price" header="Price per Day">
                    <template #body="slotProps">
                        €{{ slotProps.data.daily_price }}
                    </template>
                </Column>
                <Column header="Actions">
                    <template #body="slotProps">
                        <Button 
                            icon="pi pi-trash" 
                            class="p-button-danger p-button-outlined p-button-sm"
                            @click="removeItem(slotProps.data.id)" 
                            tooltip="Remove from cart"
                        />
                    </template>
                </Column>
            </DataTable>

            <div class="rental-form p-fluid mt-4">
                <h3>Rental Period</h3>
                <div class="flex flex-wrap gap-3">
                    <div class="flex-1">
                        <label for="startDate" class="font-bold block mb-2">From</label>
                        <Calendar 
                            v-model="startDate" 
                            id="startDate" 
                            dateFormat="dd/mm/yy" 
                            placeholder="Select start date"
                            :minDate="today"
                            :showIcon="true"
                            @date-select="updateRentalDays"
                        />
                    </div>
                    <div class="flex-1">
                        <label for="endDate" class="font-bold block mb-2">To</label>
                        <Calendar 
                            v-model="endDate" 
                            id="endDate" 
                            dateFormat="dd/mm/yy" 
                            placeholder="Select end date"
                            :minDate="minEndDate"
                            :showIcon="true"
                            @date-select="updateRentalDays"
                        />
                    </div>
                </div>

                <div class="rental-summary mt-4 p-3 surface-ground border-round">
                    <div class="flex justify-content-between">
                        <span>Rental days:</span>
                        <span class="font-bold">{{ rentalDays }}</span>
                    </div>
                    <div class="flex justify-content-between mt-2">
                        <span>Total price:</span>
                        <span class="font-bold text-xl">€{{ totalPrice.toFixed(2) }}</span>
                    </div>
                </div>

                <div class="flex justify-content-end mt-4">
                    <Button 
                        label="Cancel" 
                        icon="pi pi-times" 
                        class="p-button-text mr-2" 
                        @click="closeDialog"
                    />
                    <Button 
                        label="Confirm Rental" 
                        icon="pi pi-check" 
                        class="p-button-success" 
                        :disabled="!isFormValid"
                        @click="confirmRental"
                    />
                </div>
            </div>
        </div>
    </Dialog>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue';
import Dialog from 'primevue/dialog';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import Calendar from 'primevue/calendar';

const props = defineProps({
    visible: {
        type: Boolean,
        required: true
    },
    cartItems: {
        type: Array as () => number[],
        required: true
    },
    books: {
        type: Array,
        required: true
    }
});

const emit = defineEmits(['update:visible', 'removeFromCart', 'confirmRental']);

// State management
const dialogVisible = computed({
    get: () => props.visible,
    set: (value) => emit('update:visible', value)
});

const cartBooks = computed(() => {
    if (!props.books) return [];
    return props.books.filter((book: any) => props.cartItems.includes(book.id));
});

// Date handling
const today = ref(new Date());
const startDate = ref<Date | null>(null);
const endDate = ref<Date | null>(null);
const rentalDays = ref(0);

const minEndDate = computed(() => {
    if (startDate.value) {
        const date = new Date(startDate.value);
        date.setDate(date.getDate() + 1);
        return date;
    }
    return today.value;
});

// Price calculation
const totalPrice = computed(() => {
    if (rentalDays.value <= 0 || cartBooks.value.length === 0) return 0;
    
    return cartBooks.value.reduce((sum: number, book: any) => {
        return sum + (book.daily_price * rentalDays.value);
    }, 0);
});

// Form validation
const isFormValid = computed(() => {
    return startDate.value && 
           endDate.value && 
           rentalDays.value > 0 && 
           cartBooks.value.length > 0;
});

// Methods
const updateRentalDays = () => {
    if (startDate.value && endDate.value) {
        const start = new Date(startDate.value);
        const end = new Date(endDate.value);
        const diffTime = end.getTime() - start.getTime();
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        rentalDays.value = diffDays + 1; // Including the start day
    } else {
        rentalDays.value = 0;
    }
};

const removeItem = (bookId: number) => {
    emit('removeFromCart', bookId);
};

const closeDialog = () => {
    dialogVisible.value = false;
};

const confirmRental = () => {
    if (!isFormValid.value) return;
    
    const rentalData = {
        startDate: startDate.value,
        endDate: endDate.value,
        rentalDays: rentalDays.value,
        totalPrice: totalPrice.value,
        books: cartBooks.value
    };
    
    emit('confirmRental', rentalData);
    closeDialog();
    
    // Reset form
    startDate.value = null;
    endDate.value = null;
    rentalDays.value = 0;
};

// Reset when dialog opens
watch(() => props.visible, (newValue) => {
    if (newValue) {
        startDate.value = null;
        endDate.value = null;
        rentalDays.value = 0;
    }
});

// Update rental days when dates change
watch([startDate, endDate], () => {
    updateRentalDays();
}, { deep: true });
</script>

<style scoped>
.empty-cart-message {
    padding: 2rem;
    text-align: center;
    color: var(--text-color-secondary);
    font-size: 1.2rem;
}

.rental-summary {
    background-color: var(--surface-ground);
}
</style>
