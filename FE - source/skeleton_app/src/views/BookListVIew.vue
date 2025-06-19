<template>
    <div class="card" style = "margin-top: 4rem;">
        <DataTable v-model:filters="filters" :value="books" paginator :rows="10" dataKey="id" filterDisplay="row" :loading="loading"
                :globalFilterFields="['title', 'autore']">

            <template #header>
                <div class="flex justify-end">
                    <IconField>
                        <InputIcon>
                            <i class="pi pi-search" />
                        </InputIcon>
                        <InputText v-model="filters['global'].value" placeholder="Keyword Search" />
                    </IconField>
                </div>
            </template>

            <template #empty> No books found. </template>
            <template #loading> Loading books data. Please wait. </template>

            <Column field="title" header="Title" style="min-width: 12rem">
                <template #body="{ data }">
                    {{ data.title }}
                </template>
                <template #filter="{ filterModel, filterCallback }">
                    <InputText v-model="filterModel.value" type="text" @input="filterCallback()" placeholder="Search by title" />
                </template>
            </Column>
            
            <Column field="autore" header="Author" style="min-width: 12rem">
                <template #body="{ data }">
                    {{ data.autore }}
                </template>
                <template #filter="{ filterModel, filterCallback }">
                    <InputText v-model="filterModel.value" type="text" @input="filterCallback()" placeholder="Search by author" />
                </template>
            </Column>

            <Column field="quantity" header="Quantity" style="max-width: 8rem; width: 8rem;">
                <template #body="{ data }">
                    {{ data.quantity }}
                </template>
                <!-- <template #filter="{ filterModel, filterCallback }">
                    <InputText v-model="filterModel.value" type="text" @input="filterCallback()" placeholder="Search by quantity" />
                </template> -->
            </Column>

            <Column field="daily_price" header="Daily Price" style="max-width: 8rem; width: 8rem;">
                <template #body="{ data }">
                    €{{ data.daily_price }}
                </template>
                <!-- <template #filter="{ filterModel, filterCallback }">
                    <InputText v-model="filterModel.value" type="text" @input="filterCallback()" placeholder="Search by price" />
                </template> -->
            </Column>

            <!-- <Column field="status" header="Status" style="min-width: 10rem">
                <template #body="{ data }">
                    <Tag :value="getStatusFromQuantity(data.quantity)" :severity="getSeverityFromQuantity(data.quantity)" />
                </template>
                <template #filter="{ filterModel, filterCallback }">
                    <Dropdown v-model="filterModel.value" @change="filterCallback()" :options="bookStatuses" placeholder="Any" showClear>
                        <template #value="slotProps">
                            <Tag v-if="slotProps.value" :value="slotProps.value" :severity="getSeverityFromQuantity(getQuantityForStatus(slotProps.value))" />
                            <span v-else>{{ slotProps.placeholder }}</span>
                        </template>
                        <template #option="slotProps">
                            <Tag :value="slotProps.option" :severity="getSeverityFromQuantity(getQuantityForStatus(slotProps.option))" />
                        </template>
                    </Dropdown>
                </template>
            </Column> -->

            <Column header="Actions" style="min-width: 3rem">
                <template #body="{ data }">
                    <Button
                        v-if = "!isBookInCart(data.id)"
                        icon="pi pi-shopping-cart" 
                        rounded 
                        outlined 
                        aria-label="Add to cart" 
                        @click="addToCart(data)" 
                        :class="{'p-button-success': isBookInCart(data.id)}"
                    />
                    <Button
                        v-else
                        icon = "pi pi-times"
                        rounded
                        outlined
                        aria-label = "Remove from cart"
                        @click = "removeFromCart(data)"
                        :class = "{'p-button-danger': isBookInCart(data.id)}"
                        />
                </template>
            </Column>
        </DataTable>

        <Button 
            label="Checkout" 
            icon="pi pi-shopping-cart" 
            class="p-button-success" 
            style="margin-top: 1rem"
            @click="visible = true"
        />

        <DialogComponent 
            :visible="visible" 
            @update:visible="visible = $event"
            :cartItems="cartItems"
            :books="books"
            @removeFromCart="handleRemoveFromCart"
            @confirmRental="handleConfirmRental"
        />
    </div>
</template>

