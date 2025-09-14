{{-- Футер с копирайтом, контактами и меню --}}
<footer class="footer">
    <div class="container">
        <div class="row">
            {{-- О компании --}}
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="footer-brand mb-3">
                    <a href="{{ route('home') }}" class="text-decoration-none d-flex align-items-center">
                        <i class="fas fa-cube me-2 text-primary"></i>
                        <h5 class="footer-title mb-0">Kvadro</h5>
                    </a>
                </div>
                <p class="footer-description">
                    Лучшие квадроциклы и аксессуары для ваших приключений. 
                    Качество, надежность и страсть к движению.
                </p>
                <div class="footer-social">
                    <a href="#" class="social-link me-3">
                        <i class="fab fa-telegram"></i>
                    </a>
                    <a href="#" class="social-link me-3">
                        <i class="fab fa-vk"></i>
                    </a>
                    <a href="#" class="social-link me-3">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="social-link">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>
            
            {{-- Меню --}}
            <div class="col-lg-2 col-md-6 mb-4">
                <h6 class="footer-heading">Навигация</h6>
                <ul class="footer-links">
                    <li><a href="{{ route('home') }}" class="footer-link">Главная</a></li>
                    <li><a href="{{ route('articles.index') }}" class="footer-link">Статьи</a></li>
                    <li><a href="#catalog" class="footer-link">Каталог</a></li>
                    <li><a href="#contact" class="footer-link">Контакты</a></li>
                </ul>
            </div>
            
            {{-- Услуги --}}
            <div class="col-lg-2 col-md-6 mb-4">
                <h6 class="footer-heading">Услуги</h6>
                <ul class="footer-links">
                    <li><a href="#rent" class="footer-link">Аренда</a></li>
                    <li><a href="#repair" class="footer-link">Ремонт</a></li>
                    <li><a href="#service" class="footer-link">Обслуживание</a></li>
                    <li><a href="#training" class="footer-link">Обучение</a></li>
                </ul>
            </div>
            
            {{-- Контакты --}}
            <div class="col-lg-4 col-md-6 mb-4">
                <h6 class="footer-heading">Контакты</h6>
                <div class="footer-contact">
                    <div class="contact-item mb-2">
                        <i class="fas fa-phone me-2 text-primary"></i>
                        <a href="tel:+7(999)123-45-67" class="footer-link">+7 (999) 123-45-67</a>
                    </div>
                    <div class="contact-item mb-2">
                        <i class="fas fa-envelope me-2 text-primary"></i>
                        <a href="mailto:info@kvadro.ru" class="footer-link">info@kvadro.ru</a>
                    </div>
                    <div class="contact-item mb-2">
                        <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                        <span class="footer-text">г. Москва, ул. Примерная, д. 123</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-clock me-2 text-primary"></i>
                        <span class="footer-text">Пн-Вс: 9:00 - 21:00</span>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Копирайт --}}
        <div class="footer-bottom">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="footer-copyright mb-0">
                        © {{ date('Y') }} Kvadro. Все права защищены.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="footer-legal">
                        <a href="#privacy" class="footer-link me-3">Политика конфиденциальности</a>
                        <a href="#terms" class="footer-link">Условия использования</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
/* Стили футера */
.footer {
    background: #2c3e50;
    color: #ecf0f1;
    padding: 3rem 0 1rem;
    margin-top: 3rem;
}

.footer-title {
    color: #3498db;
    font-weight: 700;
    font-size: 1.5rem;
}

.footer-heading {
    color: #3498db;
    font-weight: 600;
    margin-bottom: 1rem;
    font-size: 1.1rem;
}

.footer-description {
    color: #bdc3c7;
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.footer-links {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-links li {
    margin-bottom: 0.5rem;
}

.footer-link {
    color: #bdc3c7;
    text-decoration: none;
    transition: color 0.3s ease;
}

.footer-link:hover {
    color: #3498db;
    text-decoration: none;
}

.footer-social {
    margin-top: 1rem;
}

.social-link {
    display: inline-block;
    width: 40px;
    height: 40px;
    background: #34495e;
    color: #bdc3c7;
    border-radius: 50%;
    text-align: center;
    line-height: 40px;
    transition: all 0.3s ease;
    text-decoration: none;
}

.social-link:hover {
    background: #3498db;
    color: white;
    transform: translateY(-2px);
}

.footer-contact .contact-item {
    display: flex;
    align-items: center;
    margin-bottom: 0.75rem;
}

.footer-text {
    color: #bdc3c7;
}

.footer-bottom {
    border-top: 1px solid #34495e;
    padding-top: 1.5rem;
    margin-top: 2rem;
}

.footer-copyright {
    color: #95a5a6;
    font-size: 0.9rem;
}

.footer-legal {
    font-size: 0.9rem;
}

/* Адаптивность */
@media (max-width: 768px) {
    .footer {
        padding: 2rem 0 1rem;
        margin-top: 2rem;
    }
    
    .footer-bottom .text-md-end {
        text-align: left !important;
        margin-top: 1rem;
    }
    
    .footer-legal {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .footer-legal .footer-link {
        margin-right: 0 !important;
    }
}
</style>
