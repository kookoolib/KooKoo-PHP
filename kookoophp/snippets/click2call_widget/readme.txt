
1) Please create a Premium KooKoo account  with outbound enabled .


2) Upload the files click2call.php and clicktocall.js to your web server
which supports PHP scripts.


3) Copy the following code on any page where you want the click to call
link

    <script src="clicktocall.js" type="text/javascript"></script>
    <div id="kookoo_click2call"></div>
    
An example is given in click2call.html file
  

4) Update $dial_to number to point to your KooKoo number. $dial_to is
declared in line 6 of click2call.php 


5) Update $api_key to your KooKoo api_key present in your dashboard.
$api_key is is declared in line 8 of click2call.php 


To Do:

1. You have to perform phone number validation if you want in your HTML
file. 
2. Ask users to enter the full number to be safe. That is prefix mobile
numbers with a 0 and prefix landline numbers 
with STD code.