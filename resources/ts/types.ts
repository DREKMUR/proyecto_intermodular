export type User = {
    id: number;
    name: string;
    email: string;
    password: string;
    is_admin: boolean;
}

export interface LoginForm {
    email: string;
    password: string;
}

export interface RegisterForm {
    name: string;
    email: string;
    password: string;
}

export interface ApiError {
    response?: {
        data?: {
            message?: string;
        };
    };
}

export type DoubtsFormType = { id?: number, name: string, email: string, productRef: string, description: string };

export type Brand = {
    name: string;
    id: number;
}

export type State = {
    label: string;
    value: string;
}

export type Category = {
    label: string;
    value: string;
}

export interface Car {
    id: number;
    name: string;
    brand: string;
    state: string;
    category: string;
    year: number;
    price: number;
    imageRoute: string;
    slug: string;
    description?: string;
}

export interface PaginatedResponse<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}
