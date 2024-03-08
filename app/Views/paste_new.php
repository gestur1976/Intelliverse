<!-- app/Views/paste_new.php -->
<div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>... or paste here an article to start from:</h2>
                <form action="/generate-from-news-article" method="post">
                    <label for="article-content">Article text</label>
                    <textarea class="form-control" name="article-content" rows="20"></textarea>
                    <input class="btn btn-primary" type="submit" value="Generate">
                </form>
            </div>
        </div>
    </div>
</div>