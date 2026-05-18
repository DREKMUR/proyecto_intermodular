import {defineStore} from "pinia";
import {ref, watch} from "vue";

import type {CartItem} from "@/types.ts";

export const useCartStore = defineStore('cart', () => {

    const items = ref<CartItem[]>(
        JSON.parse(localStorage.getItem('cart_items') || '[]')
    );

    const discountPercent = ref<number>(0);

    watch(items, (newItems) => {
        localStorage.setItem('cart_items', JSON.stringify(newItems));
    }, { deep: true });

    const addToCart = (product: Omit<CartItem, 'quantity'>) => {
        const existingItem = items.value.find(item => item.id === product.id);

        if (existingItem) {
            if(existingItem.stock < existingItem.quantity + 1)
                return true;
            existingItem.quantity++;
        } else {
            items.value.push({
                id: product.id,
                name: product.name,
                imageRoute: product.imageRoute,
                price: product.price,
                quantity: 1,
                stock: product.stock
            });
        }
    };

    const removeFromCart = (id: number | string) => {
        items.value = items.value.filter(item => item.id !== id);
    };

    const decrementQuantity = (id: number | string) => {
        const existingItem = items.value.find(item => item.id === id);
        if (existingItem) {
            if (existingItem.quantity > 1) {
                existingItem.quantity--;
            } else {
                removeFromCart(id);
            }
        }
    };

    const totalItems = () => items.value.reduce((total, item) => total + item.quantity, 0);
    const totalPrice = () => items.value.reduce((total, item) => total + (item.price * item.quantity), 0);

    const clearCart = () => {
        items.value = [];
    };

    const totalWithDiscount = () => {
        return totalPrice() * (1 - discountPercent.value / 100);
    };

    return {
        items,
        addToCart,
        removeFromCart,
        decrementQuantity,
        clearCart,
        totalItems,
        totalPrice,
        totalWithDiscount,
        discountPercent
    };
});