<script setup lang = "ts">


// TODO -aprire una modal con una lista di tutti i libri aggiunti al carrello
// TODO - aggiungere un bottone per rimuovere un libro dal carrello
// TODO - mettere dei campi per inserire la data Da e A per il prestito
// TODO - inserire un campo con il prezzo finale e un bottone per confermare il prestito

// una volta confermato  fare la chiamata api per update sulla quatità


import { ref, onMounted } from 'vue';
import { FilterMatchMode } from '@primevue/core/api';

//prime imports

import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import Dropdown from 'primevue/dropdown';
import axios, { AxiosError } from 'axios';
import { getAccessToken } from '@/utils/auth';
import DialogComponent from '@/components/DialogComponent.vue';



const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    title: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    autore: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    quantity: { value: null, matchMode: FilterMatchMode.EQUALS },
    daily_price: { value: null, matchMode: FilterMatchMode.EQUALS },
    status: { value: null, matchMode: FilterMatchMode.EQUALS }
});

const books     = ref();
const loading   = ref(true);
const visible   = ref(false);

onMounted(async () => {
    await getAllBooks();
});

const getAllBooks = async () => {
    const token = await getAccessToken();

    axios.get(import.meta.env.VITE_API_URL + 'book/all', {
        headers: {
            Authorization: 'Bearer ' + token
        }
    }).then(response => {
        console.log(response.data);
        books.value = response.data.books;
        loading.value = false;
    }).catch((e: AxiosError) => {
        console.error('Error getting books:', e);
    });
};


const cartItems = ref<number[]>([]);

const addToCart = (book: any) => {
    if (!isBookInCart(book.id)) {

        if(book.quantity !== 0){
            cartItems.value.push(book.id);

            book.quantity -= 1;

            console.log('Book added to cart:', book);
            console.log('Current cart items:', cartItems.value);
        }
    }
};

const isBookInCart = (bookId: number): boolean => {
    return cartItems.value.includes(bookId);
};


const removeFromCart = (book: any) => {
    const index = cartItems.value.indexOf(book.id);
    if (index !== -1) {
        cartItems.value.splice(index, 1);
        book.quantity += 1;
        console.log('Book removed from cart:', book);
        console.log('Current cart items:', cartItems.value);
    }
};

// Add these new functions to handle events from the dialog
const handleRemoveFromCart = (bookId: number) => {
    const book = books.value.find((b: any) => b.id === bookId);
    if (book) {
        removeFromCart(book);
    }
};

const handleConfirmRental = async (rentalData: any) => {
    console.log('Confirming rental with data:', rentalData);
    
    const token = await getAccessToken();

    const bookUpdates = {
        books: cartItems.value.map(bookId => {
            const book = books.value.find((b: any) => b.id === bookId);
            return {
                id: bookId.toString(),
                quantity: book ? book.quantity.toString() : "0"  // Using current quantity or default to 1
            };
        })
    };


    axios.put(import.meta.env.VITE_API_URL + 'book', bookUpdates, {
        headers: {
            Authorization: 'Bearer ' + token
        }
       
    }).then(response => {
        console.log(response.data);

        cartItems.value = [];
        getAllBooks();

    }).catch((e: AxiosError) => {
        console.error('Error updating books:', e);
    });


    // try {
    //     const token = await getAccessToken();
        
    //     // Prepare the rental data for API
    //     const rentalPayload = {
    //         bookIds: cartItems.value,
    //         startDate: rentalData.startDate,
    //         endDate: rentalData.endDate,
    //         totalPrice: rentalData.totalPrice
    //     };
        
    //     // Call the API to create a rental
    //     const response = await axios.post(
    //         import.meta.env.VITE_API_URL + 'rental/create', 
    //         rentalPayload,
    //         {
    //             headers: {
    //                 Authorization: 'Bearer ' + token
    //             }
    //         }
    //     );
        
    //     console.log('Rental confirmed:', response.data);
        
    //     // Clear the cart after successful rental
    //     cartItems.value = [];
        
    //     // Refresh the book list to reflect updated quantities
    //     await getAllBooks();
        
    //     // Show success message (you can use a toast component here)
        
    // } catch (error) {
    //     console.error('Error confirming rental:', error);
    // }
};
</script>
