<!DOCTYPE html>
<html lang="">
  <head>
    <meta charset="utf-8">
    <title>Multiplication table</title>
    <style>
      table{
        border-collapse: collapse;
        width: 50%;
        margin: 20px auto;
      }
      td{
        font-size: 14pt;
        font-family: "sans-serif";
        text-align: center;
        border: 1px solid darkred;
      }
      td:first-child{
        background-color: peachpuff;
        font-weight: bold;
        border: 2px solid darkred;
      }
      tr:first-child td{
        background-color: peachpuff;
        font-weight: bold;
        border: 2px solid darkred;
      }
      tr:first-child td:first-child{
        background-color: darkred;
      }
      .diagonal{
        background-color: #fcff59;
        font-weight: bold;
      }
    </style>
  </head>
  <body>
    <?php
    require_once 'multiply.php';
    
    echo createMultiplicationTable(15, 15);
    ?>
  </body>
</html>