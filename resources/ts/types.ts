export interface User {
    id: number;
    name: string;
    birth_date: string;
    phone: number;
    shipping_address: string;
    billing_address: string;
    document_id: string;
    referral_code: string | null;
    email: string;
    is_admin: boolean;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface LoginForm {
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
    logo?: string;
    country?: string;
    cars_count?: number;
}

interface CarSpecs {
    engine: string;
    hp: number;
    fuel: string;
}

export interface Car {
    id: number;
    name: string;
    brand: string;
    model: string;
    state: string;
    category: string;
    year: number;
    price: number;
    imageRoute: string;
    slug: string;
    discount: number;
    specs: CarSpecs;
    stock: number;
}

export interface PaginatedResponse<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

export interface CartItem {
    id: number;
    name: string;
    quantity: number;
    imageRoute: string;
    price: number;
    stock: number;
}

export interface Opinion {
    opinionId: string;
    idUser: number | null;
    user: string | null;
    timeStamp: string;
    rating: number;
    title: string;
    opinion: string;
}

export interface ProductOpinions {
    idProducte: number;
    date: string;
    totalOpinions: number;
    ratings: number[];
    opinions: Opinion[];
}
