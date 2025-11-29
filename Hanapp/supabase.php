<?php
$supabase_url = "https://yajldhxreagwebnaueds.supabase.co";
$supabase_key = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InlhamxkaHhyZWFnd2VibmF1ZWRzIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjQ0MTk4NzUsImV4cCI6MjA3OTk5NTg3NX0.aR8JBrCkmDVxDnM9Zx0OYtyax5UvT_QbeUiwIuiwRfg";

// Universal REST request function
function supabase_request($method, $endpoint, $data = null) {
    global $supabase_url, $supabase_key;

    $curl = curl_init();

    $headers = [
        "apikey: $supabase_key",
        "Authorization: Bearer $supabase_key",
        "Content-Type: application/json"
    ];

    $options = [
        CURLOPT_URL => $supabase_url . $endpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_CUSTOMREQUEST => $method
    ];

    if ($data !== null) {
        $options[CURLOPT_POSTFIELDS] = json_encode($data);
    }

    curl_setopt_array($curl, $options);

    $response = curl_exec($curl);
    curl_close($curl);

    return json_decode($response, true);
}
function sb_select($table, $queryString = '')
{
    $endpoint = '/' . rawurlencode($table);
    if ($queryString) {
        $endpoint .= '?' . $queryString;
    }
    $res = supabase_request('GET', $endpoint);
    return $res;
}

function sb_getById($table, $id)
{
    $q = 'select=*&id=eq.' . rawurlencode($id);
    $rows = sb_select($table, $q);
    if (!$rows)
        return null;
    return $rows[0] ?? null;
}

function sb_insert($table, $row)
{
    // return representation
    $res = supabase_request('POST', '/' . rawurlencode($table), $row);
    if ($res['status'] !== 'ok')
        return ['success' => false, 'error' => $res['error'] ?? null, 'http' => $res['http']];
    // PostgREST returns inserted records as JSON array
    return ['success' => true, 'data' => $res['data']];
}

function sb_update($table, $matchQuery, $row)
{
    // $matchQuery should be something like "id=eq.3" or "email=eq.user@example.com"
    $endpoint = '/' . rawurlencode($table) . '?' . $matchQuery . '&select=*';
    $res = supabase_request('PATCH', $endpoint, $row);
    if ($res['status'] !== 'ok')
        return ['success' => false, 'error' => $res['error'] ?? null, 'http' => $res['http']];
    return ['success' => true, 'data' => $res['data']];
}
function sb_delete($table, $matchQuery)
{
    $endpoint = '/' . rawurlencode($table) . '?' . $matchQuery;
    $res = supabase_request('DELETE', $endpoint, null);
    if ($res['status'] !== 'ok')
        return ['success' => false, 'error' => $res['error'] ?? null, 'http' => $res['http']];
    return ['success' => true];
}


/* Example usage (server-side only):
   sb_insert('messages', ['sender_id'=>1,'receiver_id'=>2,'message'=>'Hello'])
   sb_select('bookings', 'select=*&user_id=eq.5')
*/
