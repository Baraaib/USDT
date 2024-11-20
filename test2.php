<?php

class FixedFloatApi {
    const RESP_OK = 0;

    private $key = '';
    private $secret = '';

    public function __construct($key, $secret) {
        $this->key    = $key;
        $this->secret = $secret;
    }

    private function sign($data) {
        if (is_array($data)) {
            $parts = array();
            foreach ($data as $key => $value) {
                $parts[] = sprintf('%s=%s', $key, $value);
            }
            $str = join('&', $parts);
        } else {
            $str = $data;
        }
        return hash_hmac('sha256', $str, $this->secret);
    }

    private function req($method, $data) {
        $url = 'https://ff.io/api/v2/'.$method;

        $req = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            'X-API-KEY: '  . $this->key,
            'X-API-SIGN: ' . $this->sign($req)
        ));
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        if ($result['code'] == self::RESP_OK) {
            return $result['data'];
        } else {
            throw new Exception($result['msg'], $result['code']);
        }
    }

    public function ccies() {
        return $this->req('ccies', array());
    }
}

$key = 'l9zXjAOHF1Kq6TBZaNKBiD52ALX9Uhmo56qYrDBo';
$secret = 'G0JdfTNxGCK4vG68eYCOyrEKiCIusKO6wcdhO6Tn';

$api = new FixedFloatApi($key, $secret);

// الحصول على العملات المدعومة
try {
    $currencies = $api->ccies();
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supported Currencies</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

    <h1>Supported Currencies</h1>

    <?php if (isset($currencies) && !empty($currencies)): ?>
        <table>
            <thead>
                <tr>
                    <th>Currency</th>
                    <th>Coin</th>
                    <th>Network</th>
                    <th>Receive</th>
                    <th>Send</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($currencies as $currency): ?>
                    <tr>
                        <td><img src="<?= $currency['logo'] ?>" alt="<?= $currency['name'] ?>" width="30" /> <?= $currency['name'] ?></td>
                        <td><?= $currency['coin'] ?></td>
                        <td><?= $currency['network'] ?></td>
                        <td><?= $currency['recv'] ? 'Yes' : 'No' ?></td>
                        <td><?= $currency['send'] ? 'Yes' : 'No' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No currencies available at the moment.</p>
    <?php endif; ?>

</body>
</html>
