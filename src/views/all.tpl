<html>
<head>
    <title>{$title}</title>
</head>
<body>
<h2><a href="/">{$title}</a></h2>
<form action="/all-links" method="post">

    Page {$page} from {$countPages}

    <table border="1" cellpadding="5">

        <thead>
        <th>Id</th>
        <th>Short url</th>
        <th>Full url</th>
        <th>Created</th>
        <th>Action</th>
        </thead>
        <tbody>
        {foreach from=$urls item=item}
            <tr>
                <td>{$item.id}</td>
                <td><a href="{$item.short}?{$time}" target="_blank">{$item.short}</a></td>
                <td>{$item.url}</td>
                <td>{$item.updated_at}</td>
                <td>
                    <button type="submit" name="delete" value="{$item.id}">Delete</button>
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
    <br><br>
    {if $page > 1}
        <a href="/page/{$page-2}"> << Previous </a>
    {/if}

    {if $page > 1 && $page < $countPages}
        <span> ::: </span>
    {/if}

    {if $page < $countPages}
        <a href="/page/{$page}"> Next >> </a>
    {/if}


</form>
</body>
</html>