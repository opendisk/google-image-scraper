<?php

namespace Opendisk\WebScraper;

class GoogleImage
{
	public static function get($keyword, $proxy, $options = [])
	{
		$opts = '';
		
		foreach ($options as $key => $value) {
			$opts .= "&$key=$value";
		}

		$url = "https://www.google.com/search?q=" . urlencode($keyword) . $opts . "&tbm=isch&hl=en-US&sa=X&biw=500&bih=500";

		$uags = [
			"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.82 Safari/537.36",
			"Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.82 Safari/537.36",
			"Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.82 Safari/537.36",
			"Mozilla/5.0 (Macintosh; Intel Mac OS X 12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.82 Safari/537.36"
		];

		$uag = $uags[array_rand($uags)];
        if (!empty($proxy)) {
            $proxy = "tcp://$proxy";
        }

        $params = [
            "http" => [
                "method" => "GET",
                "proxy" => "$proxy",
                "user_agent" => $uag,
            ],
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ],
        ];

        $context = stream_context_create($params);

        $response = file_get_contents($url, false, $context);

        $res = '/AF_initDataCallback\({key: \'ds:1\', hash: \'\d\', data:(.*), sideChannel: {}}\);<\/script>/m';
        preg_match_all($res, $response, $matches);

        $data = isset($matches[1][0]) ? json_decode($matches[1][0], true) : [];

        $rawResults = [];
        $results = [];

        if (isset($data[31][0][12][2])) {
            $rawResults = $data[31][0][12][2];
        }

        foreach ($rawResults as $rawResult) {
        	if (count($results) >= 50) {
				break;
			}

            $result = [];

            self::filterResult($rawResult, $result);
            $data = self::getValues($result);

            $result = [];

            if (count($data) >= 11) {
                $result["keyword"]	= $keyword;
                $result["slug"]		= str_slug($keyword);
                $result["title"]	= isset($data[13]) ? ucwords(str_slug($data[13], " ")) : "";
                $result["alt"]		= isset($data[19]) ? str_slug($data[19], " "]) : "";
                $result["url"]		= $data[8];
                $result["thumb"]	= str_replace('&usqp=CAU', '', $data[4]);
                $result["filetype"] = self::getFileType($data[8]);
                $result["width"]	= $data[6];
                $result["height"]	= $data[7];
                $result["source"]	= isset($data[12]) ? $data[12] : "";
                $result["domain"]	= parse_url($data[12], PHP_URL_HOST);

                if (strpos($result["url"], "http") !== false) {
                    $results[] = $result;
                }

                $results[] = $result;
            }
        }

        return $results;
	}

	public static function getValues($array)
    {
    	$result = [];
    	foreach ($array as $key => $value) {
    		if (is_array($value)) {
    			foreach ($value as $vk => $vv) {
    				$result[] = $vv;
    			}
    		} else {
    			$result[] = $value;
    		}
    	}
    	return $result;
    }

    public static function array_flatten($array)
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result = array_merge($result, self::array_flatten($value));
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    public static function filterResult($array, &$result)
    {
        $array = array_filter($array);

        foreach ($array as $key => $value) {
            $data = [];

            if (filter_var($value, FILTER_VALIDATE_URL)) {
                $result[] = array_filter(self::array_flatten($array));
            }

            if (is_string($value)) {
                $result[] = $value;
            }

            if (is_array($value)) {
                self::filterResult($value, $result);
            }
        }
    }

    public static function getFileType($url)
	{
		$url 		= strtolower($url);
		$tmp 		= @parse_url($url)['path'];
		$ext 		= pathinfo($tmp, PATHINFO_EXTENSION);
		$arr_ext 	= ['jpg','png','webp','gif','bmp'];

		if(!in_array($ext, $arr_ext))
		{
			$ext 	= 'jpg';
		}

		return $ext;
	}
}