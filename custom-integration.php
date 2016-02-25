<?php
/* Code created by Hyacinthe Hamon (https://github.com/HyacintheHamon) */

/* Stripe init if you're using composer (https://getcomposer.org/) */
require '../composer/vendor/autoload.php';

/* If you don't use Composer, Download Stripe PHP library, and add this line with the path to the file*/
require_once('PATH-TO-THE-LIBRARY/stripe-php/init.php');

\Stripe\Stripe::setApiKey('YOUR API KEY');

$name = $_POST['name'];
$address = $_POST['address'];
$postcode = $_POST['postcode'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$plan = $_POST['plan'];
$token = $_POST['stripeToken'];

try{

	// make sure we have the payment token first
	if (empty($token)) {
		$message = 'Payment could not be completed, please try again.';
		die($message);
	}
	
	try {
        
    // Create the customer
	$customer = \Stripe\Customer::create(array(
		'source' => $token,
		'email' => $email,
		'description' => "Name:".$name.", Email:".$email,
		//Here we can pass some metada to be seen on stripe
		'metadata' => array(
			"Name" => $name, 
			"Email" => $email, 
			"Phone" => $phone, 
			"Address" => $address,
			"Plan" => $plan,
			"Unique ID" => $uniqueid
		)
	));
	
	// In case you wanna add your cuustomer to a plan for recurring billing, here's how you do it:
if (!empty($plan) && $plan == 'payasyougo') {
	    
		$cu = \Stripe\Customer::retrieve($customer->id);
		$cu->subscriptions->create(array(
			//Your plan name on Stripe, Here we call it 'payasyougo'
			// You need to have previously created a plan on stripe with the same name
			"plan" => "payasyougo", 
			'metadata' => array(
				"Name" => $name, 
				"Email" =>$email, 
				"Phone"=>$phone, 
				"UID" => $uniqueid,
				"Plan" => $plan,
				"Address" =>$address
			)
		));
		
		
	} elseif (!empty($plan) && $plan == 'monthly') {
		
        //Your plan name on Stripe, Here we call it 'monthly'
        // You need to have previously created a plan on stripe with the same name
		$cu = \Stripe\Customer::retrieve($customer->id);
		$cu->subscriptions->create(array(
			"plan" => "monthly", 
			'metadata' => array(
				"Name" => $name, 
				"Email" =>$email, 
				"Phone"=>$phone, 
				"UID" => $uniqueid,
				"Plan" => $plan,
				"Address" =>$address
			)
		));

	}
    
    // Let's deal with the potential errors
    catch(Stripe_CardError $e) {
    $error1 = $e->getMessage();
            
} catch (Stripe_InvalidRequestError $e) {
  // Invalid parameters were supplied to Stripe's API
  $error2 = $e->getMessage();
          	    	      
} catch (Stripe_AuthenticationError $e) {
  // Authentication with Stripe's API failed
  $error3 = $e->getMessage();
         	      
} catch (Stripe_ApiConnectionError $e) {
  // Network communication with Stripe failed
  $error4 = $e->getMessage();

} catch (Stripe_Error $e) {
  // Display a very generic error to the user, and maybe send
  // yourself an email
  $error5 = $e->getMessage();
           
} catch (Exception $e) {
  // Something else happened, completely unrelated to Stripe
  $error6 = $e->getMessage();

}

if ($success!=1)
{
    $_SESSION['error1'] = $error1;
    $_SESSION['error2'] = $error2;
    $_SESSION['error3'] = $error3;
    $_SESSION['error4'] = $error4;
    $_SESSION['error5'] = $error5;
    $_SESSION['error6'] = $error6;
	
	//Success! We have a new customer
	echo"Customer added successfully to stripe"
	//Do whatever you want 
}
?>