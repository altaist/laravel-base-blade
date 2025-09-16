/**
 * Composables - общие функции use для проекта
 */

/**
 * useAuth - composable для управления авторизацией
 */
const useAuth = () => {
    const STORAGE_NAME = 'auto_auth_token';
    const STORAGE_EXPIRES = 30; // дней

    /**
     * Проверка поддержки localStorage
     */
    const isLocalStorageSupported = () => {
        try {
            if (typeof(Storage) === "undefined") return false;
            const test = '__localStorage_test__';
            localStorage.setItem(test, test);
            localStorage.removeItem(test);
            return true;
        } catch (error) {
            return false;
        }
    };

    /**
     * Fallback на cookies если localStorage недоступен
     */
    const cookieFallback = {
        set(name, value, days) {
            try {
                const expires = new Date();
                expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
                document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/;SameSite=Lax`;
                return true;
            } catch (error) {
                console.error('Ошибка сохранения в cookies:', error);
                return false;
            }
        },
        
        get(name) {
            try {
                const nameEQ = name + "=";
                const ca = document.cookie.split(';');
                for (let i = 0; i < ca.length; i++) {
                    let c = ca[i];
                    while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                    if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
                }
                return null;
            } catch (error) {
                console.error('Ошибка чтения из cookies:', error);
                return null;
            }
        },
        
        remove(name) {
            try {
                document.cookie = `${name}=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;`;
                return true;
            } catch (error) {
                console.error('Ошибка удаления из cookies:', error);
                return false;
            }
        }
    };

    /**
     * Универсальные утилиты для работы с хранилищем
     */
    const storageUtils = {
        set(name, value, days) {
            if (isLocalStorageSupported()) {
                try {
                    const data = {
                        value: value,
                        expires: new Date().getTime() + (days * 24 * 60 * 60 * 1000)
                    };
                    localStorage.setItem(name, JSON.stringify(data));
                    return true;
                } catch (error) {
                    console.warn('localStorage недоступен, используем cookies:', error);
                    return cookieFallback.set(name, value, days);
                }
            } else {
                console.warn('localStorage не поддерживается, используем cookies');
                return cookieFallback.set(name, value, days);
            }
        },
        
        get(name) {
            if (isLocalStorageSupported()) {
                try {
                    const item = localStorage.getItem(name);
                    if (!item) return null;
                    
                    const data = JSON.parse(item);
                    if (!data || typeof data !== 'object' || !data.value || !data.expires) {
                        console.warn('Поврежденные данные в localStorage, очищаем');
                        localStorage.removeItem(name);
                        return null;
                    }
                    
                    if (new Date().getTime() > data.expires) {
                        const removed = localStorage.removeItem(name);
                        if (!removed) {
                            console.warn('Не удалось удалить истекший токен из localStorage');
                        }
                        return null;
                    }
                    return data.value;
                } catch (error) {
                    console.warn('Ошибка чтения из localStorage, пробуем cookies:', error);
                    return cookieFallback.get(name);
                }
            } else {
                return cookieFallback.get(name);
            }
        },
        
        remove(name) {
            let success = false;
            
            if (isLocalStorageSupported()) {
                try {
                    success = localStorage.removeItem(name) !== undefined;
                } catch (error) {
                    console.warn('Ошибка удаления из localStorage:', error);
                }
            }
            
            // Всегда пробуем удалить из cookies как fallback
            const cookieSuccess = cookieFallback.remove(name);
            
            return success || cookieSuccess;
        }
    };

    /**
     * Обработка ошибок API
     */
    const handleApiError = (error, url) => {
        if (error.message.includes('Failed to fetch')) {
            return {
                success: false,
                error: 'Проблемы с подключением к серверу. Проверьте интернет-соединение.',
                type: 'network'
            };
        }
        
        if (error.message.includes('HTTP 429')) {
            return {
                success: false,
                error: 'Слишком много запросов. Попробуйте позже.',
                type: 'rate_limit'
            };
        }
        
        if (error.message.includes('HTTP 500')) {
            return {
                success: false,
                error: 'Внутренняя ошибка сервера. Попробуйте позже.',
                type: 'server_error'
            };
        }
        
        console.error(`API запрос к ${url} failed:`, error);
        return {
            success: false,
            error: 'Произошла ошибка при авторизации',
            type: 'unknown'
        };
    };

    /**
     * Универсальный метод для API запросов с улучшенной обработкой ошибок
     */
    const makeApiRequest = async (url, options = {}) => {
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    ...options.headers
                },
                ...options
            });

            if (!response.ok) {
                const error = new Error(`HTTP ${response.status}: ${response.statusText}`);
                error.status = response.status;
                throw error;
            }

            const data = await response.json();
            return data;
        } catch (error) {
            return handleApiError(error, url);
        }
    };

    /**
     * API запросы с улучшенной обработкой ошибок
     */
    const api = {
        async checkToken(token) {
            return await makeApiRequest('/api/auto-auth/check', {
                body: JSON.stringify({ token })
            });
        },

        async confirmAuth(token) {
            return await makeApiRequest('/api/auto-auth/confirm', {
                body: JSON.stringify({ token })
            });
        },

        async rejectAuth(token) {
            return await makeApiRequest('/api/auto-auth/reject', {
                body: JSON.stringify({ token })
            });
        },

        async generateToken() {
            return await makeApiRequest('/api/auto-auth/generate');
        }
    };

    /**
     * Проверить автологин при загрузке страницы
     */
    const checkAutoAuth = async () => {
        const token = storageUtils.get(STORAGE_NAME);
        
        if (!token) {
            return { hasToken: false };
        }

        try {
            const result = await api.checkToken(token);
            
            if (result.success) {
                return {
                    hasToken: true,
                    isValid: true,
                    user: result.user,
                    token: token
                };
            } else {
                // Токен недействителен - удаляем
                storageUtils.remove(STORAGE_NAME);
                return {
                    hasToken: true,
                    isValid: false
                };
            }
        } catch (error) {
            console.error('Ошибка проверки токена автологина:', error);
            return {
                hasToken: true,
                isValid: false,
                error: error.message
            };
        }
    };

    /**
     * Подтвердить автологин
     */
    const confirmAutoAuth = async (token) => {
        try {
            const result = await api.confirmAuth(token);
            
            if (result.success) {
                // Удаляем токен из localStorage после успешной авторизации
                storageUtils.remove(STORAGE_NAME);
                
                // Перезагружаем страницу для обновления состояния
                window.location.reload();
                
                return { success: true, user: result.user };
            } else {
                return { success: false, error: result.message };
            }
        } catch (error) {
            console.error('Ошибка подтверждения автологина:', error);
            return { success: false, error: error.message };
        }
    };

    /**
     * Отклонить автологин
     */
    const rejectAutoAuth = async (token) => {
        try {
            await api.rejectAuth(token);
            storageUtils.remove(STORAGE_NAME);
            return { success: true };
        } catch (error) {
            console.error('Ошибка отклонения автологина:', error);
            return { success: false, error: error.message };
        }
    };

    /**
     * Генерировать токен автологина
     */
    const generateAutoAuthToken = async () => {
        try {
            const result = await api.generateToken();
            
            if (result.success) {
                // Сохраняем токен в localStorage
                const saved = storageUtils.set(STORAGE_NAME, result.token, STORAGE_EXPIRES);
                if (!saved) {
                    console.warn('Не удалось сохранить токен в localStorage');
                }
                return { success: true, token: result.token };
            } else {
                return { success: false, error: result.message };
            }
        } catch (error) {
            console.error('Ошибка генерации токена автологина:', error);
            return { success: false, error: error.message };
        }
    };

    /**
     * Показать popup подтверждения
     */
    const showConfirmPopup = (user) => {
        return new Promise((resolve) => {
            // Получаем существующий popup из DOM (созданный Blade компонентом)
            const popup = document.getElementById('auto-auth-popup');
            if (!popup) {
                console.error('Popup не найден в DOM. Убедитесь, что компонент <x-auto-auth-popup /> подключен.');
                resolve({ confirmed: false });
                return;
            }

            // Заполняем данные пользователя
            const nameElement = document.getElementById('user-name');
            const emailElement = document.getElementById('user-email');
            const avatarElement = document.getElementById('user-avatar-placeholder');

            if (nameElement) nameElement.textContent = user.name || 'Пользователь';
            if (emailElement) emailElement.textContent = user.email || '';
            if (avatarElement && user.name) {
                avatarElement.textContent = user.name.charAt(0).toUpperCase();
            }

            // Показываем popup
            popup.style.display = 'flex';

            // Обработчики событий
            const confirmBtn = document.getElementById('confirm-auto-auth');
            const rejectBtn = document.getElementById('reject-auto-auth');
            const overlay = popup.querySelector('.auto-auth-overlay');

            const cleanup = () => {
                popup.style.display = 'none';
            };

            confirmBtn.addEventListener('click', () => {
                cleanup();
                resolve({ confirmed: true });
            });

            rejectBtn.addEventListener('click', () => {
                cleanup();
                resolve({ confirmed: false });
            });

            overlay.addEventListener('click', () => {
                cleanup();
                resolve({ confirmed: false });
            });
        });
    };

    /**
     * Проверка авторизации пользователя
     */
    const isUserAuthenticated = () => {
        const authStatus = document.querySelector('meta[name="auth-status"]');
        return authStatus && authStatus.content === 'authenticated';
    };

    /**
     * Инициализация автологина
     */
    const initAutoAuth = async () => {
        // Проверяем, включена ли фича автологина
        const autoAuthEnabled = document.querySelector('meta[name="auto-auth-enabled"]');
        if (!autoAuthEnabled || autoAuthEnabled.content !== 'true') {
            console.log('Автологин отключен в настройках');
            return;
        }

        // Оптимизированная проверка авторизации
        if (isUserAuthenticated()) {
            console.log('Пользователь уже авторизован, автологин не нужен');
            return;
        }

        console.log('Пользователь не авторизован, проверяем токен автологина...');

        // Проверяем токен автологина
        const authResult = await checkAutoAuth();
        
        if (authResult.hasToken && authResult.isValid) {
            console.log('Найден валидный токен автологина, показываем popup');
            // Показываем popup для подтверждения
            const popupResult = await showConfirmPopup(authResult.user);
            
            if (popupResult.confirmed) {
                console.log('Пользователь подтвердил автологин');
                await confirmAutoAuth(authResult.token);
            } else {
                console.log('Пользователь отклонил автологин');
                await rejectAutoAuth(authResult.token);
            }
        } else {
            console.log('Токен автологина не найден или недействителен');
        }
    };

    /**
     * Очистить токен автологина (для выхода из системы)
     */
    const clearAutoAuthToken = () => {
        const removed = storageUtils.remove(STORAGE_NAME);
        if (removed) {
            console.log('Токен автологина очищен');
        } else {
            console.warn('Не удалось очистить токен автологина');
        }
        return removed;
    };

    /**
     * Синхронизация между вкладками
     */
    const initStorageSync = () => {
        window.addEventListener('storage', (e) => {
            if (e.key === STORAGE_NAME) {
                console.log('Токен автологина изменен в другой вкладке');
                // Можно добавить логику для обновления UI
            }
        });
    };

    /**
     * Обработать токен из сессии (после успешной авторизации)
     */
    const handleSessionToken = () => {
        const tokenElement = document.getElementById('auto-auth-token-data');
        if (tokenElement) {
            const token = tokenElement.getAttribute('data-token');
            if (token && token !== '') {
                console.log('Обрабатываем токен из сессии...');
                
                // Сохраняем токен в localStorage
                const saved = storageUtils.set(STORAGE_NAME, token, STORAGE_EXPIRES);
                if (saved) {
                    console.log('Токен автологина сохранен в localStorage');
                } else {
                    console.warn('Не удалось сохранить токен в localStorage');
                }
                
                // Удаляем элемент после обработки
                tokenElement.remove();
            }
        }
    };

    // Публичный API
    return {
        checkAutoAuth,
        confirmAutoAuth,
        rejectAutoAuth,
        generateAutoAuthToken,
        showConfirmPopup,
        initAutoAuth,
        clearAutoAuthToken,
        initStorageSync,
        handleSessionToken,
        storageUtils
    };
};

// Инициализируем при загрузке страницы только для неавторизованных пользователей
document.addEventListener('DOMContentLoaded', async () => {
    // Проверяем, что мы на странице для неавторизованных пользователей
    const authStatus = document.querySelector('meta[name="auth-status"]');
    if (authStatus && authStatus.content === 'authenticated') {
        console.log('Скрипт автологина не загружен - пользователь уже авторизован');
        return;
    }

    console.log('Загружаем скрипт автологина...');
    const auth = useAuth();
    
    // Инициализируем синхронизацию между вкладками
    auth.initStorageSync();
    
    // Обрабатываем токен из сессии (если есть)
    auth.handleSessionToken();
    
    await auth.initAutoAuth();
});

// Экспортируем для использования в других скриптах
window.useAuth = useAuth;

console.log('Composables загружены');