<?php

    function vrm_cache($idUser, $maxAge=30)
    {
        global $vrmCache;

        if($vrmCache)
            if(key_exists($idUser, $vrmCache))
            {
                $cacheItem = $vrmCache[$idUser];

                $cacheAge = (time() - $cacheItem["time"]) / 60;

                if($cacheAge < $maxAge)
                    return $cacheItem["data"];
            }

        return false;
    }

    function vrm_set_cache($idUser, $data)
    {
        global $vrmCache;

        $vrmCache[$idUser] = array(
            "time" => time(),
            "data" => $data
        );
    }

    function vrm_login_get_token($username, $password)
    {
        $vrm_request = curl_init();

        curl_setopt_array($vrm_request, array(
            CURLOPT_URL => "https://vrmapi.victronenergy.com/v2/auth/login",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode(array(
                "username" => $username,
                "password" => $password
            ))
        ));
        
        $output = curl_exec($vrm_request);

        curl_close($vrm_request);

        $vrm_response = json_decode($output, true);

        if($vrm_response["token"])
            return $vrm_response["token"];

        return false;
    }

    function vrm_request($method, $token, $payload, $isBearerToken=true)
    {
        $vrm_request = curl_init();

        curl_setopt_array($vrm_request, array(
            CURLOPT_URL => "https://vrmapi.victronenergy.com/v2/$method",
            CURLOPT_RETURNTRANSFER => true
        ));

        if($payload)
            curl_setopt_array($vrm_request, array(
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $payload
            ));
        
        $auth = ($isBearerToken ? "Bearer" : "Token") . " $token";

        curl_setopt($vrm_request, CURLOPT_HTTPHEADER, array(
            'X-Authorization: ' . $auth
        ));

        $output = curl_exec($vrm_request);

        curl_close($vrm_request);

        return json_decode($output, true);
    }

    function vrm_readfull($idUser, $token, $isBearerToken=true)
    {
        if($cache = vrm_cache($idUser))
            return $cache;

        $data = vrm_request("users/$idUser/installations?extended=1", $token, null, $isBearerToken);

        vrm_set_cache($idUser, $data);

        return $data;
    }

    function vrm_system_overview($idSite, $token, $isBearerToken=true)
    {
        $data = vrm_request("installations/$idSite/widgets/TempSummaryAndGraph", $token, null, $isBearerToken);

        return $data;
    }

    function vrm_get_site($idSite, $idUser, $token, $isBearerToken=true, $fullData=null)
    {
        if(!$fullData)
            $fullData = vrm_readfull($idUser, $token, $isBearerToken);

        $sites = vrmhelper_parse_sites($fullData);

        if(!$sites)
            return false;

        if(key_exists($idSite, $sites))
            return $sites[$idSite];

        return false;
    }

    function vrm_get_site_alarms(array $site)
    {
        if(!$site)
            return false;

        return $site["current_alarms"];
    }

    function vrm_get_site_timestamp(array $site)
    {
        if(!$site)
            return false;

        return $site["last_timestamp"];
    }
    
    function vrm_get_site_attribute(array $site, $idCode)
    {
        if(!$site)
            return false;

        if(!$site["extended"])
            return false;

        $attribute = null;

        foreach($site["extended"] as $attr)
            if($attr["code"] == $idCode)
                $attribute = $attr;

        if($attribute)
            return $attribute;

        return false;
    }

    function vrm_get_site_location(array $site)
    {
        if(!$site)
            return false;

        $lat = vrm_get_site_attribute($site, "lt");
        $lng = vrm_get_site_attribute($site, "lg");

        if($lat && $lng)
            return array(
                "lat" => $lat["rawValue"],
                "lng" => $lng["rawValue"]
            );

        return false;
    }

    function vrm_get_site_voltage(array $site)
    {
        return vrm_get_site_attribute($site, "bv");
    }
    
    function vrm_get_site_batterystate(array $site)
    {
        return vrm_get_site_attribute($site, "bst");
    }

    function vrm_get_site_altitude(array $site)
    {
        return vrm_get_site_attribute($site, "la");
    }

    function vrm_get_site_consumption(array $site)
    {
        return vrm_get_site_attribute($site, "consumption");
    }

    function vrm_get_site_solaryield(array $site)
    {
        return vrm_get_site_attribute($site, "solar_yield");
    }

    function vrm_get_site_batterysoc(array $site)
    {
        return vrm_get_site_attribute($site, "bs");
    }

    function vrm_get_site_current(array $site)
    {
        return vrm_get_site_attribute($site, "bc");
    }

    function vrm_get_site_acinput(array $site)
    {
        return vrm_get_site_attribute($site, "si1");
    }

    function vrm_get_site_systemstate(array $site)
    {
        return vrm_get_site_attribute($site, "ss");
    }

    function vrm_get_site_gridpower(array $site)
    {
        return vrm_get_site_attribute($site, "from_to_grid");
    }

    function vrm_get_site_switchposition(array $site)
    {
        return vrm_get_site_attribute($site, "s");
    }

    function vrm_get_site_lowstateofcharge(array $site)
    {
        return vrm_get_site_attribute($site, "ASoc");
    }

    function vrm_get_site_gridalarm(array $site)
    {
        return vrm_get_site_attribute($site, "Agl");
    }

    function vrmhelper_parse_sites($data)
    {
        if(!$data["success"])
            return false;

        $sites = array();

        foreach($data["records"] as $row)
            $sites[$row["idSite"]] = $row;

        return $sites;
    }


?>