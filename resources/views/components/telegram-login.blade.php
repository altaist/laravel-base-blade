<div class="telegram-login">
    <script 
        async 
        src="https://telegram.org/js/telegram-widget.js?22" 
        data-telegram-login="{{ config('telegram.bot.name') }}"
        data-size="large"
        data-radius="8"
        data-auth-url="{{ route('telegram.callback') }}"
        data-request-access="write">
    </script>
</div>

<style>
.telegram-login {
    display: flex;
    justify-content: center;
    margin: 1rem 0;
}
</style>
