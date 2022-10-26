<?php


use SaintSystems\OData\ODataClient;

function main(array $args): array
{
    $id = $args['id'];
    $parcelInfo = null;

    $id = addHyphenToID($id);

    $odataServiceUrl = env('WA_KING_ODATA_SERVICEURL');
    $subKey = env('WA_KING_ODATA_SERVICESUBKEY');

    $odataClient = getODataClient($odataServiceUrl, $subKey);

    $permits = $odataClient
        ->from('PtasParceldetail') // problem
        ->where('PtasName', '=', $id)
        ->select('PtasParceldetailid')->expand(
            [
                'PtasPermitPtasParcelidValueNavigation($select=PtasPermittype,PtasIssueddate,PtasRevieweddate,PtasName,PtasLinktopermit,PtasDescription,PtasPermitvalue;$orderby=PtasIssueddate desc)',
            ]
        )
        ->get();

    foreach ($permits as $key => $value) {
        $permit = $value->PtasPermitPtasParcelidValueNavigation;
        $parcelInfo["permits"] = getPermitTableDetails($permit);
    }

    $payload = [
        "permits" => $parcelInfo["permits"],
    ];

    return ["body" => $payload];
    // return json_encode($payload);
}

function addHyphenToID($id)
{
    $separator = '-';
    $length = 6;

    if (strpos($id, $separator, $length))
        return $id;
    if (strlen($id) > $length) {
        $id = str_split($id, $length);
        return implode($separator, $id);
    }
    return null;
}

function getODataClient(string $odataServiceUrl, string $subscriptionKey)
{

    $odataServiceUrl = env('WA_KING_ODATA_SERVICEURL');      // 'https://services.odata.org/V4/TripPinService';
    // https://api-test.kingcounty.gov/ptas-uat-odataservices/v1.0/API
    $odataClient = new ODataClient($odataServiceUrl);

    // var_dump($odataClient);
    // die();

    $odataClient = new ODataClient($odataServiceUrl, function ($request) use (&$subscriptionKey) {
        $token = getToken();
        $accessToken = $token["token"];

        // Might have to create these in main
        $request->headers['Ocp-Apim-Subscription-Key'] = $subscriptionKey;
        $request->headers['Authorization'] = 'Bearer ' . $accessToken;
        // var_dump($request);
        // die();
    });

    // var_dump($odataClient);
    // die();

    return $odataClient;
}

function getPermitTableDetails($permits)
{
    $permitDetail = array();
    $permitDetail["headers"] = array((object)['title' => 'Permit Number', 'addlink' => false], 'Permit Description', 'Type', 'Issue Date', 'Value', 'Jurisdiction', 'Reviewed Date');
    $permitDetail["body"] = array();
    foreach ($permits as $key => $value) {
        $issueDate = isset($value["PtasIssueddate"]) ? date("Y-m-d", strtotime($value["PtasIssueddate"])) : "-";
        $reviewDate = isset($value["PtasRevieweddate"]) ? date("Y-m-d", strtotime($value["PtasRevieweddate"])) : "-";
        array_push($permitDetail["body"], array((object)[
            'data' => $value["PtasName"],
            'url' => $value["PtasLinktopermit"],
            'value' => $value["PtasName"],
        ], $value["PtasDescription"], (string)$value["PtasPermittype"],  $issueDate, "$" . number_format($value["PtasPermitvalue"]), '-', $reviewDate));
    }
    return $permitDetail;
}

function getToken(): array
{
    $TenantID = env('WA_KING_ODATA_TENANTID');
    $base_url = 'https://login.microsoftonline.com/' . $TenantID . '/oauth2/token';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $base_url);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array(
        'client_id' => env('WA_KING_ODATA_CLIENTID'),
        'client_secret' => env('WA_KING_ODATA_CLIENTSECRET'),
        'resource' => env('WA_KING_ODATA_CLIENTRESOURCE'),
        'grant_type' => 'client_credentials'
    ));
    $data = curl_exec($ch);
    $auth_string = json_decode($data, true);
    //Cache::put('token', $auth_string["access_token"], 2400);
    return array('token' => $auth_string["access_token"], 'expires_on' => $auth_string["expires_on"]);
}





// use NFNumberToWord\NumberToWords;

// function main(array $args): array
// {
//     if (!isset($args['number'])) {
//         return wrap(['error' => 'Please supply a number.']);
//     }

//     $number = (int)($args['number']);
//     $words = (new NumberToWords)->toWords($number);

//     // return [
//     //     'body' => $words,
//     // ];

//     return [
//         'body' => getenv('NAME'),
//     ];
// }

// function wrap(array $args): array
// {
//     return ["body" => $args];
// }
