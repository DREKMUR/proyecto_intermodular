export type User = {
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

