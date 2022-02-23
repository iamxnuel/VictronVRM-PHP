<?php

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
        return vrm_request("users/$idUser/installations?extended=1", $token, null, $isBearerToken);
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