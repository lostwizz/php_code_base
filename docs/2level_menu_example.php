
/*

<ul>
	<li><a href="./index.php?MENU_SELECT=home...">Home</a></li>
	<li><a href="./index.php?MENU_SELECT=news...">News</a></li>
	<li class="dropdown">
		<a href="javascript:void(0)" class="dropbtn">Dropdown</a>
		<div class="dropdown-content">
			<a href="./index.php?MENU_SELECT=link.one..">Link 1</a>
			<a href="./index.php?MENU_SELECT=link.two..">Link 2</a>
			<a href="./index.php?MENU_SELECT=link.three..">Link 3</a>
		</div>
	</li>
	<?php
		if ( Settings::GetRunTime('userPermissionsController')->hasRole('DBA') ){
?>	<li class="dropdown">
		<a href="javascript:void(0)" class="dropbtn">DBA</a>
		<div class="dropdown-content">
			<a href="./index.php?MENU_SELECT=dba.one..">DBA1</a>
			<a href="./index.php?MENU_SELECT=dba.two..">DBA2</a>
			<a href="./index.php?MENU_SELECT=dba.three..">DBA3</a>
		</div>
	</li>
<?php
		}
	?>
	<li class="dropdown">
		<a href="javascript:void(0)" class="dropbtn">Help</a>
		<div class="dropdown-content">
			<a href="./index.php?MENU_SELECT=help.one..">About</a>
			<a href="./index.php?MENU_SELECT=help.two..">Version</a>
			<a href="./index.php?MENU_SELECT=help.three..">Help</a>
		</div>
	</li>
</ul>

<h3>Dropdown Menu inside a Navigation Bar</h3>
<p>Hover over the "Dropdown" link to see the dropdown menu.</p>

<?php


	}

}


*/
