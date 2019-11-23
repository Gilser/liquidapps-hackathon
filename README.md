# liquidapps-hackathon
Repository holding the source code for the tipit.io liquidapps hackathon entry.

To get the demo to function :

  1. First you'll need to install the Stripe PHP library, this can be done by 'composer require stripe/stripe-php', or you can download the library and follow the instructions at https://github.com/stripe/stripe-php.
  
  2.You'll need to have a Stripe developer account if you don't already have one. Once you have a Stripe developer account, you'll need to put your "Secret key" into the settings.php file $settings['stripe-key'].  Your key can be found under Home -> Get Your Test API Keys in the Stripe System.
  
  3. You'll need to create a test customer. To create a test customer in Stripe, click on "Customers" on the left menu, then the "+New" button. For name, enter an EOS wallet address, and for email, enter your, or someone else's email. For description, enter "EOS Account".
  
  4. Next, you'll need to enter the test customer id into the settings file. Click on the newly created customer you just created, and you'll see a page with an "ID" value, copy this value, and put it into the settings.php file $settings['customer-id'].
  
The demo is now set up to work with an "EOS" type checkout.  The "Tipit" type checkout will not work via a github download as the endpoint information is private, but this can be previewed at https://tipit.io/liquidapps-hackathon.
  
In a real world situation, the customer would be generated upon checkout if they didn't exist, but for the sake of the demo we used a customer that already existed in the system.
  
