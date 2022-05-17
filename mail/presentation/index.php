<div class="container">
    <h4>Предложения в прикрепленных файлах.</h4>
    <? if ($userMessage) : ?>
        <div class="user-message">
            <p><?= $userMessage ?></p>
        </div>
    <? endif; ?>
</div>