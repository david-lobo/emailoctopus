<?php
  $variables = [
      'API_KEY' => 'REPLACE_WITH_YOUR_API_KEY',
      'LIST_ID' => 'REPLACE_WITH_LIST_ID'
  ];
  foreach ($variables as $key => $value) {
      putenv("$key=$value");
  }
?>