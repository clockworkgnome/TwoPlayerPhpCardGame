<?php
session_start();
require_once('inc/db.inc.php');

// if the user is logged in
if ($_SESSION["bLoggedIn"]) {

	//get the users id
	$iUserID = $_SESSION["sUserID"];

	//get the users status
	$sSQL = "SELECT status FROM `s_members` WHERE `id` = '".$iUserID."'";
	$_SESSION["sUserStatus"] = $GLOBALS['MySQL']->getOne($sSQL);
}
?>
<html>
<head>
	<title>Game Client</title>
	<!--Import Google Icon Font-->
	<link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<!--Import materialize.css-->
	<link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>

	<!--Let browser know website is optimized for mobile-->
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
	<body>
		<script src='js/jquery-2.1.3.min.js'></script>
		<script type="text/javascript" src="js/materialize.min.js"></script>

		<script>
		$(document).ready(function(){
    $('.collapsible').collapsible();
  });
//****************************************************************************************************************
//this loop will updates what the other player is doing
//****************************************************************************************************************
			function myTimer() {
				$.post("gameUpdates.php",
				{
					gameRequest: "getUpdates",
				},
				function(data, status){
					if(status == "success"){
						$("#playerUpdates").html(data);
					}else{
						$("#playerUpdates").html("failed...");
					}
				});
			}
			myTimer();
			var myVar = setInterval(function(){ myTimer() }, 5000);

			function myStopFunction() {
			    clearInterval(myVar);
			}

//***********************************************************************************************
// if the user clicked the start button
//***********************************************************************************************
function startClicked(){
			$.post("cardgameclasses.php",
				    {
						gameRequest: "getCards",
				    },
				    function(data, status){
				        //alert("Data: " + data + "\nStatus: " + status);
				        if(status == "success"){
				        	//$.getScript("cardgameclasses.php");
				        	// this script will remove the start button and add the deck vissual
				        	$("#GameCommand").html(data);
				        }
				    });
}

//*******************************************************************************************************
// the user clicked the deck
//*******************************************************************************************************
function deckClicked(){
			$.post("cardgameclasses.php",
				    {
						gameRequest: "drawHand",
				    },
				    function(data, status){
				    	//alert("Data: " + data + "\nStatus: " + status);
				        if(status == "success"){
				        	//$.getScript("cardgameclasses.php");
				        	$("#playerHand").html(data);
				        }
				    });
}

//*******************************************************************************************************
// the user clicked the deck
//*******************************************************************************************************
function drawCard(){
			$.post("cardgameclasses.php",
				    {
						gameRequest: "drawCard",
				    },
				    function(data, status){
				    	//alert("Data: " + data + "\nStatus: " + status);
				        if(status == "success"){
				        	//$.getScript("cardgameclasses.php");
				        	$("#playerHand").append(data);
				        }
				    });
}
//*********************************************************************************************************
</script>
	<ul class="collapsible" data-collapsible="accordion">
    <li>
      <div class="collapsible-header"><i class="material-icons">expand_more</i>Your Hand</div>
      <div class="collapsible-body" id="playerHand"><span>Lorem ipsum dolor sit amet.</span></div>
    </li>
    <li>
      <div class="collapsible-header"><i class="material-icons">expand_more</i>Second</div>
      <div class="collapsible-body"><span>Lorem ipsum dolor sit amet.</span></div>
    </li>
    <li>
      <div class="collapsible-header"><i class="material-icons">expand_more</i>Third</div>
      <div class="collapsible-body"><span>Lorem ipsum dolor sit amet.</span></div>
    </li>
  </ul>
	<div id="mainGame"></div>
	<div id="playerUpdates"></div>
	<script>
	//*********************************************************************************************************
	//this is the code for the start button
	//*********************************************************************************************************

	var startButton = document.createElement('div');
	startButton.id = 'GameCommand';
	startButton.classList.add('row');
	startButton.innerHTML="<a class='waves-effect waves-light btn' onClick='startClicked()'><i class='material-icons'>play_arrow</i> Start</a>"
	document.getElementById('mainGame').appendChild(startButton);

	</script>
	</body>
</html>
