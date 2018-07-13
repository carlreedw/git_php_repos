// author: carlreedw@gmail.com
// date: 7/12/2018
// purpose: This script allows the interface to request an updated list of PHP repos from GitHub

$(document).ready(function(){
	// refresh_client();
});

function initCollapsible() {	// I tend to prefer named functions over anonymous functions
	$('.collapsible').collapsible();
}

function refresh_database() {
	$("#mainContent").load("includes/refresh_database.php");
}

function refresh_client() {
	$("#mainContent").load("includes/refresh_client.php", initCollapsible);
}