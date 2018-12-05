<?php // Script 9.6 - login.php #3

/* This page lets people log into the site (almost!). */

header( "refresh:1.3;url=login.php" );


// Set the page title and include the header file:

define('TITLE', 'Error');

include('templates/header.html');



// Print some introductory text:

print '<h2>Error Page</h2>

	<p>You need to be a member, Please sign in before using the map.</p>';


include('templates/footer.html'); // Need the footer.
exit();
?>