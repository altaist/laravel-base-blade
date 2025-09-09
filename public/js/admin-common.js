/**
 * Общие JavaScript функции для админки
 */

/**
 * Сброс формы с подтверждением
 * @param {string} formId - ID формы для сброса
 */
function resetForm(formId) {
    if (confirm('Вы уверены, что хотите сбросить все изменения?')) {
        const form = document.getElementById(formId);
        if (form) {
            form.reset();
        }
    }
}

/**
 * Подтверждение удаления записи
 * @param {number} id - ID записи
 * @param {string} name - Название записи
 * @param {string} entityName - Тип сущности (по умолчанию 'запись')
 */
function confirmDelete(id, name, entityName = 'запись') {
    if (confirm(`Вы уверены, что хотите удалить ${entityName} "${name}"?\n\nЭто действие нельзя отменить.`)) {
        const form = document.getElementById('deleteForm');
        if (form) {
            form.action = form.action.replace(/\/\d+$/, `/${id}`);
            form.submit();
        }
    }
}

/**
 * Подтверждение удаления статьи
 * @param {HTMLElement} button - Кнопка удаления
 */
function confirmDeleteArticle(button) {
    const articleId = button.getAttribute('data-article-id');
    const articleTitle = button.getAttribute('data-article-title');
    
    if (confirm(`Вы уверены, что хотите удалить статью "${articleTitle}"?\n\nЭто действие нельзя отменить.`)) {
        const form = document.getElementById('deleteForm');
        if (form) {
            form.action = `/admin/articles/${articleId}`;
            form.submit();
        }
    }
}

/**
 * Валидация формы
 * @param {HTMLFormElement} form - Форма для валидации
 * @returns {boolean} - Результат валидации
 */
function validateForm(form) {
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(function(field) {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });

    if (!isValid) {
        alert('Пожалуйста, заполните все обязательные поля');
    }

    return isValid;
}

/**
 * Очистка ошибок валидации при вводе
 * @param {HTMLFormElement} form - Форма
 */
function clearValidationErrors(form) {
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(function(input) {
        input.addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
    });
}

/**
 * Обработка кликов по строкам таблицы
 */
function initClickableRows() {
    const clickableRows = document.querySelectorAll('.clickable-row');
    
    clickableRows.forEach(function(row) {
        row.addEventListener('click', function() {
            const href = this.getAttribute('data-href');
            if (href) {
                window.location.href = href;
            }
        });
    });
}

/**
 * Инициализация всех админских функций
 */
document.addEventListener('DOMContentLoaded', function() {
    // Инициализация валидации форм
    const forms = document.querySelectorAll('.admin-form');
    
    forms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            if (!validateForm(form)) {
                e.preventDefault();
            }
        });

        // Очистка ошибок при вводе
        clearValidationErrors(form);
    });

    // Инициализация кликабельных строк
    initClickableRows();
});

/**
 * Утилиты для работы с формами
 */
window.AdminUtils = {
    resetForm: resetForm,
    confirmDelete: confirmDelete,
    confirmDeleteArticle: confirmDeleteArticle,
    validateForm: validateForm,
    clearValidationErrors: clearValidationErrors,
    initClickableRows: initClickableRows
};
