<header class="header js-header" data-module="header" style="background-color: #0E0623;">
	<div class="row justify-content-center" style="align-items: center;">
		<div class="logo-wrapper">
			<a href="{{ route('app_dashboard') }}">
				<img src="{{ url('/images/ui/kolab-logo-black.png') }}" class="img-fluid logo is-theme-light" alt="kolab" width="110" height="36">
				<img src="{{ url('/images/ui/kolab-logo.png') }}" class="img-fluid logo is-theme-dark" alt="kolab" width="110" height="36">
			</a>
		</div>
		<div class="main-container">
			<div style="display: flex;
			align-items: center; position: relative;">
				<div class="actions-list js-toggle-wrapper">
					<button type="button" class="button has-plus js-toggle-button" style=" background: #2B1C56;
					border: 0;
					color: $white;
					position: relative;
					z-index: 5;
					width: 110px;
					height: 38px;
					display: flex;
					justify-content: space-between;
					align-items: center;">Créer <span style="width: 31px;
						height: 32px;
						background: #150C2D;
						border-radius: 3px;
						position: absolute;
						right: 0;
						margin-right: 6px;
						padding: 8px;
					"><img src="/images/plus.png" alt="" style="width:10px; height:10px;"></span></button>
					<div class="actions-list__buttons js-toggle-content" style="position: absolute;
					z-index: 1;
					width: 250px;
					top: 50px;
					left: 0;display:none;background: rgb(25, 15, 51); border-radius:10px; " >
						  <a href="javascript:;" class="button" v-on:click="Global._setAction('SET_PROJECT')"> <span class="icon icon-project"></span>Nouveau projet </a>
						  <a href="javascript:;" class="button" v-on:click="Global._setAction('SET_APPOINTMENT')"> <span class="icon icon-calendar"></span>Nouveau RDV</a>
						  <a href="javascript:;" class="button" v-on:click="Global._setAction('SET_TASK')"> <span class="icon icon-task"></span>Nouvelle tâche</a>
						  <a href="javascript:;" class="button" v-on:click="Global._setAction('SET_TALENT')"><span class="icon icon-talent"></span> Nouveau talent</a>
					</div>
				</div>
			<nav class="menu">
				<ul class="menu__wrapper">
					<li class="menu__item">
						<a href="{{ route('app_dashboard') }}" class="menu__link {{ Route::currentRouteName() == 'app_dashboard' ? 'is-active' : ''}}">
							Dashboard
						</a>
					</li>
					<li class="menu__item">
						<a href="{{ route('app_planning') }}" class="menu__link {{ Route::currentRouteName() == 'app_planning' ? 'is-active' : ''}}">
							Planning
						</a>
					</li>
					<li class="menu__item">
						<a href="{{ route('app_projects') }}" class="menu__link {{ Route::currentRouteName() == 'app_projects' ? 'is-active' : ''}}">
							Projets
						</a>
					</li>
                    <li class="menu__item">
                        <a href="{{ route('app_talents') }}" class="menu__link {{ Route::currentRouteName() == 'app_talents' ? 'is-active' : ''}}">
                            Talents
                        </a>
                    </li>
                    <li class="menu__item">
                        <a href="{{ route('app_explorer') }}" class="menu__link {{ str_contains(Route::currentRouteName(),'app_explorer') ? 'is-active' : ''}}">
                            Explorer
                        </a>
                    </li>
				</ul>
			</nav>
			<div style="display: flex;
			justify-content: space-between;">

				<button type="button" class="button " style="width: 35px;
				height: 35px;
				margin-right: 45px;background: #48367C;justify-content: center;
    display: flex;align-items: center;"> <img src="/images/Vector.png" alt="" style="width:11px; height:14px; "></button>
				<div class="account__header js-toggle-wrapper">
					<div style="width: 35px; height: 35px;margin-right: 45px;">
						<div class="account-menu__info">
							<!--<p><strong>{{ Auth::user()->name }}</strong></p>-->
							<button type="button" class="button js-toggle-button" style="width: 35px;
							height: 35px;
							margin-right: 45px;background: #2B1C56;justify-content: center;
    display: flex;align-items: center;" ><img src="{{ url('/images/Group.png') }}" alt="" style="width:13px; height:13px;"></button>
						</div>
					</div>

					<div class="account-menu__panel js-toggle-content">
						<!--<a href="{{ url('profile') }}" class="account-menu__link">Profile</a>-->
						<button type="button" class="account-menu__link" v-on:click="Global._showAccount({{ Auth::user() }})">Paramètres</button>
						<!--<button type="button" class="account-menu__link js-dark-mode-button">Dark mode <span class="switch-button"><span class="switch-button__btn"></span></span></button>-->
						<a href="{{ url('logout') }}" class="logout-link account-menu__link">Deconnexion</a>


					</div>
				</div>
				<!--<button type="button" class="button is-gradient has-arrow" v-on:click="toggle()" style="width: 35px; height: 35px;margin-right: 45px;"> <img src="/images/Group.png" alt="" style="width:10px; height:10px; "></button>-->
			</div>
		</div>
	</div>

		<!--<div class="account__header js-toggle-wrapper">
			<div class="account-menu__content">
				<div class="account-menu__info">
					<p><strong>{{ Auth::user()->name }}</strong></p>
				</div>
			</div>

			<div class="account-menu__panel js-toggle-content">
				<button type="button" class="account-menu__link" v-on:click="Global._showAccount({{ Auth::user() }})">Paramètres</button>
				<button type="button" class="account-menu__link js-dark-mode-button">Dark mode <span class="switch-button"><span class="switch-button__btn"></span></span></button>
				<a href="{{ url('logout') }}" class="logout-link account-menu__link">Deconnexion</a>
			</div>
		</div>-->
		<div style="position: absolute;
		right: 0;
		width: 200px;
		height: 100%;
		display: flex;
		align-items: center;
		justify-content: center;
		border-left: 1px solid #39276a">
			<img src="/upload/avatars/{{Auth::user()->avatar}}" class="img-fluid" alt="" width="50" height="50" style="border-radius: 50%;">
		</div>
	</div>
</header>

