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
      }
      td:first-child{
        background-color: peachpuff;
        font-weight: bold;
        border: 2px solid darkred;
        
      }
      tr:first-child{
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
      echo '<table border="1">';
      for($i = 0; $i <= 10; $i++)
      {
        echo '<tr>';
        for($j = 0; $j <= 10; $j++)
        {
          $class = '';

          if ($i === $j && $i > 0 && $j > 0)
            $class = ' class="diagonal"';
          echo '<td' . $class . '>';
          if ($i === 0 && $j === 0)
              echo ' ';
          elseif ($i === 0 || $j === 0)
            echo $i + $j;
          else
            echo $i * $j;
          echo '</td>';
        }
        echo '</tr>';
      }
      echo '</table>';
    ?>
  </body>
</html>
