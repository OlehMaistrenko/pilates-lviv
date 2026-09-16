<?php
/**
 * Порожній стан розділу кабінету. Перед include задай:
 *   $empty_text       — що саме порожнє й що з цим робити
 *   $empty_link_href  — опційна дія
 *   $empty_link_label — підпис до неї
 *
 * Генерично, а не в кожному розділі своє: порожньо може бути в чотирьох
 * із п'яти. Після виводу змінні гасимо — інакше наступний include на тій
 * самій сторінці підхопив би чуже посилання.
 */
?>
<p class="account-empty text--muted">
  <?= htmlspecialchars($empty_text ?? 'Тут поки порожньо.') ?>
  <?php if (!empty($empty_link_href)): ?>
    <a class="account-empty__link" href="<?= $empty_link_href ?>"><?= htmlspecialchars($empty_link_label ?? 'Перейти') ?></a>
  <?php endif; ?>
</p>
<?php $empty_text = $empty_link_href = $empty_link_label = null; ?>
