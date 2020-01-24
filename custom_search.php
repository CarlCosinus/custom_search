<html>
<head>
    <title>Custom Search</title>
</head>
<body>
    <pre>
    <?php
    require_once __DIR__ . '/vendor/autoload.php';
    use League\Flysystem\Adapter\Local;
    use League\Flysystem\Filesystem;
    use Cache\Adapter\Filesystem\FilesystemCachePool;

    $filesystemAdapter = new Local('/tmp/custom_search_cache');
    $filesystem        = new Filesystem($filesystemAdapter);

    $cache = new FilesystemCachePool($filesystem);

    function getJson($url){
        global $cache,$search;
        if ($cache->hasItem($search)){
            $item = $cache->getItem($search);
            http_response_code(304);
            return $item->get();
        }
        else{
            $item = $cache->getItem($search);
            $json = file_get_contents($url);
            $json = json_decode($json);
            $json = $json->items;
            $json = json_encode($json,JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $item->set($json);
            $cache->save($item);
            http_response_code(200);
            return $item->get();
        }
    }

    if (!isset($_GET['search'])) {
        http_response_code(400);
       echo "please use ?search=";
    }
    else{
        $search = $_GET['search'];
        $url = 'https://www.googleapis.com/customsearch/v1?key=AIzaSyC2AduGUphE5-ksJwJaV_uJrSlTdAp8rBM&cx=006002295758494874133:7gt8xaawvjy&q='.$search;
        echo getJson($url);
    }

    ?>
        </pre>
</body>
</html>