<?php

include("functions.php");

//helo

//contenteditable
//for pasting


//with submit

function RandomButton()
{
	$Options = array("Sex", "Bubbles", "Friendship", "Drugs", "Happy", "Sabmit", "Danger", "Death", "Salvation");
	shuffle($Options);

	echo $Options[0];
}

if($_POST)
{
	$HTML = is_string($_POST['html'] ?? null) ? $_POST['html'] : '';
	$Site = getenv('SITE_URL');

	// get images

	$Length = strlen($HTML);
	$Hash = md5($HTML);

	$Plural = ($Length != 1) ? "s" : "";

	$Stuff = array(	"Your post is $Length character{$Plural} long.",
					"With an MD5 hash of $Hash",
					"",
					"<a href='https://wiki.wetfish.net '>Embed Code</a>",
					"<input type='text' value='embed[https://$Site/$Hash]' size='60' />",
					"<a href='https://$Site$Hash'>Dangerfish Page</a>",
					"<input type='text' value='https://$Site/$Hash' size='60' />");

	foreach($Stuff as $Line)
	{
		echo "<b>$Line</b><br />";
	}

	// Insert to database

	$Query = Query("Select `Content` from `Posts` where `Hash`=?", $Hash);
	list($Content) = $Query->fetch_row();

	if(empty($Content))
	{
		$Author = $_SERVER['REMOTE_ADDR'];
		$Content = $HTML;
		$Time = time();

		Query("Insert into `Posts` values (?, ?, ?, ?)", $Hash, $Author, $Content, $Time);
	}

	//echo stripslashes($_POST['html']);
}
else
{
	$Hash = trim($_GET['hash'] ?? '', '/');
	$Page = explode('/', $Hash);
	$Content = "";

	switch($Page[0])
	{
		case "recent":
			$Query = Query("Select * from `Posts` order by `Time` desc limit 50");
			while($Row = $Query->fetch_row())
			{
				list($QueryHash, $QueryAuthor, $QueryContent, $QueryTime) = $Row;
				$Length = strlen($QueryContent);

				date_default_timezone_set('America/New_York');
				$Time = date("F j\, Y G:i:s", $QueryTime)." EST";

				$Content .= "<tr>
								<td><b><a href='/$QueryHash'>$QueryHash</a></b></td>
								<td><b><a href='/author/$QueryAuthor'>$QueryAuthor</b></td>
								<td><b>$Length</b></td>
								<td><b>$Time</b></td>
							</tr>";
			}

			$Content = "<table>
							<tr>
								<td><b>Hash</b></td>
								<td><b>Author</b></td>
								<td><b>Length</b></td>
								<td><b>Time</b></td>
							</tr>
							$Content
						</table>";

			$Editable = "contenteditable=false";
		break;

		case "author":
			$Author = $Page[1] ?? '';

			$Query = Query("Select * from `Posts` where `Author`=? order by `Time` desc limit 50", $Author);
			while($Row = $Query->fetch_row())
			{
				list($QueryHash, $QueryAuthor, $QueryContent, $QueryTime) = $Row;
				$Length = strlen($QueryContent);

				date_default_timezone_set('America/New_York');
				$Time = date("F j\, Y G:i:s", $QueryTime)." EST";

				$Content .= "<tr>
								<td><b><a href='/$QueryHash'>$QueryHash</a></b></td>
								<td><b><a href='/author/$QueryAuthor'>$QueryAuthor</b></td>
								<td><b>$Length</b></td>
								<td><b>$Time</b></td>
							</tr>";
			}

			$Content = "<table>
							<tr>
								<td><b>Hash</b></td>
								<td><b>Author</b></td>
								<td><b>Length</b></td>
								<td><b>Time</b></td>
							</tr>
							$Content
						</table>";

			$Editable = "contenteditable=false";
		break;

		default:
			$Query = Query("Select `Content` from `Posts` where `Hash`=?", $Hash);
			list($Content) = $Query->fetch_row();

			if($Content)
				$Content = stripslashes($Content);
			else
			{
				$Content = "Are you ready to get <b>DANGEROUS?</b><br /><br />
					This is a contenteditable div, you can paste all sorts of fun stuff into it, complete with URL for <a href='https://wiki.wetfish.net/'>sharing</a>.<br /><br />
					<span style='font-size:16pt; font-weight:bold;'>But what kind of fun stuff?</span><br />
					HTML formatting/images from other pages,<br />
					Formatting from rich text editors,<br />
					Malicious javascript,<br />
					AND SO MUCH MORE!!!!!!!!";
			}

			$Editable = "contenteditable=true";
		break;
	}

?>

<html>
	<head>
		<title>dangerfish</title>

		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link href="/jetpack.css" rel="stylesheet" type="text/css" />
		<script type='text/javascript' src='/node_modules/wetfish-basic/dist/basic.js'></script>
		<script type='text/javascript' src='/node_modules/dragondrop/dragondrop.js'></script>

		<script>

			var Jetpack = new Object;

			Jetpack.Window = function(Input)
			{
				var Selector = "#jetpack[window='"+Input.name+"']";

				if($(Selector).length) // If the window has already been created.
				{
					$(Selector + ' > div#title').html(Input.title);
					$(Selector + ' > div#content').html(Input.contents);
				}
				else // Create new window.
				{
					Jetpack.title = "<div id='title'>" + Input.title + "</div>";
					Jetpack.contents = "<div id='content'>" + Input.contents + "</div>";

					// Added as an element, so the rest of the page is left as it is
					Jetpack.window = document.createElement('div');
					Jetpack.window.id = 'jetpack';
					Jetpack.window.setAttribute('window', Input.name);
					Jetpack.window.innerHTML = Jetpack.title + Jetpack.contents;

					document.body.appendChild(Jetpack.window);

					// spawn on active part of the screen

					if('resize' in Input)
					{
						$(Selector).addClass('resizable');

						// Leave the bottom right corner to the browser's resize handle,
						// before dragondrop starts dragging the window from there
						Jetpack.window.addEventListener('mousedown', function(Event)
						{
							var Box = this.getBoundingClientRect();

							if(Box.right - Event.clientX <= 16 && Box.bottom - Event.clientY <= 16)
								Event.stopImmediatePropagation();
						}, true);
					}

					// Fades in, and has to be showing before dragondrop finds its position
					$(Selector).addClass('open');
					$(Selector).dragondrop();
				}
			}

			// Runs the scripts in an element's new HTML, then removes them, like jQuery's .html() did
			function RunScripts(Element)
			{
				Element.querySelectorAll('script').forEach(function(Old)
				{
					var Script = document.createElement('script');

					for(var i = 0; i < Old.attributes.length; i++)
						Script.setAttribute(Old.attributes[i].name, Old.attributes[i].value);

					Script.text = Old.text;
					Old.remove();

					document.head.appendChild(Script);
					Script.remove();
				});
			}

			function Submit()
			{
				var Post = new URLSearchParams({html: $('#dangerbox').el[0].innerHTML});

				fetch('/index.php', {method: 'POST', body: Post}).then(function(Response)
				{
					return Response.text();
				}).then(function(Happy)
				{
					Jetpack.Window({
								name: 'Just A Window',
								title: '',
								contents: Happy,
								resize: true
							 });
				});
			}

			function Render()
			{
				var HTML = $('#dangerbox').el[0].innerHTML;

				HTML = HTML.replace(/&amp;/g, '&');
				HTML = HTML.replace(/&lt;/g, '<');
				HTML = HTML.replace(/&gt;/g, '>');

				$('#dangerbox').html(HTML);
				RunScripts($('#dangerbox').el[0]);
			}

			function UnRender()
			{
				var HTML = $('#dangerbox').el[0].innerHTML;

				HTML = HTML.replace(/&/g, '&amp;');
				HTML = HTML.replace(/</g, '&lt;');
				HTML = HTML.replace(/>/g, '&gt;');

				$('#dangerbox').html(HTML);
			}

		</script>
	</head>

	<body>
		<a href='/' style='font-weight:bold'>What?</a> |
		<a href='/recent' style='font-weight:bold'>Recent Posts</a> |
		<a href='javascript:Render()' style='font-weight:bold'>Render HTML</a> |
		<a href='javascript:UnRender()' style='font-weight:bold'>UnRender HTML</a>

		<br /><br />

		<div id='dangerbox' <?php echo $Editable ?>>
			<?php echo $Content ?>
		</div>

		<br />

		<input type='button' value='<?php RandomButton(); ?>' onClick='Submit()' />
	</body>
</html>
<?php

}

// Yuck!

?>
