import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';
import type {User} from "@/types.ts";

export const useAuthStore = defineStore('auth', () => {
    const user = ref<User | null>(JSON.parse(localStorage.getItem('user') ?? 'null'));
    const token = ref<string | null>(localStorage.getItem('auth_token'));

    const isLoggedIn = computed(() => !!token.value);
    const isAdmin = computed(() => user.value?.is_admin === true);

    const login = async (credentials: Record<string, any>) => {
        const { data } = await axios.post('/api/login', credentials);

        token.value = data.access_token;
        user.value = data.user;

        localStorage.setItem('auth_token', data.access_token);
        localStorage.setItem('user', JSON.stringify(data.user));

        axios.defaults.headers.common['Authorization'] = `Bearer ${data.access_token}`;
    };

    const logout = async () => {
        try {
            await axios.post('/api/logout');
        } finally {
            token.value = null;
            user.value = null;
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user');
            delete axios.defaults.headers.common['Authorization'];
        }
    };

    return { user, token, isLoggedIn, isAdmin, login, logout };
});
