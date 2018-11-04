<html>
<head>
    <title>{$title}</title>
</head>
<body>
<h2><a href="/">{$title}</a></h2>
<form action="/" method="post">
    <label for="full_url">Enter full link: </label>
    <input type="text" style="width: 500px" name="full_url" id="full_url">
    <input type="submit" value="Get short link"/>
    <p style="color: red">{$error}</p>
    {if $shortLink != ''}
        <p style="color: green">
           Your short link is <a href="{$shortLink}" target="_blank">{$shortLink}</a>
        </p>
    {/if}
    <br>
    <a href="/all-links" target="_blank"> All links</a>
</form>
</body>
</html>