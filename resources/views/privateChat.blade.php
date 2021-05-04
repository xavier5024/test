<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=0.5">
		<!-- CSRF Token -->
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<!-- Scripts -->
		<script src="{{ asset('js/app.js') }}" defer></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script src="{{ mix('js/broadcast.js') }}"></script>
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
		<link href="{{ asset('css/broadcast.css') }}" rel="stylesheet">

        <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>        
		<script>
            const curruser = <?=$user?>;   
			const privateTo = <?=$privateTo?>;
			const channelname = "<?=$channelname?>";
            $( document ).ready(function(){
				$("#chatbox").scrollTop($("#chatbox")[0].scrollHeight);
                window.Echo.join('monitoring.<?=$channelname?>')
                .here((users) => {
					console.log(users);
					let userhtml = '<li class="active"';
					userhtml += 'id="userlist_'+curruser.id+'" onClick="privateChat('+curruser.id+');">';
					userhtml += '<div class="d-flex bd-highlight"><div class="img_cont"><img src="'+curruser.profile_src+'" class="rounded-circle user_img"><span id="online_icon_'+curruser.id+'" class="online_icon"></span></div><div class="user_info"><span>'+curruser.name+'</span>';
					userhtml += '<span class="whisper_cnt whisper">';
					userhtml += "<span id='whisper_cnt_"+curruser.id+"' class='whisper_cnt whisper'></span>";
					userhtml += '</span>';
					userhtml += '<p>'+curruser.name+' is online</p></div></div></li>';

					userhtml += '<li class=""';
					userhtml += 'id="userlist_'+privateTo.id+'" onClick="privateChat('+privateTo.id+');">';
					userhtml += '<div class="d-flex bd-highlight"><div class="img_cont"><img src="'+privateTo.profile_src+'" class="rounded-circle user_img"><span id="online_icon_'+privateTo.id+'" class="online_icon ';
					let isonline = false;
					for(var idx=0; idx<users.length; idx++){
						userhtml += "offline";
					}
					userhtml += '"></span></div><div class="user_info"><span>'+privateTo.name+'</span>';
					userhtml += '<span class="whisper_cnt whisper">';
					userhtml += "<span id='whisper_cnt_"+privateTo.id+"' class='whisper_cnt whisper'></span>";
					userhtml += '</span>';
					userhtml += '<p>'+privateTo.name+' is online</p></div></div></li>';					

					$("#userlist").html(userhtml);
                })
                .joining((user) => {
                    //console.log(user);
                    $("#online_icon_"+user.id).removeClass("offline");
                })
                .listen('Hello', (data) => {
                    let wheremy = (curruser.id == data.user.id) ? "start" : "end";
                    let ifmessage = (curruser.id == data.user.id) ? "msg_cotainer" : "msg_cotainer_send";
                    let message_html = '<div class="d-flex justify-content-'+wheremy+' mb-4"><div class="img_cont_msg"><img src="'+data.user.profile_src+'" class="rounded-circle user_img_msg"></div><div class="'+ifmessage+'">'+data.data.message+'<span class="msg_time">지금</span></div></div>';
                    $("#chatbox").append(message_html);
                    $("#chatbox").scrollTop($("#chatbox")[0].scrollHeight);
                    $(".current_my").remove();
					let send_data = "privateTo="+privateTo.id;
					$.ajax({ 
						type: "POST", 
						url: "/privateRead", 
						data:  send_data,
						dataType: 'html',
						success: function (data) {
							console.log(data);
						}
					});					
                })
                .leaving((user) => {
                    //console.log(user.name);
                    $("#online_icon_"+user.id).addClass("offline");
                });

            });
			function go_broadcasting(){
                $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
				var message = $("#message").val();

                let message_html = '<div class="d-flex justify-content-start mb-4 current_my"><div class="img_cont_msg"><img src="'+curruser.profile_src+'" class="rounded-circle user_img_msg"></div><div class="msg_cotainer">'+message+'<span class="msg_time">지금</span></div></div>';
                $("#chatbox").append(message_html);
				$("#message").val("");

				var send_data = "message="+message+"&channelname="+channelname+"&privateTo="+privateTo.id;
				$.ajax({ 
					type: "POST", 
					url: "/privateBroadcast", 
					data:  send_data,
					dataType: 'html',
					success: function (data) {
						console.log(data);
					}
				});
			}
		</script>
		<title>Chat</title>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css">
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.js"></script>
		<script>
		$(document).ready(function(){
			$('#action_menu_btn').click(function(){
				$('.action_menu').toggle();
			});
		});
		</script>
	</head>
	<!--Coded With Love By Mutiullah Samim-->
	<body> 
		<div class="dropdown-menu dropdown-menu-right show" aria-labelledby="navbarDropdown" style="top:0px">
			<a class="dropdown-item" href="{{ route('logout') }}"
				onclick="event.preventDefault();
								document.getElementById('logout-form').submit();">
				{{ __('Logout') }}
			</a>

			<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
				@csrf
			</form>
		</div>
		<div class="container-fluid h-100">
			<div class="row justify-content-center h-100">
				<div class="col-md-4 col-xl-3 chat"><div class="card mb-sm-3 mb-md-0 contacts_card">
					<div class="card-header">
						<div class="input-group">
							<input type="text" placeholder="Search..." name="" class="form-control search">
							<div class="input-group-prepend">
								<span class="input-group-text search_btn"><i class="fas fa-search"></i></span>
							</div>
						</div>
					</div>
					<div class="card-body contacts_body">
						<ui class="contacts" id="userlist">

						</ui>
					</div>
					<div class="card-footer"></div>
				</div></div>
				<div class="col-md-8 col-xl-6 chat">
					<div class="card">
						<div class="card-header msg_head">
							<div class="d-flex bd-highlight">
								<div class="img_cont">
									<img src="<?=$user["profile_src"]?>" class="rounded-circle user_img">
									<span class="online_icon"></span>
								</div>
								<div class="user_info">
									<span><?=$privateTo->name?>와의 대화</span>
									<p>Messages</p>
								</div>
								<div class="video_cam">
									<span><i class="fas fa-video"></i></span>
									<span><i class="fas fa-phone"></i></span>
								</div>
							</div>
							<span id="action_menu_btn"><i class="fas fa-ellipsis-v"></i></span>
							<div class="action_menu">
								<ul>
									<li><i class="fas fa-user-circle"></i> View profile</li>
									<li><i class="fas fa-users"></i> Add to close friends</li>
									<li><i class="fas fa-plus"></i> Add to group</li>
									<li><i class="fas fa-ban"></i> Block</li>
								</ul>
							</div>
						</div>
						<div class="card-body msg_card_body" id="chatbox">
						<?php
						if($chatting){
							foreach($chatting as $val){
								$val->data = json_decode($val->data);
								$wheremy = ($val->notifiable_id != $user->id) ? "start" : "end";
								$ifmessage = ($val->notifiable_id != $user->id) ? "msg_cotainer" : "msg_cotainer_send";
								$profile_src = ($val->notifiable_id != $user->id) ? $user->profile_src : $privateTo->profile_src;
								echo '<div class="d-flex justify-content-'.$wheremy.' mb-4"><div class="img_cont_msg"><img src="'.$profile_src.'" class="rounded-circle user_img_msg"></div><div class="'.$ifmessage.'">'.$val->data->message.'<span class="msg_time">'.$val->created_at.'</span></div></div>';
							}
						}
						?>
						</div>
						<div class="card-footer">
							<div class="input-group">
								<div class="input-group-append">
									<span class="input-group-text attach_btn"><i class="fas fa-paperclip"></i></span>
								</div>
								<textarea id="message" name="" class="form-control type_msg" placeholder="Type your message..." onkeyup="javascript:if(event.keyCode==13)go_broadcasting()"></textarea>
								<div class="input-group-append">
									<span class="input-group-text send_btn" onClick="go_broadcasting()"><i class="fas fa-location-arrow"></i></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>