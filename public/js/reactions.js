document.addEventListener('DOMContentLoaded', () => {
    // Обработчик для лайков
    document.addEventListener('click', async (e) => {
        if (e.target.closest('.like-btn')) {
            e.preventDefault();
            const button = e.target.closest('.like-btn');
            const itemId = button.dataset.itemId;
            
            try {
                const response = await fetch('/api/likes/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        likeable_type: 'article',
                        likeable_id: parseInt(itemId)
                    })
                });

                if (response.ok) {
                    const data = await response.json();
                    button.classList.toggle('active', data.is_liked);
                    
                    const counter = button.querySelector('.reaction-count');
                    if (counter) {
                        counter.textContent = data.likes_count;
                    }
                }
            } catch (error) {
                console.error('Like error:', error);
            }
        }
    });

    // Обработчик для избранного
    document.addEventListener('click', async (e) => {
        if (e.target.closest('.favorite-btn')) {
            e.preventDefault();
            const button = e.target.closest('.favorite-btn');
            const itemId = button.dataset.itemId;
            
            try {
                const response = await fetch('/api/favorites/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        favoritable_type: 'article',
                        favoritable_id: parseInt(itemId)
                    })
                });

                if (response.ok) {
                    const data = await response.json();
                    button.classList.toggle('active', data.is_favorited);
                    
                    const counter = button.querySelector('.reaction-count');
                    if (counter) {
                        counter.textContent = data.favorites_count;
                    }
                }
            } catch (error) {
                console.error('Favorite error:', error);
            }
        }
    });

    // Обработка галереи
    const gallery = document.querySelector('#pageGallery');
    if (gallery) {
        const indicators = document.querySelectorAll('.gallery-indicators .indicator');
        
        indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => {
                setActiveIndicator(index);
                bootstrap.Carousel.getInstance(gallery).to(index);
            });
        });

        gallery.addEventListener('slide.bs.carousel', (e) => {
            setActiveIndicator(e.to);
        });
    }

    function setActiveIndicator(index) {
        const indicators = document.querySelectorAll('.gallery-indicators .indicator');
        indicators.forEach((indicator, i) => {
            indicator.classList.toggle('active', i === index);
        });
    }
});

