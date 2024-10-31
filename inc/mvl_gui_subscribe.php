<?php

function mvl_processSubscribe($p) {
	$code = 0;
	$s = '';
	$id = '';
	$siteDetails = mvl_readOption(MVIS_LITE_OPT_NAME, 'siteDetails');
	
	if ($p == 10) { // Start of Subcription
		//retreive captcha and store captcha plus Id
		mvl_deleteOption(MVIS_LITE_OPT_NAME, 'productsArray');
		mvl_writeOption(MVIS_LITE_OPT_NAME, 'currency', 'EUR');
		mvl_writeOption(MVIS_LITE_OPT_NAME, 'productId',1);
		mvl_writeOption(MVIS_LITE_OPT_NAME, 'currentProduct',1);
		mvl_writeOption(MVIS_LITE_OPT_NAME,'SRchecked','checked');
		mvl_deleteOption(MVIS_LITE_OPT_NAME, 'couponCode');

		$apiRes = mvl_get_captcha($code, $id);
		if ($code == 200) {
			mvl_writeOption(MVIS_LITE_OPT_NAME, 'captchaId', $id);
			$captcha = "<img id=\"mvis-captcha\" src=\"data:image/jpeg;base64,$apiRes\">\r\n";
			mvl_writeOption(MVIS_LITE_OPT_NAME, 'captcha', $captcha);
			$res = mvl_getPageSubscribe(1, '', $captcha);
		}else {
			$err = mvl_getApiError('get_captcha', $code);
			$res = mvl_processSummary(0,__('There has been an error while requesting the captcha from the server. ',MVLTD). $err);
			return($res);
		}
	}
	
	if ($p == 13) { // subscribed package is chosen and sent to the server
		$authToken = mvl_readOption(MVIS_LITE_OPT_NAME, 'authToken');
		
		$coupon = mvl_getRequestParam('coupon');
		if($coupon){
			if(preg_match("/^MSC[a-z0-9]{14}-\d\d$/i", $coupon)){
				mvl_writeOption(MVIS_LITE_OPT_NAME, 'couponCode',$coupon);
			}else{
				//change the request parameter to redirect properly
				$_REQUEST['p'] = 10;
				$res = mvl_getPageSubscribe(1, __('The entered couponcode is invalid. Please enter it again.',MVLTD));
				return ($res);
			}
		}
		$product_ID = intval(mvl_getRequestParam('productId', '1'));
		if($product_ID == 99){
			$product_ID = mvl_readOption(MVIS_LITE_OPT_NAME,'currentProduct');
			$sCurrency = mvl_readOption(MVIS_LITE_OPT_NAME,'scurrency');
			mvl_writeOption(MVIS_LITE_OPT_NAME, 'currency',strtoupper($sCurrency));
		}
			
		
		mvl_writeOption(MVIS_LITE_OPT_NAME, 'productId', $product_ID);
			
		if(isset($siteDetails['id']))
			$siteId = $siteDetails['id'];		
		
		//Check if the user is already logged in
		//If that is the case and this code is reached,
		//this site is not active yet -  continue to the checkout page.
		if((isset($siteId) && $siteId != '') && (isset($authToken) && $authToken != '')){
			$callBackUrlSuccess = mvl_getAbsoluteAdminUrl(15);
			$callBackUrlError = mvl_getAbsoluteAdminUrl(16);
			$currency = mvl_readOption(MVIS_LITE_OPT_NAME, 'currency','EUR');
			if($coupon)
				$pplUrl = mvl_getServerURLPayPal($siteId, $authToken,'','', $callBackUrlSuccess, $callBackUrlError);
			else
				$pplUrl = mvl_getServerURLPayPal($siteId, $authToken, $currency, $product_ID, $callBackUrlSuccess, $callBackUrlError);
			$res = mvl_getPageSubscribe(3, '', false, $pplUrl);
			return ($res);
		}
		
		$captcha = mvl_readOption(MVIS_LITE_OPT_NAME, 'captcha');
		$res = mvl_getPageSubscribe(2, '', $captcha);
		return($res);
	}

	if (($p == 12) or ($p == 11)) {
		if ($p == 12) {
			$firstname = mvl_getRequestParam('firstname');
			$surname = mvl_getRequestParam('surname');
			$captchaCode = mvl_getRequestParam('captcha_code');
			$email = mvl_getRequestParam('email');  
			$password = mvl_getRequestParam('register_password');
			$organisation = mvl_getRequestParam('organisation');
			$captchaId = mvl_readOption(MVIS_LITE_OPT_NAME, 'captchaId');
			$captcha = mvl_readOption(MVIS_LITE_OPT_NAME, 'captcha'); // reread for use in errorsituations

			$apiRes = mvl_registerUser($code, $email, $password, $captchaId, $captchaCode, $organisation, $firstname, $surname);
			
			if ($code == 201) {
				// OK, Account created			
				mvl_writeOption(MVIS_LITE_OPT_NAME, 'userName', $email);
				mvl_deleteOption(MVIS_LITE_OPT_NAME, 'captchaId');
				mvl_deleteOption(MVIS_LITE_OPT_NAME, 'captcha');
				mvl_deleteOption(MVIS_LITE_OPT_NAME, 'captchaSrc');
			} else {
				$err = mvl_getApiError('registerUser', $code);			
				$s = '<strong>' . __('Error registering user: ',MVLTD) . '</strong>'. mvl_htmlEncode($err);
				$apiRes = mvl_get_captcha($code, $id);
				if ($code == 200) {
					mvl_writeOption(MVIS_LITE_OPT_NAME, 'captchaId', $id);
					$captcha = "<img id=\"mvis-captcha\" src=\"data:image/jpeg;base64,$apiRes\">\r\n";
					mvl_writeOption(MVIS_LITE_OPT_NAME, 'captcha', $captcha);
					$res = mvl_getPageSubscribe(2, $s, $captcha);
					} else {
						$err = mvl_getApiError('get_captcha', $code);
						$res = __('There has been an error while requesting the captcha from the server. ',MVLTD). $err;
					}
				return($res);
			}
		}
		
		if(!isset($email) && !isset($password)){
			if(mvl_getRequestParam('password') && mvl_getRequestParam('email')){	
				$email = mvl_getRequestParam('email');
				$password = mvl_getRequestParam('password');
			}
		}
		
		$err = mvl_Login($email, $password);
		if($err == 'active' && !mvl_renewSubscription())
			return (mvl_processSummary(0,$err));
		elseif ($err != '' && $err != 'created' && $err != 'inactive' && $err != 'active'){
			$captcha = mvl_readOption(MVIS_LITE_OPT_NAME, 'captcha');
			$res = mvl_getPageSubscribe(2, mvl_htmlEncode($err), $captcha);
			return($res);
		}
		
		//Everything seems in order, the site should now be either created 
		//or existing details retrieved.
		$site = mvl_readOption(MVIS_LITE_OPT_NAME, 'siteDetails');
		if(isset($site['id']))
			$siteId = $site['id'];
		else
			return(mvl_getPageSubscribe(2,__('There was an error creating the Site, please try again!',MVLTD)));
		
		$authToken = mvl_readOption(MVIS_LITE_OPT_NAME, 'authToken');
		//If the authToken has not been successfully set throw an error.
		//This should be caught in mvl_Login and never be the case here.
		if(!$authToken)
			return (mvl_getPageSubscribe(2, __('Error: No Authentication Token was found. Please logout/login or register again.',MVLTD)));
		
		//SiteID was successfully retrieved and Site is active
		//If users are not eligible to repurchase, then redirect them to the summary page
		if((isset($site['status']) && $site['status'] == 'ACTIVE') && !mvl_renewSubscription()) 
			return (mvl_processSummary(0, __('Your subscription is still valid. You can renew your subscription within two weeks before its expiration.',MVLTD)));		
		
		$callBackUrlSuccess = mvl_getAbsoluteAdminUrl(15);
		$callBackUrlError = mvl_getAbsoluteAdminUrl(16);
		$currency = mvl_readOption(MVIS_LITE_OPT_NAME, 'currency','EUR');
		$product_ID = mvl_readOption(MVIS_LITE_OPT_NAME, 'productId',1);
		$pplUrl = mvl_getServerURLPayPal($siteId, $authToken, $currency, $product_ID, $callBackUrlSuccess, $callBackUrlError);
		//All good, show the paypal link.
		$res = mvl_getPageSubscribe(3, '', false, $pplUrl);					
	}
	
	if ($p == 15 && $p == 16){
		mvl_deleteOption(MVIS_LITE_OPT_NAME, 'squantity');
		mvl_deleteOption(MVIS_LITE_OPT_NAME, 'sduration');
		mvl_deleteOption(MVIS_LITE_OPT_NAME, 'scurrency');
	}
	
	if ($p == 15) {
		$s = '<strong>' .__('Congratulations, the payment was processed successfully. Welcome to MVIS PROtection!',MVLTD) . '</strong>';
		$userName = mvl_readOption(MVIS_LITE_OPT_NAME, 'userName');
		$authToken = mvl_readOption(MVIS_LITE_OPT_NAME, 'authToken');
		$siteDetails = mvl_readOption(MVIS_LITE_OPT_NAME, 'siteDetails');
		if($userName == '' || $authToken == '')
			return (mvl_getPageSubscribe(2, __('Error: An error has occured, please logout/login or register again.',MVLTD)));
		
		$apiRes = mvl_getSiteDetails($code, $userName, $authToken, $siteDetails['id']);
		if ($code <> 200) {
			$err = mvl_getApiError('getSiteDetails', $code); 
			$s .= '<br />' . __('An unexpected error has occured while retrieving your Site status. Please contact us if this problem persists.',MVLTD) . '<br />' . $err;
		} else {
			if (!mvl_updateSiteDetailsAndSync($apiRes))
				$s .= __('The payment was successful, but there was an error while retrieving the site details. Please logout/login again.',MVLTD);
		}

		$res = mvl_getPageSubscribe(4, $s, false);
	}

	if ($p == 16) {
		$s = __('The subscription was not successful. Please try again.',MVLTD);
		$res = mvl_getPageSubscribe(4, $s, false);
	}

	return($res);
}

