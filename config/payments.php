<?php

return [
  'liqpay' => [
      'private_key' => env('LIQPAY_PRIVATE_KEY'),
      'public_key' => env('LIQPAY_PUBLIC_KEY'),
      'server_url' => env('APP_URL') . '/update_order_status',
  ],
];
