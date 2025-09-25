<?php
  echo "<h1>Servidor funcionando!</h1>";
  echo "<p>Teste de carga de CPU iniciado!</p>";

  $result = 0;
  for($i = 0; $i < 500000; $i++) {
      $result += sqrt($i) * sin($i);
  }

  echo "<p>Teste finalizado!</p>";
?>