Basic Usage
===========

Here's a quick example of how you can use the OpenGSQ library to query a server using the VCMP protocol:

.. code-block:: php

   <?php

   // Include the Composer autoloader
   require_once '../vendor/autoload.php';

   // Import the Vcmp class from the OpenGSQ\Protocols namespace
   use OpenGSQ\Protocols\Vcmp;

   // Create a new Vcmp object with the specified host and port
   $vcmp = new Vcmp('123.123.123.123', 8114);

   // Get the status of the server
   $status = $vcmp->getStatus();

   // Output the status information
   var_dump($status);

   // Get the players on the server
   $players = $vcmp->getPlayers();

   // Output the player information
   var_dump($players);

In this example, we first include the Composer autoloader and import the `Vcmp` class. We then create a new `Vcmp` object, specifying the host and port of the server we want to query. Finally, we call the `getStatus` and `getPlayers` methods to retrieve and output information about the server and its players.
