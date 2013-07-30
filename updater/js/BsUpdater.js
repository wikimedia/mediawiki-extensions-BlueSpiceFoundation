$(document).ready(function(){
	$("#updater").click(function(e){
			e.preventDefault();
			$("#updater_msg_details").text("");
			$("#updater_msg > span").css("display", 'none');
			$("#updater_msg").css('display', 'block');
			$("#updater_msg > img").css('display', 'block');
			$("#updater_msg_details").load("BsSetupUpdater.php", function(){
					$("#updater_msg > img").css('display', 'none');
					$("#updater_msg > span").css("display", 'block');
					$("#details").unbind('click');
					$("#details").click(function(e){
							e.preventDefault();
							$("#updater_msg_details").toggle();
					});
			});
	});
	$("#update_exe").click(function(e){
			e.preventDefault();
			$("#updater_msg_details").text("");
			$("#updater_msg > span").css("display", 'none');
			$("#updater_msg").css('display', 'block');
			$("#updater_msg > img").css('display', 'block');
			$("#updater_msg_details").load("BsSetupUpdater.php", function(){
					$("#updater_msg > img").css('display', 'none');
					$("#updater_msg > span").css("display", 'block');
					$("#details").unbind('click');
					$("#details").click(function(e){
							e.preventDefault();
							$("#updater_msg_details").toggle();
					});
			});
	});
});