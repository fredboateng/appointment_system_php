		<h1></h1>
		<form id="form_958923" class="appnitro"  method="post" action="index.php">
					<div class="form_description">
			<h2>Enrollment test appointment system</h2>
		</div>
		<ul >
		<li id="li_1" >
		<label class="description" for="element_1">Name </label>
		<div>
			<input id="name" name="name" class="element text medium" type="text" maxlength="255"  value="<?php echo $_REQUEST['name'];?>"/> 
		</div> 
		</li>

		<?php if($_REQUEST['name']=='' && $_REQUEST["action"] == 'login'){?>
		<p class="error">Please enter your Name</p>
		<?php ;}?>

		<li id="li_2" >
		<label class="description" for="element_2">Last Name </label>
		<div>
			<input id="lname" name="lname" class="element text medium" type="text" maxlength="255"  value="<?php echo $_REQUEST['lname'];?>"/> 
		</div> 
		</li>

		<?php if($_REQUEST['lname']=='' && $_REQUEST["action"] == 'login'){?>
		<p class="error">Please enter your Last Name</p>
		<?php ;}?>

		<li id="li_3" >
		<label class="description" for="element_3">Phone (e.g. 6509612044)</label>
		<div>
			<input id="phone" name="phone" class="element text medium" type="text" maxlength="10"  value="<?php echo $_REQUEST['phone'];?>"/> 
		</div> 

		<?php if($_REQUEST['phone']=='' && $_REQUEST["action"] == 'login'){?>
		<p class="error">Please enter phone #</p>
		<?php ;}?>

		<?php if($_REQUEST['phone']!='' & $wrong_phone == 1){?>
		<p class="error">Wrong phone # format</p>
		<?php ;}?>

		 
		</li>
		<li id="li_4" >
		<label class="description" for="element_4">Email </label>
		<div>
			<input id="email" name="email" class="element text medium" type="text" maxlength="255"  value="<?php echo $_REQUEST['email'];?>"/> 
		</div> 
		</li>

		<?php if($_REQUEST['email']=='' && $_REQUEST["action"] == 'login'){?>
		<p class="error">Please enter email</p>
		<?php ;}?>

		<?php if($_REQUEST['email']!='' & $wrong_email == 1){?>
		<p class="error">Entered email is wrong</p>
		<?php ;}?>

		<li id="li_4" >
		<label class="description" for="element_4">Zip code</label>
		<div>
			<input id="email" name="zip" class="element text medium" type="text" maxlength="5"  value="<?php echo $_REQUEST['zip'];?>"/> 
		</div> 
		</li>

		<?php if($_REQUEST['zip']=='' && $_REQUEST["action"] == 'login'){?>
		<p class="error">Please enter zip</p>
		<?php ;}?>

		<?php if($_REQUEST['zip']!='' & $wrong_zip == 1){?>
		<p class="error">Entered zip is wrong</p>
		<?php ;}?>


		<li id="li_1" >
		<label class="description">Verification code</label>
		<div>
			<input name="verif_box" class="element text medium"  type="text" size="5" maxlength="5"><img src="verificationimage.php?<?php echo rand(0,99999);?>" width="64" height="24" align="absbottom" />

		</div> 
		</li>


		<?php if($_REQUEST['verif_box']=='' && $_REQUEST["action"] == 'login'){?>
		<p class="error">Please enter verification code</p>
		<?php ;}?>

		<?php if($_REQUEST['verif_box']!='' & $wrong_ver == 1){?>
		<p class="error">Wrong verification code</p>
		<?php ;}?>

			* all fields are required

			<li class="buttons">
			    <input type="hidden" name="action" value="login" />
			    
				<input id="saveForm" class="button_text" type="submit" name="submit" value="Make/Cancel an appointment" />
		</li>
			</ul>
		</form>	