<?php
$value1 = '123';
echo $value1 . "<br>";

if(preg_match('/^\d+$/', $value1))
{
    echo "1) Matched :)<br><br>";
}
else
{
    echo "1) Unmatched :(<br><br>";
}

$value2 = 'ferraefa';
echo $value2 . "<br>";

if(preg_match('/^[\da-zA-Z]+$/', $value2))
{
    echo "2) Matched :)<br><br>";
}
else
{
    echo "2) Unmatched :(<br><br>";
}

$value3 = 'ferraefaернвекно';
echo $value3 . "<br>";

if(preg_match('/^[\da-zA-ZА-Яа-яёЁ]+$/u', $value3))
{
    echo "3) Matched :)<br><br>";
}
else
{
    echo "3) Unmatched :(<br><br>";
}

$value4 = 'google.com';
echo $value4 . "<br>";

if(preg_match('/^([\w\-]+\.){0, 2}[a-z\d][a-z\d\-]*[a-z\d]\.[a-z]{2, 16}/i', $value4) and !preg_match('/\-\-/', $value4))
{
    echo "4) Matched :)<br><br>";
}
else
{
    echo "4) Unmatched :(<br><br>";
}

$value5 = 'Spa12';
echo $value5 . "<br>";

if(preg_match('/^[a-zA-Z][\da-zA-Z]{2, 24}$/', $value5))
{
    echo "5) Matched :)<br><br>";
}
else
{
    echo "5) Unmatched :(<br><br>";
}

$value6 = 'fraffrsrerefawe';
echo $value6 . "<br>";

if(preg_match('/^[a-zA-Z]+$/', $value6))
{
    echo "6) Matched :)<br><br>";
}
else
{
    echo "6) Unmatched :(<br><br>";
}

$value7 = 'fraFF4542rerefawe!()';
echo $value7 . "<br>";

if(preg_match('/^[a-zA-Z\d!@#$%^&*()_+=-]{8,}$/', $value7))
{
    echo "7) Matched :)<br><br>";
}
else
{
    echo "7) Unmatched :(<br><br>";
}

$value8 = '2026-02-24';
echo $value8 . "<br>";

if(preg_match('/^\d{1, 4}\-(0[1-9]|1[12])\-(0[1-9]|[12][0-9]|3[01])$/', $value8))
{
    echo "8) Matched :)<br><br>";
}
else
{
    echo "8) Unmatched :(<br><br>";
}

$value9 = '18/11/2004';
echo $value9 . "<br>";

if(preg_match('/^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[12])\/\d{1, 4}$/', $value9))
{
    echo "9) Matched :)<br><br>";
}
else
{
    echo "9) Unmatched :(<br><br>";
}

$value10 = '04.12.2016';
echo $value10 . "<br>";

if(preg_match('/^(0[1-9]|[12][0-9]|3[01])\.(0[1-9]|1[12])\.\d{1, 4}$/', $value10))
{
    echo "10) Matched :)<br><br>";
}
else
{
    echo "10) Unmatched :(<br><br>";
}

$value11 = '23:50:01';
echo $value11 . "<br>";

if(preg_match('/^(0\d|1\d|2[0-3]):[0-5]\d:[0-5]\d$/', $value11))
{
    echo "11) Matched :)<br><br>";
}
else
{
    echo "11) Unmatched :(<br><br>";
}

$value12 = '23:50';
echo $value12 . "<br>";

if(preg_match('/^(0\d|1\d|2[0-3]):[0-5]\d$/', $value12))
{
    echo "12) Matched :)<br><br>";
}
else
{
    echo "12) Unmatched :(<br><br>";
}

$value13 = 'https://www.yandex.ru/';
echo $value13 . "<br>";

if(preg_match('/^(http|https):\/\/([\w\-]+\.){0, 2}[a-z\d][a-z\d\-]*[a-z\d]\.[a-z]{2, 16}\/$/i', $value13) and !preg_match('/\-\-/', $value13))
{
    echo "13) Matched :)<br><br>";
}
else
{
    echo "13) Unmatched :(<br><br>";
}

$value14 = 'user@maildomain.com';
echo $value14 . "<br>";

if(preg_match('/^[A-Za-z0-9._-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/', $value14))
{
    echo "14) Matched :)<br><br>";
}
else
{
    echo "14) Unmatched :(<br><br>";
}

$value15 = '94.137.192.210';
echo $value15 . "<br>";

if(preg_match('/^(\d|\d\d|1\d\d|2[0-5][0-5])\.(\d|\d\d|1\d\d|2[0-5][0-5])\.(\d|\d\d|1\d\d|2[0-5][0-5])\.(\d|\d\d|1\d\d|2[0-5][0-5])$/', $value15))
{
    echo "15) Matched :)<br><br>";
}
else
{
    echo "15) Unmatched :(<br><br>";
}

$value16 = '2001:0:9d38:6abd:c70:2d3c:a176:3398';
echo $value16 . "<br>";

if(preg_match('/^([0-9a-fA-F]{1,4}:){7}[0-9a-fA-F]{1,4}$/', $value16))
{
    echo "16) Matched :)<br><br>";
}
else
{
    echo "16) Unmatched :(<br><br>";
}

$value17 = 'ec:23:3d:1b:7a:e7';
echo $value17 . "<br>";

if(preg_match('/^([0-9a-fA-F]{2}:){5}[0-9a-fA-F]{2}$/', $value17))
{
    echo "17) Matched :)<br><br>";
}
else
{
    echo "17) Unmatched :(<br><br>";
}

$value18 = '+79021234567';
echo $value18 . "<br>";

if(preg_match('/^(\+7|8)\d{10}$/', $value18))
{
    echo "18) Matched :)<br><br>";
}
else
{
    echo "18) Unmatched :(<br><br>";
}

$value19 = '4048 4323 9889 3301';
echo $value19 . "<br>";

if(preg_match('/^([0-9]{4} ){3}[0-9]{4}$/', $value19))
{
    echo "19) Matched :)<br><br>";
}
else
{
    echo "19) Unmatched :(<br><br>";
}

$value20 = '380870115601';
echo $value20 . "<br>";

if(preg_match('/^[0-8]\d(\d{8}|\d{10})$/', $value20))
{
    echo "20) Matched :)<br><br>";
}
else
{
    echo "20) Unmatched :(<br><br>";
}

$value21 = '664000';
echo $value21 . "<br>";

if(preg_match('/^\d{6}$/', $value21))
{
    echo "21) Matched :)<br><br>";
}
else
{
    echo "21) Unmatched :(<br><br>";
}

$value22 = '1000000,00 рублей';
echo $value22 . "<br>";

if(preg_match('/^[1-9]\d*\,\d{2} (руб.|р.|рублей)$/', $value22))
{
    echo "22) Matched :)<br><br>";
}
else
{
    echo "22) Unmatched :(<br><br>";
}

$value23 = '$39.99';
echo $value23 . "<br>";

if(preg_match('/^\$\d+\.\d{2}$/', $value23))
{
    echo "23) Matched :)<br><br>";
}
else
{
    echo "23) Unmatched :(<br><br>";
}
?>