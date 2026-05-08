export function debounce<T extends (...args: any[]) => any>(fn: T, wait = 300) {
    let timer: ReturnType<typeof setTimeout> | null = null;

    const debounced = function (this: ThisParameterType<T>, ...args: Parameters<T>) {
        if (timer) clearTimeout(timer);
        timer = setTimeout(() => {
            timer = null;
            fn.apply(this, args);
        }, wait);
    };

    debounced.cancel = () => {
        if (timer) {
            clearTimeout(timer);
            timer = null;
        }
    };

    return debounced as T & { cancel: () => void };
}
