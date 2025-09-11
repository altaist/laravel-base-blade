export class PageReactions {
    constructor() {
        this.reactionButtons = document.querySelectorAll('.reaction-btn-modern');
        this.init();
    }

    init() {
        this.reactionButtons.forEach(button => {
            button.addEventListener('click', (e) => this.handleReaction(e));
        });
    }

    async handleReaction(e) {
        const button = e.currentTarget;
        const type = button.dataset.type;
        const id = button.dataset.id;
        
        try {
            const response = await fetch(`/api/reactions/${type}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ id })
            });

            if (!response.ok) throw new Error('Network response was not ok');

            const data = await response.json();
            
            // Обновляем состояние кнопки
            button.classList.toggle('active', data.status === 'added');
            
            // Обновляем счетчик
            const counter = button.querySelector('.reaction-count');
            if (counter) {
                counter.textContent = data.count;
            }
            
        } catch (error) {
            console.error('Error:', error);
            // Здесь можно добавить уведомление пользователю об ошибке
        }
    }
}
