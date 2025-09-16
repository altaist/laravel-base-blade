{{-- Компонент popup для подтверждения автологина --}}
<div id="auto-auth-popup" class="auto-auth-popup" style="display: none;">
    <div class="auto-auth-overlay"></div>
    <div class="auto-auth-modal">
        <div class="auto-auth-header">
            <h4>Автоматический вход</h4>
        </div>
        <div class="auto-auth-body">
            <div class="user-info">
                <div class="user-avatar">
                    <div class="avatar-placeholder" id="user-avatar-placeholder">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <div class="user-details">
                    <h5 id="user-name">Загрузка...</h5>
                    <p id="user-email">Загрузка...</p>
                </div>
            </div>
            <p class="confirm-text">Это ты?</p>
        </div>
        <div class="auto-auth-footer">
            <button class="btn btn-primary confirm-btn" id="confirm-auto-auth">
                <i class="fas fa-check"></i> Да, это я
            </button>
            <button class="btn btn-secondary reject-btn" id="reject-auto-auth">
                <i class="fas fa-times"></i> Нет
            </button>
        </div>
    </div>
</div>
