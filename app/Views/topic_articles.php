
<!-- app/Views/topic_articles.php -->
<div class="container">
    <h1>Interesting <?= esc($topic) ?> articles:</h1>
    <ul class="list-group">
        <?php for ($num = 1; $num <= 10; $num++): ?>
        <li class="list-group-item"><?php echo ucfirst($topic)?> article <?= esc($num) ?> </li>
        <?php endfor; ?>
    </ul>
</div>