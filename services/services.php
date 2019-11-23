<?php

$phpIn = file_get_contents('php://input');
$jsonIn = json_decode($phpIn, true);

$response = Array(
  "success" => false,
  "message" => "An error occurred."
);

if (isset($jsonIn['request'])) {

  $request = $jsonIn['request'];
  switch ($request) {

    case "stripe-record-eos":
      include "../settings.php";
      require_once('../vendor/autoload.php');
      \Stripe\Stripe::setApiKey($settings['stripe-key']);

      //create invoice item
      $createInvoiceItem = \Stripe\InvoiceItem::create([
        'customer' => $settings['customer-id'] ,
        'amount' => $_SESSION['amount'],
        'currency' => 'usd',
        'description' => $_SESSION['product'],
      ]);

      //create invoice
      $txid = "";
      $buyer = "";
      $seller = "";
      $amount = "";
      $memo = "";
      if (isset($jsonIn['txid'], $jsonIn['buyer'], $jsonIn['seller'], $jsonIn['amount'], $jsonIn['memo'])) {
        $txid = $jsonIn['txid'];
        $buyer = $jsonIn['buyer'];
        $seller = $jsonIn['seller'];
        $amount = $jsonIn['amount'];
        $memo = $jsonIn['memo'];

        if ($jsonIn['buyer'] == "tipitaccount") {
          $buyer = $settings['tipit-from-account-name'];
          $memo = "Tipit purchase - ".$jsonIn['memo'];
        }
      }



      $createInvoice = \Stripe\Invoice::create([
        'customer' => $settings['customer-id'] ,
        'custom_fields' => Array(
          Array("name" => "token", "value" => "EOS"),
          Array("name" => "buyer", "value" => $buyer),
          Array("name" => "amount", "value" => $amount),
          Array("name" => "seller", "value" => $seller),
        ),
          "description" => "txid : ".$txid.", memo : ".$memo
      ]);

      $invoiceToPay = $createInvoice->id;

      // pay the invoice
      $invoice = \Stripe\Invoice::retrieve(
        $invoiceToPay
      );
      $invoice->pay(["paid_out_of_band" => true]);

      $response = Array(
        "success" => true,
        "response" => "success"
      );
      break;

    case "tipit-payment":
      if (isset($jsonIn['amount'], $jsonIn['memo'])) {
        include "../settings.php";
        $url2 = $settings['send-tip-endpoint'];
        $ch2 = curl_init($url2);
        $testReq2 = array(
              "from_name" => $settings['tipit-from-account-name'],
              "from_uid" => $settings['tipit-from-account-uid'],
              "to_name" => $settings['tipit-to-account-name'],
              "to_uid" => $settings['tipit-to-account-uid'],
              "pid" => 5,
              "tid" => "8",
              "full_amount" => (float)$jsonIn['amount'],
              "memo" => $jsonIn['memo']
            );


        $jsonDataEncoded2 = json_encode($testReq2);

        //Tell cURL that we want to send a POST request.
        curl_setopt($ch2, CURLOPT_POST, 1);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch2, CURLOPT_POSTFIELDS, $jsonDataEncoded2);
        curl_setopt($ch2, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $result = curl_exec($ch2);
        $decodedResponse = json_decode($result, true);

        if (isset($decodedResponse['status']) && $decodedResponse['status'] == 'success') {
          $response = Array(
            "success" => true,
            "response" => $decodedResponse,
            "tipit_url" => "https://tipit.io/twitter/".$settings['tipit-to-account-name']."/".$decodedResponse['id']
          );
        }


      }
      break;

    case "fetch-content":
      $response = Array(
        "success" => true,
        "response" => "https://ipfs.globalupload.io/QmR9jyWyvruKEwXg219L9f1KihnywGS7FQyvCp7kp47wpD"
      );
      break;

    case "get-vceipt":
      //connect to remote login helper service for verification
      $url2 = 'http://kylin-dsp-1.liquidapps.io/v1/dsp/ipfsservice1/get_table_row';
      $ch2 = curl_init($url2);
      $testReq2 = array(
          "contract"=>"gilsertience",
          "scope"=>"newcustomer1",
          "table"=>"vceptable",
          "key"=>"3",
          "keytype" => "number"
          );
      $jsonDataEncoded2 = json_encode($testReq2);

      //Tell cURL that we want to send a POST request.
      curl_setopt($ch2, CURLOPT_POST, 1);
      curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch2, CURLOPT_POSTFIELDS, $jsonDataEncoded2);
      curl_setopt($ch2, CURLOPT_HTTPHEADER, array('Content-Type=> application/json'));

      //Execute the request
      $result2 = curl_exec($ch2);
      $resultArray = json_decode($result2, true);

      $response = Array(
          "success" => true,
          "response" => $resultArray
      );
      break;
  }

  header('Content-Type: application/json');
  echo json_encode($response);

}









?>
