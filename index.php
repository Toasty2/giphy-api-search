<?php
require 'vendor/autoload.php';

use GuzzleHttp\Client;

define('API_KEY', 'CCPpLP13wgluwvmSaxqYFrtwVOXKhzxr');

$client = new Client([
    // Base URI is used with relative requests
    //'base_uri' => 'api.giphy.com/v1',
    // You can set any number of default request options.
    'timeout'  => 2.0,
    'query' => ['api_key' => API_KEY, 'rating' => 'g', 'limit' => '25']
]);


/*
 * Search for GIFs
 */
$gotSearchResults = false;
if (!empty($_POST['q'])) {
    // GIPHY probably has server-side input sanitisation but doesn't hurt to add it here too
    $userQuery = filter_var($_POST['q'], FILTER_SANITIZE_STRING);
    $searchResponse = $client->request('GET', '//api.giphy.com/v1/gifs/search', [
        'query' => ['q' => $userQuery, 'api_key' => API_KEY, 'rating' => 'g', 'limit' => '25']
    ]);

    $search = json_decode($searchResponse->getBody()->getContents());
    $gotSearchResults = true;
}

/*
 * Get trending GIFs
 */ 
$trendingResponse = $client->request('GET', '//api.giphy.com/v1/gifs/trending', []);
$trending = json_decode($trendingResponse->getBody()->getContents());

/*
 * Get categories
 */ 
$categoriesResponse = $client->request('GET', '//api.giphy.com/v1/gifs/categories', []);
$categories = json_decode($categoriesResponse->getBody()->getContents());
//var_dump($categories);
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <title>OnBrand GIPHY code assessment</title>
  </head>
  <body>
    <div class="container">
        <div class="row pt-3 pb-3">
            <div class="col-12">
                <h1>OnBrand Search GIPHY Assessment</h1>
                
                <h4>Popular categories</h4>
                <ul class="nav nav-pills">
                    <?php
                    foreach($categories->data AS $cat) { ?>
                        <li class="nav-item">
                            <a class="nav-link search-term" href="#" data-term="<?php echo $cat->name; ?>"><?php echo $cat->name; ?></a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>

        <div class="row pt-3 pb-3">
            <div class="col-12">
                <h4>Search GIPHY</h4>
                <form method="post">
                    <div class="mb-3">
                        <label for="q" class="form-label">Enter search term</label>
                        <input type="search" class="form-control" name="q" id="q">
                    </div>
                    <button type="submit" class="btn btn-primary" id="submit">Search</button>
                </form>
            </div>
        </div>

        <?php if ($gotSearchResults) { ?>
            <div class="row pt-3 pb-3">
                <div class="col-12">
                    <h3>
                        Showing results for '<?php echo $_POST['q']; ?>'
                    </h3>
                </div>
                <?php
                foreach($search->data AS $gif) { ?>
                    <div class="col-6 col-md-3">
                        <img src='<?php echo $gif->images->fixed_height->url; ?>'>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>

        <div class="row pt-3 pb-3">
            <div class="col-12">
                <h2>
                    Latest Trending GIFs
                </h2>
            </div>
                <?php
                foreach($trending->data AS $gif) { ?>
                    <div class="col-6 col-md-3">
                        <img src='<?php echo $gif->images->fixed_height->url; ?>'>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.slim.min.js" integrity="sha256-u7e5khyithlIdTpu22PHhENmPcRdFiHRjhAuHcs05RI=" crossorigin="anonymous"></script>
    <script>
    $(document).ready(function() {
        /*
         * Turn clicks on popular categories into searches
         */
        $('.search-term').on('click', function() {
            let term = $(this).data('term');
            $('#q').val(term);
            $('#submit').click();
        });
    });
    </script>
  </body>
</html>