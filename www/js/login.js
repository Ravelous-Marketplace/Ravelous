$(document).ready(function(){
	$("button.login_button").click(function(){
		$(this).addClass('active');
		$("button.register_button").removeClass('active');
		$(".login").show();
		$(".register").hide();
		$("#submitForm").html('Login');
		$("input[name=type]").val('Login');
	});
	$("button.register_button").click(function(){
		$(this).addClass('active');
		$("button.login_button").removeClass('active');
		$(".login").hide();
		$(".register").show();
		$("#submitForm").html('Register');
		$("input[name=type]").val('Register');
	});
	$("input[name=register_email]").change(function(){checkMail()});
	$("input[name=register_email]").keyup(function(){checkMail()});

	$("input[name=register_username]").change(function(){checkName()});
	$("input[name=register_username]").keyup(function(){checkName()});

	$("input[name=register_password], input[name=register_passwordrepeat]").change(function(){checkPassword()});
	$("input[name=register_password], input[name=register_passwordrepeat]").keyup(function(){checkPassword()});

	$("button#submitForm").click(function(){submitForm()});
});

function checkMail() {
	var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	if (!regex.test($("input[name=register_email]").val())) {
		$("input[name=register_email]").prev().html('E-Mail: <span style="color: red">not valid</span>');
		$("input[name=register_email]").addClass('incorrectField');
		return false;
	} else {
		$("input[name=register_email]").prev().html('E-Mail:');
		$("input[name=register_email]").removeClass('incorrectField');
		return true;
	}
}

function checkName() {
	if ($("input[name=register_username]").val().length < 5) {
		$("input[name=register_username]").prev().html('Username: <span style="color:red;">min 6 characters</span>')
		$("input[name=register_username]").addClass('incorrectField');
		return false;
	}
	else {
		$("input[name=register_username]").prev().html('Username:')
		$("input[name=register_username]").removeClass('incorrectField');
	}

	var regex = /[a-zA-Z0-9\-'ÆÐƎƏƐƔĲŊŒẞÞǷȜæðǝəɛɣĳŋœĸſßþƿȝĄƁÇĐƊĘĦĮƘŁØƠŞȘŢȚŦŲƯY̨Ƴąɓçđɗęħįƙłøơşșţțŧųưy̨ƴÁÀÂÄǍĂĀÃÅǺĄÆǼǢƁĆĊĈČÇĎḌĐƊÐÉÈĖÊËĚĔĒĘẸƎƏƐĠĜǦĞĢƔáàâäǎăāãåǻąæǽǣɓćċĉčçďḍđɗðéèėêëěĕēęẹǝəɛġĝǧğģɣĤḤĦIÍÌİÎÏǏĬĪĨĮỊĲĴĶƘĹĻŁĽĿʼNŃN̈ŇÑŅŊÓÒÔÖǑŎŌÕŐỌØǾƠŒĥḥħıíìiîïǐĭīĩįịĳĵķƙĸĺļłľŀŉńn̈ňñņŋóòôöǒŏōõőọøǿơœŔŘŖŚŜŠŞȘṢẞŤŢṬŦÞÚÙÛÜǓŬŪŨŰŮŲỤƯẂẀŴẄǷÝỲŶŸȲỸƳŹŻŽẒŕřŗſśŝšşșṣßťţṭŧþúùûüǔŭūũűůųụưẃẁŵẅƿýỳŷÿȳỹƴźżžẓ]$/;
	if (!regex.test($("input[name=register_username]").val())) {
		$("input[name=register_username]").prev().html('Username: <span style="color: red">invalid characters</span>');
		$("input[name=register_username]").addClass('incorrectField');
		return false;
	} else {
		$("input[name=register_username]").prev().html('Username:');
		$("input[name=register_username]").removeClass('incorrectField');
		return true;
	}
}

function checkPassword() {
	passwordStrength();
	// Check if password has at least 8 characters
	if ($("input[name=register_password]").val().length < 7) {
		$("input[name=register_password]").prev().html('Password: <span style="color:red;">min 8 characters</span>')
		$("input[name=register_password]").addClass('incorrectField');
		return false;
	} else {
		$("input[name=register_password]").prev().html('Password:');
		$("input[name=register_password]").removeClass('incorrectField');
	}

	if ($("input[name=register_password]").val() !== $("input[name=register_passwordrepeat]").val()) {
		$("input[name=register_passwordrepeat]").prev().html('Repeat password: <span style="color:red;">not matching</span>')
		$("input[name=register_passwordrepeat]").addClass('incorrectField');
		return false;
	} else {
		$("input[name=register_passwordrepeat]").prev().html('Repeat password:')
		$("input[name=register_passwordrepeat]").removeClass('incorrectField');
		return true;
	}
}

function submitForm() {
	if ($("input[name=type]").val() == 'Register') {
		if (!checkMail()) {
			alert('Mail not valid');
			return false;
		} else if (!checkName()) {
			alert('Name not valid');
			return false;
		} else if (!checkPassword()) {
			alert('Password not valid or not matching');
			return false;
		}
	}
	$('#loginForm').submit();
}

function passwordStrength() {
	$score = 0;
	$total = 50;
	$pass = $("input[name=register_password]").val();

	if ($pass.length > 6) {
		$score += 5;
	}
	if ($pass.length > 10) {
		$score += 5;
	}
	if ($pass.length > 14) {
		$score += 4;
	}

	if ($pass.replace(/[^0-9]/g).length > 2) {
		$score += 5;
	}
	if ($pass.replace(/[^0-9]/g).length > 5) {
		$score += 5;
	}

	if ($pass.replace(/[^0-9]/g).length > 0) {
		$score += 2;
	}
	if ($pass.replace(/[^0-9]/g).length > 3) {
		$score += 5;
	}

	if ($pass.replace(/[^0-9a-bA-B]/g).length > 1) {
		$score += 3;
	}
	if ($pass.replace(/[^0-9a-bA-B]/g).length > 3) {
		$score += 5;
	}

	$percentage = $score / $total * 100;
	$("#strength > div").css('width', $percentage + "%");
}