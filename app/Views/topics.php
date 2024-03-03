<!-- app/Views/topics.php -->
<div class="container">
    <div class="row my-4">
        <div class="col-md-12 ">
            <h1 class="display-4">Choose A Category!</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <ul>
                <?php foreach ($categories as $category) : ?>
                    <li class="my-2 lead">
                        <a href="/<?php echo $category["slug"] ?>">
                            <?= esc($category["title"]) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
