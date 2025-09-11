/**
 * Articles JavaScript functionality
 * Handles AJAX likes and favorites without page reload
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize article interactions
    initArticleInteractions();
});

function initArticleInteractions() {
    // Handle like button clicks
    document.querySelectorAll('.like-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            handleReaction(this, 'like');
        });
    });

    // Handle favorite button clicks
    document.querySelectorAll('.favorite-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            handleReaction(this, 'favorite');
        });
    });

    // SVG изображения всегда доступны, обработка ошибок не нужна
}

function handleReaction(button, type) {
    // Check if user is authenticated
    if (!isUserAuthenticated()) {
        showAuthRequired();
        return;
    }

    const articleId = button.dataset.articleId;
    const isActive = button.classList.contains('active');
    
    // Show loading state
    setButtonLoading(button, true);
    
    // Prepare request data
    const requestData = {
        [type === 'like' ? 'likeable_type' : 'favoritable_type']: 'App\\Models\\Article',
        [type === 'like' ? 'likeable_id' : 'favoritable_id']: articleId,
        _token: getCsrfToken()
    };

    // Determine API endpoint
    const endpoint = type === 'like' ? '/api/likes/toggle' : '/api/favorites/toggle';
    
    // Make AJAX request
    fetch(endpoint, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json'
        },
        body: JSON.stringify(requestData)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        // Update button state
        updateButtonState(button, data.is_liked || data.is_favorited);
        
        // Update counter
        const countElement = button.querySelector('.reaction-count');
        if (countElement) {
            const newCount = type === 'like' ? data.likes_count : data.favorites_count;
            countElement.textContent = newCount;
        }
        
        // Show success message
        showSuccessMessage(data.message);
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorMessage('Произошла ошибка. Попробуйте еще раз.');
    })
    .finally(() => {
        // Remove loading state
        setButtonLoading(button, false);
    });
}

function updateButtonState(button, isActive) {
    if (isActive) {
        button.classList.add('active');
    } else {
        button.classList.remove('active');
    }
}

function setButtonLoading(button, isLoading) {
    if (isLoading) {
        button.classList.add('loading');
        button.disabled = true;
    } else {
        button.classList.remove('loading');
        button.disabled = false;
    }
}

function isUserAuthenticated() {
    // Check if user is authenticated by looking for auth indicators
    // This could be a meta tag, data attribute, or other indicator
    const authMeta = document.querySelector('meta[name="auth-status"]');
    return authMeta && authMeta.content === 'authenticated';
}

function getCsrfToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    return token ? token.content : '';
}

function showAuthRequired() {
    // Show modal or redirect to login
    if (confirm('Для выполнения этого действия необходимо войти в систему. Перейти на страницу входа?')) {
        window.location.href = '/login';
    }
}

function showSuccessMessage(message) {
    // Simple success notification
    const notification = document.createElement('div');
    notification.className = 'alert alert-success alert-dismissible fade show position-fixed';
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 3000);
}

function showErrorMessage(message) {
    // Simple error notification
    const notification = document.createElement('div');
    notification.className = 'alert alert-danger alert-dismissible fade show position-fixed';
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Utility function to refresh article data (if needed)
function refreshArticleData(articleId) {
    // This could be used to refresh article data from server
    // For now, we'll just log it
    console.log(`Refreshing data for article ${articleId}`);
}
