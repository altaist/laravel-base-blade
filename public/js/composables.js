/**
 * Composables - общие функции use для проекта
 */

/**
 * useAuth - composable для управления авторизацией
 */
const useAuth = () => {
    const COOKIE_NAME = 'auto_auth_token';
    const COOKIE_EXPIRES = 30; // дней

    /**
     * Утилиты для работы с куками
     */
    const cookieUtils = {
        set(name, value, days) {
            const expires = new Date();
            expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
            document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/;SameSite=Lax`;
        },
        
        get(name) {
            const nameEQ = name + "=";
            const ca = document.cookie.split(';');
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        },
        
        remove(name) {
            document.cookie = `${name}=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;`;
        }
    };

    /**
     * API запросы
     */
    const api = {
        async checkToken(token) {
            const response = await fetch('/api/auto-auth/check', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ token })
            });
            return response.json();
        },

        async confirmAuth(token) {
            const response = await fetch('/api/auto-auth/confirm', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ token })
            });
            return response.json();
        },

        async rejectAuth(token) {
            const response = await fetch('/api/auto-auth/reject', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ token })
            });
            return response.json();
        },

        async generateToken() {
            const response = await fetch('/api/auto-auth/generate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            return response.json();
        }
    };

    /**
     * Проверить автологин при загрузке страницы
     */
    const checkAutoAuth = async () => {
        const token = cookieUtils.get(COOKIE_NAME);
        
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
                cookieUtils.remove(COOKIE_NAME);
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
                // Удаляем токен из куков после успешной авторизации
                cookieUtils.remove(COOKIE_NAME);
                
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
            cookieUtils.remove(COOKIE_NAME);
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
                // Сохраняем токен в куки
                cookieUtils.set(COOKIE_NAME, result.token, COOKIE_EXPIRES);
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
     * Инициализация автологина
     */
    const initAutoAuth = async () => {
        // Двойная проверка: сначала meta тег, потом дополнительная проверка
        const authStatus = document.querySelector('meta[name="auth-status"]');
        if (authStatus && authStatus.content === 'authenticated') {
            console.log('Пользователь уже авторизован, автологин не нужен');
            return; // Пользователь уже авторизован
        }

        // Дополнительная проверка: если есть элементы, указывающие на авторизацию
        const authElements = document.querySelectorAll('[data-auth="true"], .user-menu, .logout-btn');
        if (authElements.length > 0) {
            console.log('Найдены элементы авторизованного пользователя, автологин не нужен');
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

    // Публичный API
    return {
        checkAutoAuth,
        confirmAutoAuth,
        rejectAutoAuth,
        generateAutoAuthToken,
        showConfirmPopup,
        initAutoAuth,
        cookieUtils
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
    await auth.initAutoAuth();
});

// Экспортируем для использования в других скриптах
window.useAuth = useAuth;

console.log('Composables загружены');