function mvl_getPageSubscribe($tab=1, $message='', $captcha='', $pplUrl='#') {
	global $mvl_checks_config;	
	global $mvlState;
	$productTables = '';
	$productShort = '';
	$productName = '';
	$amount = '';
	$currency= '';
	$quantity = array();
	$duration = array();
	$proceedUrl = mvl_getAbsoluteAdminUrl(13);
	$overViewUrl = mvl_getAbsoluteAdminUrl(1);
	
	$text = '';	
	$content = mvl_getPageStart('subscription', __('Subscribe to MVIS PROtection',MVLTD), $text, false, $mvlState->showProfile);
	if($_REQUEST['p'] == 10){
		$p = mvl_getJsonData($code, MVIS_LITE_PRODUCTS_URL);
		if($p)
			mvl_writeOption(MVIS_LITE_OPT_NAME, 'productsArray', $p);
		
		//Reset the values whenever the subscription process is started
		mvl_writeOption(MVIS_LITE_OPT_NAME,'squantity',1);
		mvl_writeOption(MVIS_LITE_OPT_NAME,'sduration',365);
		mvl_writeOption(MVIS_LITE_OPT_NAME,'scurrency','eur');
		
		$products_available = intval($p['available']);
		//Parse all available quantities and durations and sort the arrays accordingly
		for($i=1;$i<=$products_available;$i++){
			if(!in_array(intval($p['mvis'.$i.'quantity']),$quantity))
					array_push($quantity,intval($p['mvis'.$i.'quantity']));
			if(!in_array(intval($p['mvis'.$i.'duration']),$duration))
				array_push($duration,intval($p['mvis'.$i.'duration']));
		}
		sort($duration,SORT_NUMERIC);
		sort($quantity,SORT_NUMERIC);
		if($message != '')
			$productTables .= '<div class="error-message">' .mvl_htmlEncode($message) .' </div>';
		
		$productTables .= '
		 <div style="padding: 5px;"><strong>'
          .  __('Option A:',MVLTD). '&nbsp;</strong><a id="myHeader1-2" href="javascript:toggleDiv(\'optionboxes1\');" >'.__('Already have a code?',MVLTD) . '</a>
         </div>
         <div class="optionboxes" id="optionboxes1" style="display: none;padding: 5px;">
         	</br>
         	<form id="coupon" method="post" action="'.$proceedUrl.'">
                        <p><label for="coupon"><strong>'. __('Enter Coupon Code',MVLTD) .'</strong></label> <input type="text" id="coupon" name="coupon" placeholder="MSCXXXXXXXXXXXXXX-XX" class="required" size="20" style="width:170px;"/>
                        <input type="submit" value="'. __('Submit',MVLTD).'" /></p>
			</form>
			<div class="clear"></div>
         </div>
		 </br>
	 	<div style="padding: 5px;"><strong>'
            . __('Option B:',MVLTD). '&nbsp;</strong> <a id="myHeader2-2" href="javascript:toggleDiv(\'optionboxes2\');" >'. __('Simply want to subscribe this site for one year?',MVLTD) .'</a>
         </div>
         <div class="optionboxes" id="optionboxes2" style="display: none;padding: 5px;">';
         
         	$productTables .= '
         		<div class="tab-table table-subscribe">
		<table>
		<caption><span class="star-yellow"></span>'.__('MVIS PROtection - Feature Overview', MVLTD) .'</caption>
		<tbody>';
		
		//Iterate through the descriptions and tooltexts
		for($j=0;$j<sizeof($p['mvis1']);$j++){
			$productFeature = mvl_htmlEncode($p['mvis1'][$j]);
			$productFeatureTooltip = mvl_htmlEncode($p['mvis1tt'][$j]);
			$productTables .= '
			<tr>
			<td><span class="arrow-grey"></span><a href="#" class="mvltooltip" title="'.$productFeatureTooltip.'">'.$productFeature.'</a></td>
			</tr>';
		}
		//Settings for one year
		$productTables .= '
		<tr><td>
		<span class="arrow-grey"></span>'. __('Subscribe this site for 1 year for only:',MVLTD).' <select onchange="mvl_set_price(this.value);">
		<option selected="selected" value="EUR">'.mvl_htmlEncode($p['mvis1eur']).' '. __('EUR',MVLTD).'</option>
		<option value="USD">'.mvl_htmlEncode($p['mvis1usd']).' '. __('USD',MVLTD).'</option>
		<option value="SGD">'.mvl_htmlEncode($p['mvis1sgd']).' '. __('SGD',MVLTD).'</option>
		</select>
		</td></tr>
		<tr><td></td></tr>
		</tbody>
		</table>
		<div class="signup-button">
		<a href="'.$proceedUrl.'&productId=1"><span class="arrow-grey"></span>'. __('Click here to contine!',MVLTD).'</a>
		</div>
		</div>
		<div class="clear"></div>';	
		
		$productTables .= '</div></br>';
		
		$productTables .= '
		<div style="padding: 5px;"><strong>'
		. __('Option C:',MVLTD) .'&nbsp;</strong><a id="myHeader3-2" href="javascript:toggleDiv(\'optionboxes3\');" >'. __('Want to secure more than one site and get a juicy discount?',MVLTD). '</a>
		</div>
		<div class="optionboxes" id="optionboxes3" style="display: none;padding: 5px;">	
		
		<div class="tab-table table-subscribe">
		<table>
		<caption><span class="star-yellow"></span>'.__('MVIS PROtection - Feature Overview', MVLTD) .'</caption>
		<tbody>';
		
		//Iterate through the descriptions and tooltexts
		for($j=0;$j<sizeof($p['mvis1']);$j++){
			$productFeature = mvl_htmlEncode($p['mvis1'][$j]);
			$productFeatureTooltip = mvl_htmlEncode($p['mvis1tt'][$j]);
			$productTables .= '
			<tr>
			<td colspan="2"><span class="arrow-grey"></span><a href="#" class="mvltooltip" title="'.$productFeatureTooltip.'">'.$productFeature.'</a></td>
			</tr>';
		}
		$productTables .= '
		<tr><td>
		<span class="arrow-grey"></span>'. __('How many sites to you want to subscribe: ',MVLTD).'</td><td> 
		<select onchange="mvl_refresh_price(this.value,\'q\');">';
		for($i=0;$i<count($quantity);$i++){
			if($i==0)
				$productTables .= '<option selected="selected" value="'.intval($quantity[$i]).'">'.intval($quantity[$i]).' ' . __('site(s)',MVLTD).'</option>';
			else
				$productTables .= '<option value="'.intval($quantity[$i]).'">'.intval($quantity[$i]).' ' . __('site(s)',MVLTD).'</option>';
		}
		$productTables .= '</select>
		</td></tr>
		
		<tr><td>
		<span class="arrow-grey"></span>'. __('How long do you want to subscribe for: ',MVLTD).'</td><td> 
		<select onchange="mvl_refresh_price(this.value,\'d\');">';
		for($i=0;$i<count($duration);$i++){
			if($i==0)
				$productTables .= '<option selected="selected" value="'.intval($duration[$i]).'">'.intval($duration[$i]/365).'-' . __('year',MVLTD).'</option>';
			else
				$productTables .= '<option value="'.intval($duration[$i]).'">'.intval($duration[$i]/365).'-' . __('year',MVLTD).'</option>';
		}
		$productTables .= '</select>
		</td></tr>
		<tr><td colspan="2">
			<span class="arrow-grey"></span>';
		
			if(mvl_readOption(MVIS_LITE_OPT_NAME,'SRchecked') == 'checked')
				 $checkedSR = ' checked="checked" '; 
			else 
				$checkedSR = '';
			
		$productTables .= '<input type="checkbox"' . $checkedSR . ' name="cbSR" onclick="mvl_agree_selfregister(this.checked);" />&nbsp;';
			
		$productTables .= '
			<a href="#" class="mvltooltip" title="'.__('Checking this box will automatically use one of the coupons on this site and the remaining coupons will be sent to you by e-mail.',MVLTD).'">'.__('Automatically register this site after checkout.',MVLTD).'</a>
		</td></tr>
		<tr class="total"><td colspan="2">';
			$productTables .= '
			<span class="arrow-grey"></span><strong>'. __('Total:') .'&nbsp;</strong>
			<select onchange="mvl_refresh_price(this.value,\'c\');">
				<option selected="selected" value="eur">'.__('EUR',MVLTD).'</option>
				<option value="usd">'.__('USD',MVLTD).'</option>
				<option value="sgd">'.__('SGD',MVLTD).'</option>
			</select>
			<strong>
			<label id="mvis-pricing">';
			$productTables .= mvl_htmlEncode($p['mvis1eur']);
			$productTables .= '</label></strong>You Save: <label id="mvis-saving" style="width:auto;margin-top:-2px;color:green;font-weight: bold;">00.00 EUR</label>
		</td></tr>
		<tr><td colspan="2"></td></tr>
		</tbody>
		</table>
		<div class="signup-button">
		<a href="'.$proceedUrl.'&productId=99"><span class="arrow-grey"></span>'. __('Click here to contine!',MVLTD).'</a>
		</div>		  
		</div>';
		
		
        $productTables .= '<script> 
        	function toggleDiv(optionid) {
		      var optionboxes = document.getElementsByTagName("div");
		      for(var x=0; x<optionboxes.length; x++) {
		            name = optionboxes[x].getAttribute("class");
		            if (name == \'optionboxes\') {
		                  if (optionboxes[x].id == optionid) {
		                        if (optionboxes[x].style.display == \'block\') {
		                              optionboxes[x].style.display = \'none\';
		                        }
		                        else {
		                              optionboxes[x].style.display = \'block\';
		                        }
		                  }else {
		                        optionboxes[x].style.display = \'none\';
		                  }
		            }
		      }
			}
		</script>';
        
        $productTables .= '
        </div></br>'
        . __('Or do you want something specific?',MVLTD) . ' <a href="mailto:mvis_wp@sec-consult.com"target="_blank">'. __('Then just contact us directly!',MVLTD). '</a>';
		
	}
	$productId = mvl_readOption(MVIS_LITE_OPT_NAME, 'productId');
	$p = mvl_readOption(MVIS_LITE_OPT_NAME, 'productsArray');
	
	$coupon = mvl_readOption(MVIS_LITE_OPT_NAME, 'couponCode');
	if (intval($productId)){
		$currency = mvl_htmlEncode(mvl_readOption(MVIS_LITE_OPT_NAME, 'currency'));			
		$amount = mvl_htmlEncode($p['mvis'.$productId. strtolower($currency)]);
		$productName = $p['mvis'.$productId.'name'];
		$productName = mvl_htmlEncode($productName);
		$subscriptionDuration = $p['mvis'.$productId.'duration'];
	}
	
	
	if($coupon != ''){
		$coupon = mvl_htmlEncode($coupon);
		$products_available = intval($p['available']);
		$dur = intval(substr($coupon, strlen($coupon)-2));
		for($n=0;$n<$products_available;$n++){
			if ($p['mvis'.$n.'quantity'] == 1 && $p['mvis'.$n.'duration'] == ($dur*365)){
				$productName = mvl_htmlEncode($p['mvis'.$n.'name']);
				$couponProductId = $n;
			}
		}
	}
	
	$mainUrl = mvl_getMainUrl();
	$loginUrl = $mainUrl . '&p=11';
	$createAccountUrl = $mainUrl . '&p=12';
	$refreshcaptchaicon =  WP_PLUGIN_URL . '/mvis-security-center/images/refresh.png';
	
	$tabSelected = intval($tab)-1;

	$content .= '
	    <script type="text/javascript">
	    
		jQuery(document).ready(function() {
      
		      var tabs = jQuery("#tabs").tabs({
		      	  selected: ' . $tabSelected .',	
		          select: function(event, ui) {
		              return false;
		          }
		      });
      
      jQuery("a[id=submit]").click(function() {
          jQuery(this).parents("form").submit();
      });';
      
	//Validation for the login form
	$content .= '
		jQuery("#validation1").validate({
       		rules: {
        		email: {
            		required: true,
            		email: true
          		},
        		password: "required"
			 },
      		 messages: {
          		email: "'. __('Please enter a valid email address',MVLTD).'",
          		password: "'. __('Please enter your password',MVLTD).'",
          	}
      	});';
	//Validation for the registration form
	$content .= '
	
	jQuery.validator.addMethod("digit", function(value) {
		return value.match(/.*\d.*/);
	}, "'. __('Please enter at least one number',MVLTD) .'");
	jQuery.validator.addMethod("letter", function(value) {
		return value.match(/[a-z]/i);
	}, "'. __('Please enter at least one letter',MVLTD) .'");
	jQuery.validator.addMethod("special", function(value) {
		return value.match(/[!\"$%&\/()\._\-=?]/);
	}, "'. __('Enter one of these characters:',MVLTD) .' !\"$%/()._-=?");
    		
      jQuery("#validation2").validate({
        rules: {
        	firstname: "required",
        	surname: "required",
          	register_password: {
            	required: true,
            	digit: true,
            	letter: true,
            	special: true,
            	minlength: 8
          	},
          	confirm_password: {
            	required: true,
            	minlength: 8,
            	equalTo: "#register_password"
          	},
          	email: {
            	required: true,
            	email: true
          	},
			confirmemail: {
            	required: true,
            	equalTo: "#register_email"
          	},
          	terms: "required"
       },
       messages: {
          firstname: "'. __('Please enter your firstname',MVLTD).'",
          surname: "'. __('Please enter your lastname',MVLTD).'",
          password: {
            required: "'. __('Please provide a password',MVLTD).'",
            minlength: "'. __('Your password must be at least 8 characters long',MVLTD).'"
          },
          confirm_password: {
            required: "'. __('Please provide a password',MVLTD).'",
            minlength: "'. __('Your password must be at least 8 characters long',MVLTD).'",
            equalTo: "'. __('Please enter the same password as above',MVLTD).'"
          },
          email: "'. __('Please enter a valid email address',MVLTD).'",
          confirmemail: {
          	required: "'. __('Please repeat the e-mail address from above',MVLTD).'",
          	equalTo: "'. __('Please repeat the e-mail address from above',MVLTD).'",
          },
          captcha_code: "' . __('Please enter the text from the picture above',MVLTD) .'",
          terms: "'. __('Please accept our Terms and Conditions',MVLTD).'"
        }
      });
    });
    </script>';
	
	
	$content .= '
    
	
<div class="mvis-container">
        <div id="tabs">
  
          <ul>
            <li><a href="#tabs-1"><span class="tab-number">1.</span>' . __('Choose',MVLTD) . '</a></li>
            <li><a href="#tabs-2"><span class="tab-number">2.</span>' . __('Login or Register',MVLTD) .'</a></li>
            <li><a href="#tabs-3"><span class="tab-number">3.</span> '. __('Proceed to Checkout',MVLTD) .'</a></li>
            <li><a href="#tabs-4"><span class="tab-number">4.</span> '. __('Result',MVLTD) .'</a></li>
          </ul>
                  
              <div id="tabs-1">
 				'. $productTables . '
              </div>
              
              <div id="tabs-2">';
				if(mvl_readOption(MVIS_LITE_OPT_NAME, 'couponCode') == '')
                	$content .= '<h3>'. __('Chosen Product:',MVLTD) . ' <span class="grey-text">'. $productName . ' - ' . $amount . ' ' . $currency . '</span></h3>';
				else
					$content .= __('Please login or register before verifying the coupon code.',MVLTD) ; 
                
              $content .='                  
                  <div class="clear"></div>';

					if($message != '')
                 		$content .= '<div class="error-message">' .$message .' </div>';                  
      $content .= '
                  <div id="registration">
                      
                    <form id="validation1" method="post" action="'.$loginUrl.'">
                    
                      <fieldset>
      
                        <legend>'. __('login',MVLTD) .'</legend>
                        <p><label for="email">'. __('email address',MVLTD) .' *</label> <input type="text" id="email" name="email" placeholder="Email" class="required" /></p>
                        <p><label for="password">'. __('your password',MVLTD).' *</label> <input type="password" id="password" name="password" placeholder="Password" class="required" /></p>
                        <p><input type="submit" value="'. __('login',MVLTD).'" /> <a href="#" onclick="mvl_forgotPWD();return(false);">'. __('Reset Password',MVLTD) . '</a></p>
      
                      </fieldset>

					</form>

                    <form id="validation2" method="post" action="'.$createAccountUrl.'">

                      <fieldset>
                        
                        <legend>'. __('create account',MVLTD) .'</legend>
						<p><label for="register_name">'. __('first name',MVLTD) .' *</label> <input type="text" id="register_name" name="firstname" class="required" /></p>
						<p><label for="register_surname">'. __('surname',MVLTD) .' *</label> <input type="text" id="register_surname" name="surname" class="required" /></p>
                        <p><label for="register_email">'. __('email',MVLTD) .' *</label> <input type="text" id="register_email" name="email" class="required" /></p>
                        <p><label for="confirmemail">'. __('repeat email',MVLTD) .' *</label> <input type="text" id="confirmemail" name="confirmemail" class="required email" /></p>
                        <p><label for="organisation">'. __('organisation',MVLTD) .'</label> <input type="text" id="organisation" name="organisation" /></p>
                        <p><label for="register_password">'. __('password',MVLTD) .' *</label> <input type="password" id="register_password" name="register_password" class="password required" /></p>
                        <p><label for="confirm_password">'. __('repeat password',MVLTD) .' *</label> <input type="password" id="confirm_password" name="confirm_password" class="required" /></p>
                        <p><label for="captcha">'. __('captcha',MVLTD) .' &nbsp;&nbsp;&nbsp;<a href="#" onclick="mvl_refresh_captcha();return(false);"><img src="'.$refreshcaptchaicon.'" width="12px" height="12px"></a></label>'.$captcha.'</p>
                        <p><label for="captcha_code">'. __('captcha code',MVLTD) .' *</label> <input type="text" id="captcha_code" name="captcha_code" class="required" /></p>
                        
                        <div class="terms">
                          <p><label for="terms">'. __('I accept the ',MVLTD) . '<a href="https://mvis.sec-consult.com/mvis-sc/information/TERMS_AND_CONDITIONS.pdf" target="_blank">' . __('Terms & Conditions',MVLTD) .'</a> *</label> <input type="checkbox" id="terms" name="terms" class="required" /></p>
                        </div>
                        <p><input type="submit" value="'. __('create account',MVLTD) .'" /></p>
                      </fieldset>
                    
                    </form>
                  </div>
              </div>
                            
              <div id="tabs-3">
      			<h3>'.__('Summary',MVLTD).'</h3>';
    	  		
    	  		if($message != '')
	            	$content .= '<div class="error-message">' .$message .' </div>';
    	  			
    	  		$username = mvl_readOption(MVIS_LITE_OPT_NAME, 'userName');
    	  		$siteDetails = mvl_readOption(MVIS_LITE_OPT_NAME, 'siteDetails');
    	  	
    	 $content .= '	  
				<div class="tab-table table-subscribe-finish">
				<table>
				  <caption><span class="star-yellow"></span>';
    	 	if($coupon == '')
				  $content .=__('Subscription Details',MVLTD);
    	 	else
    	 		$content .=__('Coupon Details',MVLTD);
				  
		$content .= '
				</caption><tbody>';
    	 			if($username != ''){
    	 				$content.= '<tr>
    	 					<td>'.__('Username',MVLTD).'</td>
    	 					<td>'.mvl_htmlEncode($username).'</td>
    	 				</tr>';
    	 			}
    	 			if(isset($siteDetails['name']) && $siteDetails['name'] != ''){
    	 				$content.= '<tr>
	    	 				<td>'.__('Sitename',MVLTD).'</td>
	    	 				<td>'. mvl_htmlEncode($siteDetails['name']).'</td>
    	 				</tr>';
    	 			}
		$content .= '<tr>
					  <td>Product</td>
					  <td>'.$productName.'</td>
					</tr>';
		if ($coupon == ''){
			$content .=	'
				<tr>
					<td>Currency</td>
					<td>'.strtoupper($currency).'</td>
				</tr>';
		}
		
		$content .= '
					<tr>
					  <td>Duration</td>
					  <td>';
					  if ($coupon != '') $dur = intval(substr($coupon, strlen($coupon)-2));
					  else $dur = intval($subscriptionDuration/365);
					  
					  if($dur>1) 
					  	$content .= $dur . __(' Years',MVLTD);
					  else 	
					  	$content .= $dur . __(' Year',MVLTD);
					  
					  $content .='</td>
					</tr>';
		if (!$coupon){
			$content .=	'
					<tr class="total">
					  <td>'. __('Total',MVLTD) . '</td>
					  <td>'. $amount . ' ' . strtoupper($currency) .'</td>
					</tr>';
		}else{
			$content .=	'
			<tr class="total">
			<td>'. __('Code',MVLTD) . '</td>
			<td>'. $coupon .'</td>
			</tr>';
		}
			$content .='
					<tr align="center">
						<td colspan="2">';
						if(!$coupon){
							mvl_readOption(MVIS_LITE_OPT_NAME,'SRchecked') == 'checked' ? $selfregister = '&selfregister' : $selfregister = '' ;
								$content .= '<form action="'.$pplUrl.$selfregister.'&method=pp" target="_self" method="POST" class="subscription">
								<input type="submit" id="proceed" value="'.__('Proceed to Checkout with Paypal',MVLTD).'" />
								</form>';
						}else{
							//Todo: Remove static currency & productId
							if(!stristr($_SERVER['HTTP_USER_AGENT'], 'chrome') && !stristr($_SERVER['HTTP_USER_AGENT'], 'msie') && !stristr($_SERVER['HTTP_USER_AGENT'], 'firefox')){ //Safari does not set third-party cookies unless a POST request is sent to the third party
								$content .= '<form action="'.$pplUrl.'&method=coupon&currency=EUR&productId='.$couponProductId.'&code='.$coupon.'" target="_self" method="POST" class="subscription">
								<input type="submit" id="proceed" value="'.__('Proceed to Coupon Verification',MVLTD).'" />
								</form>';
							}else{
								//Todo: Remove static currency & productId
								$content .= '
								<div class="btn">
									<a href="'.$pplUrl.'&method=coupon&currency=EUR&productId='.$couponProductId.'&code='.$coupon.'" class="iframex">'.__('Proceed to Coupon Verification',MVLTD).'</a>
								</div>';
							}
						}							
		$content .= '
						</td>
					</tr>
				  </tbody>
				</table>
				</div>
              </div>

              <div id="tabs-4">
                
                <h3>' . __('Result',MVLTD) .'</h3>

                <div class="tab-table table-subscribe-finish">';
                //Catch errors 
		
		          if(mvl_getRequestParam('p') == 16){
		          	$content .= '<div class="error-message"><h2>' . $message . '</h2></div>';
		          	$content .= '<h2><strong>&rarr;</strong>&nbsp;<a href="'. $overViewUrl .'">'.__('Return to Plugin',MVLTD).'</a></h2>';
		          }
		          else{
		          	$content .= '<div class="success-message"><h2>' . $message . '</h2></div>';
		          	$content .= '<h2><strong>&rarr;</strong>&nbsp;<a href="'. $overViewUrl .'">'.__('Return to Plugin',MVLTD).'</a></h2>';
		          } 
		         	     
		          
 	$content .= '</div>
              </div>
        </div>
    
    </div>';
		
	$content .= mvl_getPageEnd();
	return($content);	
}

?>