<!-- app/Views/paste_new.php -->
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>... or write an article URL and we'll process it to start browsing ...</h2>
                <form action="/generate-from-url" method="POST">
                    <label for="article-url">Article URL:</label>
                    <input type="text" id="article-url" name="article-url">
                    <input class="btn btn-primary" type="submit" value="Submit"">
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h2>... or paste here the article content.</h2>
                <form action="/generate-from-news-article" method="post">
                    <label for="article-content">Article text</label>
                    <textarea class="form-control" name="article-content" rows="20"></textarea>
                    <input class="btn btn-primary" type="submit" value="Generate">
                </form>
            </div>
        </div>
    </div>
