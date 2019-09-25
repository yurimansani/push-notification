<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/html; charset=utf-8');

require_once('../connection.php');


if(!isset($_SESSION['id_cliente']))
    autenticacao_requerida_apis();

function enviar_push_android($rows, $data) {
    $app_key = "AIzaSyBU23UfNH7E_VNnvxECTKzcCM1T0geouaA";
    $url = 'https://fcm.googleapis.com/fcm/send';

    $ids = "[";
    foreach ($rows as $k => $v) {
        $ids .= '"'. $v["push_id"] . '",';
    }
    $ids = trim($ids, ",") . "]";

    $body = '{"registration_ids" : ' .  $ids . ', "data" : '. json_encode($data) . '}';

    $hdr = '';
    foreach (array(
                 "Content-Type" => "application/json",
                 "Authorization" => "key=". $app_key,
             ) as $k => $v) {
        $hdr .= $k . ":" . $v . "\r\n";
    }
    $opts = array('http' =>
        array(
            'method'  => 'POST',
            'header'  => $hdr,
            'content' => $body,
            'timeout' => 60,
            'ignore_errors' => true
        )
    );
    $ctx  = stream_context_create($opts);
    //print_pre($opts);
    $result = file_get_contents($url, false, $ctx , -1, 40000);

    $json2array = json_decode($result, true);

    //comentar print
    //print_pre($json2array);

    $del_types = array('InvalidRegistration','NotRegistered');
    $bad_ids = array();
    $results = $json2array['results'];
    $len = count($results);

    for ($i = 0; $i < $len; $i++) {
        if (!array_key_exists('error', $results[$i]) || !in_array($results[$i]['error'], $del_types))
            continue;

        if(array_key_exists($i ,$rows))
            $bad_ids[] = $rows[$i]['id_device'];
    }//for

    $bad_ids = array_filter($bad_ids);

    $count_bi = count($bad_ids);

    if ($count_bi == 0)
        return true;

    //comentar print
    //print_pre("Count do Bad Ids: ".$count_bi);

    // global $db_handle;
    // pg_exec($db_handle, "DELETE FROM device WHERE id_device IN (" . implode(',', $bad_ids) . ")" );

    //ob_end_flush();
    flush();
    //fastcgi_finish_request(); //cancela execução

    return true;
}

function enviar_push_ios($rows, $debug, $data) {
    $host = ($debug ? 'gateway.sandbox.push.apple.com' : 'gateway.push.apple.com');
    $port = ($debug ? 2195 : 2195);
    $cert = ($debug ? '/opt/bitnami/apache2/htdocs/webservices/cert/apns-dev.pem' : '/opt/bitnami/apache2/htdocs/webservices/cert/apns-prod.pem');
    $streamContext = stream_context_create();
    stream_context_set_option($streamContext, 'ssl', 'local_cert', $cert);
    $sock = stream_socket_client('ssl://' . $host . ':' . $port, $error, $errorString, 2, STREAM_CLIENT_CONNECT, $streamContext);

    $payload = json_encode( array_merge( $data, array( "aps" => array( "content-available" => 1 )) ) );
    $l2 = strlen($payload);
    $b2 = $l2 % 256;
    $b1 = ($l2-$b2)/256;
    $l2 = chr($b1) . chr($b2);

    $msg = "";
    foreach ($rows as $k => $r) {
        $id = pack('H*', $r['push_id']);

        $l1 = strlen($id);
        $b2 = $l1 % 256;
        $b1 = ($l1-$b2)/256;
        $l1 = chr($b1) . chr($b2);
        $msg .= chr(0) . $l1 . $id . $l2 . $payload;
    }//foreach

    fwrite($sock, $msg);

    fclose($sock);

    //ob_end_flush();
    flush();
    //fastcgi_finish_request(); //cancela execução

    return true;
}//function

function enviar_push($rows, $data) {

    if(!$rows)
        return;

    //Ids do Android
    $f = function($v) {
            return ($v['plataforma'] == 0);
    };
    $ids = array_filter($rows, $f);
    if (count($ids) > 0) {
        enviar_push_android($ids, $data);
        echo "Android: <pre>";
        var_dump($ids);
        echo "</pre><br>";
    }
    else
        echo "nenhum push id para Android.";

    //Ids do iOS Produção
    $f = function($v) {
            return ($v['plataforma'] == 1 && !$v['debug']);
    };
    $ids = array_filter($rows, $f);
    if (count($ids) > 0) {
        enviar_push_ios($ids, false, $data);

        echo "iPhone pro: <pre>";
        var_dump($ids);
        echo "</pre><br>";
    }
    else
        echo "nenhum push id para iPhone pro.";
    //Ids do iOS Dev
    $f = function($v) {
            return ($v['plataforma'] == 1 && $v['debug']);
    };
    $ids = array_filter($rows, $f);
    if (count($ids) > 0) {
        enviar_push_ios($ids, true, $data);

        echo "iPhone dev: <pre>";
        var_dump($ids);
        echo "</pre><br>";
    }
    else
        echo "nenhum push id para iPhone dev.";
    echo "<hr>";
}

//$rs = pg_query($db_handle, "SELECT id_tagpoint FROM tagpoint WHERE status=0 ORDER BY id_tagpoint limit 100");
$rs = pg_query($db_handle, "select id_tagpoint, titulo from tagpoint where tipo = 1 AND fk_cliente <> 1689 AND status=0 AND data_vencimento > now() and fk_cliente <> 265 and titulo not ilike '%TagPoint%' and titulo <> 'LIMBO' AND descricao <> 'iBeacon' and titulo not ilike '%teste%' and descricao not ilike '%teste%' and descricao <> '' and data_ultima_deteccao > now() - interval '1 month' limit 50");

$id_cliente = 1659;
//$id_cliente = 8;
$tags = pg_fetch_all($rs);

$rs = pg_query($db_handle, "SELECT * FROM device WHERE fk_cliente = $id_cliente");
$rows = pg_fetch_all($rs);

$count_device = pg_num_rows($rs);
pg_close($db_handle);

echo "<h1>Push Beacons</h1>";

if($count_device==0) {
    echo "Este cadastro não possui nenhum device ativo.";
    exit;
}

$total = 0;
foreach($tags as $k => $t) {
    $id_tagpoint = $t['id_tagpoint'];
    echo "id_tagpoint = " . $id_tagpoint . ", titulo = {$t['titulo']}<br>";
    $data = array( "cmd" => "TagOnline", "id" => $id_tagpoint);
    enviar_push($rows, $data);
    flush();
    sleep(0.2);
    $total++;
}

echo "TOTAL: $total beacons selecionados para o usuário: $id_cliente<br><br><br>";






