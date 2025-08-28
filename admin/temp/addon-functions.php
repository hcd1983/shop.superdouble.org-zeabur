<?php
function key_search($array, $key, $value)
{
    $results = [];

    if (is_array($array)) {
        if (isset($array[$key]) && $array[$key] == $value) {
            $results[] = $array;
        }

        foreach ($array as $subarray) {
            $results = array_merge($results, key_search($subarray, $key, $value));
        }
    }
    return $results;
//    return count($results) ? true : false;
}

function useCache ($cache_file, $getDataFn) {
    if (file_exists($cache_file) && (filemtime($cache_file) > (time() - 60 * 60  ))) {
        // Cache file is less than five minutes old.
        // Don't bother refreshing, just use the file as-is.
        $file = file_get_contents($cache_file);
    } else {
        // Our cache is out-of-date, so load the data from our remote server,
        // and also save it over our cache for next time.
        if (is_callable( $getDataFn )) {
            $file = $getDataFn();
            file_put_contents($cache_file, $file, LOCK_EX);
        } else {
            $file = false;
        }
    }
    return $file;
}
