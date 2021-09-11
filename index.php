<?php
    session_start();
    use \PhpPot\Service\StripePayment;

    require_once "config.php";
    require_once "function.php";

    if (!empty($_POST["token"])) {
     
        require_once 'StripePayment.php';
        $stripePayment = new StripePayment();
        $_POST['email'] = getTVEmail($_POST['tv_code']);

        if(empty($_POST['email'])){
            $_SESSION['error_smsg']="Invalid Token";
        }else{
            $stripeResponse = $stripePayment->SubscribePlanFromCard($_POST);
            // echo '<pre>';
            // print_r($stripeResponse);die;
            if( isset($stripeResponse['code']) == 402 ) {
                $_SESSION['error_smsg']=$stripeResponse['message'];
            }else{
                if ($stripeResponse['status'] == 'active') {
                    $_SESSION['smsg']="User is subscribed to plan";
                    header("Refresh: 0; ");
                    exit();
                }else{
                    $_SESSION['error_smsg']="The charge is blocked as it’s considered fraudulent.";
                }
            }
        }
    }
?>
<html>
    <head>
        <title>Adtranquility</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <link rel="stylesheet" href="jQuery-Validation-Engine/css/validationEngine.jquery.css" type="text/css"/>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
        <link href="css/bootstrap.css" rel="stylesheet" type="text/css">
        <link href="css/style.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="jQuery-Validation-Engine/css/template.css" type="text/css"/>
        <style type="text/css">
            button.order {
    outline: 0px;
}
        </style>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="jQuery-Validation-Engine/js/languages/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8"></script>
        <script src="jQuery-Validation-Engine/js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
        <script src="js/jquery.payment.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="js/bootstrap.js"></script>
        <script>
            jQuery(document).ready(function(){
                // binds form submission and fields to the validation engine
                jQuery("#frmStripePayment").validationEngine();
            });
                
        </script>
    </head>
    <body>
    <header class="py-3">
        <div class="container">
            <div class="logo text-center"><img src="images/logo.png" class="img-fluid"></div>
            <h1 class="text-center text-black">Your Phone will be Secure Now</h1>
            <p class="text-center text-black">Complete your purchase to instantly banish malicious ads from your phone, that steal your private<br> information and slow down your device.</p>
        </div>
    </header>
    <div class="bottom-part py-5">
        <section class="form-part">
            <form id="frmStripePayment" method="POST">
                <div class="container">
                    <?php if(isset($_SESSION['smsg'])) { ?>
                        <div class="alert alert-success" role="alert" id="success-message">
                            <?php echo $_SESSION['smsg']; 
                                unset ($_SESSION["smsg"]);
                            ?>  
                        </div>
                    <?php  } ?>
                    <?php if(isset($_SESSION['error_smsg'])) { ?> 
                        <div class="alert alert-danger" role="alert" id="success-danger">
                            <?php echo $_SESSION['error_smsg']; 
                             unset ($_SESSION["error_smsg"]);?>
                        </div>
                    <?php  } ?>
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <p class="steps"><b>Step #1:</b>Choose plan</p>
                            <ul class="plans p-0">
                                <li class="d-flex b-line">
                                    <div class="l-part">
                                        <b>Plan</b>
                                    </div>

                                    <div class="r-part ml-auto">
                                        <b class="text-right">Price</b>
                                    </div>
                                </li>
                                <li class="d-flex t-smal ">
                                    <div class="l-part">
                                        <input type="radio" class="radio_first" id="html" name="plan" value="price_1JWyDdCg9BN3rqzaBU034i0P" checked><label for="html">[Save 82%] 1-year plan ($3.8/mo) + 1 Month Fee</label>
                                    </div>
                                    <div class="r-part ml-auto text-right">
                                        <label for="html">$49.85 every 1 year</label>
                                    </div>
                                </li>

                                <li class="d-flex t-smal ">
                                    <div class="l-part">
                                        <input type="radio" class="radio_second" id="css" name="plan" value="price_1JWyCrCg9BN3rqza3im9WnXZ"><label for="css">[Save 0%] 1-month plan ($4.85/mo)</label>
                                    </div>
                                    <input type='hidden' name='currency_code' value='USD'>
                                    <input type='hidden' name='item_name' value='Test Product'>
                                    <input type='hidden' name='item_number' value='PHPPOTEG#1'>
                                    <div class="r-part ml-auto text-right">
                                        <label for="css">$4.85 every month</label>
                                    </div>
                                </li>
                            </ul>

                            <div style="display: none;" class="alert alert-danger" role="alert" id="error-message_card"></div>
                            <div class="choose" id="credit">
                                <div class="form-group w-100">
                                    <label for="email">Credit Card Number:</label>
                                    <input type="tel" pattern="\d*" class="form-control cc-number validate[required,creditCard]" id="card-number" placeholder="Card number" name="card" required maxlength="19">
                                </div>
                                <div class="form-group">
                                    <label for="pwd">Expiry *:</label>
                                    <input type="tel" pattern="\d*" maxlength="7" class="form-control cc-expires validate[required],minSize[7],maxSize[7]" id="month_year" placeholder="MM/YY" name="month_year" required>
                                </div>
                                <div class="form-group">
                                    <label for="pwd">CVC Code:</label>
                                    <input type="tel" pattern="\d*" maxlength="4" class="form-control cc-cvc validate[required],minSize[3],maxSize[4]" id="cvc" placeholder="CVC" name="cvc" required >
                                </div>
                            </div>

                            <div class="list-points">
                                <p><span>1</span>Complete your purchase</p>
                                <p><span>2</span>Download AdTranquility App</p>
                                <p><span>3</span>In 1 click protect your phone!</p>
                            </div>

                            <ul id="first" class="plans p-0">
                                <li class="d-flex b-line">
                                    <div class="l-part">
                                    <b>TOTAL</b>
                                    </div>

                                    <div class="r-part ml-auto">
                                    <b class="text-right">BILLED NOW</b>
                                    </div>
                                </li>
                                <li class="d-flex t-smal ">
                                    <div class="l-part">
                                        [Save 82%] 1-year plan ($3.8/mo) + 1 Month Fee
                                    </div>
                                    <div class="r-part ml-auto text-right">
                                        $49.85 every 1 year
                                    </div>
                                </li>
                            </ul>
                            <ul id="second" class="plans p-0">
                                <li class="d-flex b-line">
                                    <div class="l-part">
                                        <b>TOTAL</b>
                                    </div>
                                    <div class="r-part ml-auto">
                                        <b class="text-right">BILLED NOW</b>
                                    </div>
                                </li>

                                <li class="d-flex t-smal ">
                                    <div class="l-part">
                                        [Save 0%] 1-month plan ($4.85/mo)
                                    </div>
                                    <div class="r-part ml-auto text-right">
                                        $4.85 every month
                                    </div>
                                </li>
                            </ul>
                            <!--  <ul id="second" class="plans p-0">
                                    <li class="d-flex b-line">
                                    <div class="l-part">
                                    <b>TOTAL</b>
                                    </div>

                                    <div class="r-part ml-auto">
                                    <b class="text-right">BILLED NOW</b>
                                    </div>
                                    </li>

                                    <li class="d-flex t-smal ">
                                    <div class="l-part">
                                    [Save 0%] 1-month plan ($9.97/mo)
                                    </div>

                                    <div class="r-part ml-auto text-right">
                                    $9.97 every month
                                    </div>
                                    </li>
                            </ul> -->

                            <button type="submit" class="order" onClick="stripePay(event);"><b>Complete Order</b><br>Get Instant Access</button>
                            <div class="card-image text-center py-3"><img src="images/credit.png" class="img-fluid"></div>

                            </div>

                            <div class="col-sm-12 col-md-6">
                                <h2 class="text-center">Contact Information</h2>
                                <b>Step #2</b>
                                <div class="info">
                                    <div class="form-group">
                                        <label for="pwd"><b>TV Code:</b></label>
                                        <input type="zip" class="form-control validate[required]" id="tv_code" placeholder="Tv Code" name="tv_code" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Name:</label>
                                        <input type="text" class="form-control validate[required]" id="username" placeholder="Your Name" name="username" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="pwd">Email Address:</label>
                                        <input type="email" class="validate[required,custom[email]] form-control" id="email" placeholder="Email Address" name="email" required>
                                    </div>
                                        <div class="form-group">
                                        <label for="pwd">Zip Code:</label>
                                        <input type="text" class="form-control validate[required,custom[number]]" id="zip" placeholder="Zip Code" name="zip" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>
        <footer>
            <div class="container">
                <div class="inner">
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <div class="d-flex  group">
                                <div><img src="images/seal1.png"></div>
                                <div class="text"><b>30 Day Guarantee</b><br>Unhappy for whatever reason - get all your money back. We always honor our guarantee.</div>
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-6">
                            <div class="d-flex  group">
                                <div><img src="images/lock.png"></div>
                                <div class="text"><b>Secure Payment</b>
                                    <br>All orders are through a very secure network. Your credit card information is never stored in any way. We respect your privacy.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    <script type="text/javascript" src="https://js.stripe.com/v2/"></script>

    <script>
        $(".t-smal").click(function(){
            $(".t-smal").removeClass('bg-white');
            $(this).addClass("bg-white");
        });
        

        jQuery(function () {
            jQuery('#credit .cc-number').formatCardNumber();
            jQuery('#credit .cc-expires').formatCardExpiry();
            jQuery('#credit .cc-cvc').formatCardCVC();
        });



        function cardValidation () { 
            var valid = true;
            var name = $('#username').val();
            var email = $('#email').val();
            var cardNumber = $('#card-number').val();
            var month_year = $('#month_year').val();
            var cvc = $('#cvc').val();
            var tv = $('#tv_code').val();
            var zip = $('#zip').val();

            $("#error-message").html("").hide();

            if (name.trim() == "") {
                valid = false;
            }
            
            if (email.trim() == "") {
                valid = false; 
            }
            if (cardNumber.trim() == "") {
                valid = false;
            }
            if (month_year.trim() == "") {
                    valid = false;
            }
            if (cvc.trim() == "") {
                valid = false;
            }
            if (tv.trim() == "") {
                valid = false;
            }
            if (zip.trim() == "") {
                valid = false;
            }
            if(valid == false) {
                $("#error-message_card").css("display", "block")
                $("#error-message_card").html("All Fields are required");
                // console.log("All Fields are required");
            }

            return valid;
        }
        //set your publishable key
        Stripe.setPublishableKey("<?php echo STRIPE_PUBLISHABLE_KEY; ?>");

        //callback to handle the response from stripe
        function stripeResponseHandler(status, response) {
            if (response.error) {
                //enable the submit button
                $("#submit-btn").show();

                //display the errors on the form
                $("#error-message_card").css("display:block");
                $("#error-message_card").html(response.error.message).show();
            } else {
                //get token id
                var token = response['id'];

                //insert the token into the form
                $("#frmStripePayment").append("<input type='hidden' name='token' value='" + token + "' />");
                //submit form to the server
                $("#frmStripePayment").submit();
            }
        }

        function stripePay(e) {
            e.preventDefault();
            var valid = cardValidation();

            if(valid == true) {
                $("#submit-btn").hide();
                $( "#loader" ).css("display", "inline-block");
                var month_year = $('#month_year').val(),
                    month_year = month_year.split("/");
                var month = parseInt(month_year[0]);
                var year = parseInt(month_year[1]);
                Stripe.createToken({
                    number: $('#card-number').val(),
                    cvc: $('#cvc').val(),
                    exp_month: month,
                    exp_year: year
                }, stripeResponseHandler);

                //submit from callback
                return false;
            }
        }


        $(document).ready(function(){

            if($('.radio_first').is(':checked')){
                $("#second").hide();
            }

            $(".radio_first").click(function(){
                $("#first").show();
                $("#second").hide();
            });
            $(".radio_second").click(function(){
                $("#first").hide();
                $("#second").show();
            });
            });

            $('#card-number').on('keypress change blur', function () {
            $(this).val(function (index, value) {
                return value.replace(/[^a-z0-9]+/gi, '').replace(/(.{4})/g, '$1 ');
            });
            });

            $('#card-number').on('copy cut paste', function () {
            setTimeout(function () {
                $('#card-number').trigger("change");
            });
            });
        </script>
    </body>
</html>